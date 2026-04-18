<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternshipAttendance extends Model
{
    protected $table = 'internship_attendances'; 

    protected $fillable = [
        'user_id',
        'employee_id',
        'date',
        'check_in_time',
        'check_out_time',
        'working_hours',
        'status',
        'remarks',
    ];
      public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
      public function user()
    {
        return $this->belongsTo(Employee::class, 'user_id');
    }
}
