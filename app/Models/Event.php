<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    // تحديد الحقول القابلة للتعبئة
    protected $fillable = [
        'title',
        'description',
        'event_date'
    ];

    protected $casts = [
        'event_date' => 'date'
    ];
}
