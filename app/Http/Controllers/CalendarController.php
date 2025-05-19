<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CalendarDay;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CalendarController extends Controller
{
    // Метод для загрузки данных из Excel-файла и сохранения в базу данных
    public function upload(Request $request)
    {
        // Валидация: файл обязателен и должен быть формата .xlsx
        $request->validate([
            'file' => 'required|file|mimes:xlsx'
        ]);

        // Получение данных из Excel (в виде массива)
        $data = Excel::toArray([], $request->file('file'));
        $rows = $data[0]; // Предполагаем, что нас интересует только первый лист

        // Оборачиваем в транзакцию, чтобы при ошибке откатить все изменения
        DB::transaction(function () use ($rows) {
            foreach ($rows as $index => $row) {
                if ($index === 0) continue; // Пропускаем заголовок таблицы

                // Обновляем или создаем запись по дате
                CalendarDay::updateOrCreate([
                    'event_date' => $row[0], // дата события
                ], [
                    'name' => $row[1] ?? null, // название события 
                    'is_working_weekend' => isset($row[2]) && trim($row[2]) !== '', // является ли это рабочим выходным
                ]);
            }
        });

        return response()->json(['message' => 'Данные загружены']);
    }

    // Метод для вычисления количества рабочих дней между двумя датами
    public function calculate(Request $request)
    {
        // Преобразуем входные даты в объекты Carbon
        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);

        // Получаем праздничные и рабочие выходные из базы
        $holidays = CalendarDay::where('is_working_weekend', false)->pluck('event_date')->toArray();
        $weekendWorkdays = CalendarDay::where('is_working_weekend', true)->pluck('event_date')->toArray();

        $workdays = 0;

        // Перебираем каждую дату от начала до конца
        for ($date = $start; $date <= $end; $date->addDay()) {
            $isWeekend = in_array($date->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY]);
            $dateStr = $date->toDateString(); // Приводим дату к строке для сравнения

            // Учитываем:
            // - Обычные будни, не попадающие в праздники
            // - Рабочие выходные
            if (!$isWeekend && !in_array($dateStr, $holidays)) {
                $workdays++;
            } elseif ($isWeekend && in_array($dateStr, $weekendWorkdays)) {
                $workdays++;
            }
        }

        return response()->json(['workdays' => $workdays]);
    }
}
