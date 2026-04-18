<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectAttandanc extends Model
{
    //
        protected $table = 'project_attendance';

    // Fillable columns for mass assignment
    protected $fillable = [
        'user_id',
        'project_id',
        'h_rate',
        'm_rate',
        'start_time',
        'end_time',
        'total_minutes',
        'total_amount',
        'date',
        'status',
    ];

    // If you want date casting for carbon operations
    protected $dates = ['date', 'start_time', 'end_time'];

    // Optional relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(AdminProject::class);
    }
}
