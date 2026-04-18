<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    // Define the table associated with the model (if it differs from the default "attendances")
    protected $table = 'attendance';

    // The attributes that are mass assignable
    protected $fillable = [
        'employee_id',
        'date',
        'shift',
        'check_in_time',
        'check_out_time',
        'working_hours',
        'overtime_hours',
        'status',   
        'earned_salary',   
        'leave_type',
        'device_id',
        'geo_location',
        'remarks',
        'work_from',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

}
