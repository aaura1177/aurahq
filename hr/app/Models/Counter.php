<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    use HasFactory;

    protected $table = 'counters'; 

    protected $fillable = [
        'counter_name',
        'prefix',
        'count',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'count' => 'integer',
    ];
}
