<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RevenueTarget extends Model
{
    protected $guarded = [];

    protected $casts = [
        'month' => 'date',
        'target_amount' => 'decimal:2',
    ];
}
