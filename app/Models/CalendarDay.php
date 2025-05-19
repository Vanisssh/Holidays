<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarDay extends Model
{
    protected $fillable = [
        'event_date',
        'name',
        'is_working_weekend',
    ];
}
