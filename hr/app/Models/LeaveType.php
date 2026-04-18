<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class LeaveType extends Model
{
    //

    use HasFactory;
    protected $table = 'leave_types'; 

    protected $fillable = [        
        'leave_name ',
        'description',
        'max_days',
    ];


  
}
