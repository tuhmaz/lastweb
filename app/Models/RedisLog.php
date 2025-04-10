<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RedisLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'ttl',
        'action',
        'user',
    ];
}
