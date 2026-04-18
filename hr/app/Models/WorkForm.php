<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkForm extends Model
{
    
     protected $table = 'work_forms'; 

    protected $fillable = [
        'user_id',
        'work_date',
        'start_time',
        'end_time',
        'working_hours',
        'reason',
        'location',
        'status',
    ];

    // Optional: Define accessors/relationships if needed
    public function user()
    {
        return $this->belongsTo(Employee::class, 'user_id');
    }
}
