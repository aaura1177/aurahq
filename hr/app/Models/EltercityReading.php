<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EltercityReading extends Model
{
        protected $table = 'eltercity_readings'; 
    protected $fillable = ['time_slot', 'reading', 'date', 'screenshot'];
}
