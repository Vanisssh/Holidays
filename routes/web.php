<?php
use App\Http\Controllers\CalendarController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('app');
});
Route::post('/upload-calendar', [CalendarController::class, 'upload']);
Route::post('/calculate', [CalendarController::class, 'calculate']);