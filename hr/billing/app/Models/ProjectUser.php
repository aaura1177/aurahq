<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class ProjectUser extends Model
{
    //

    use HasFactory;

    protected $table = 'project_user';

    protected $fillable = [
        'user_id',
        'project_id', 
        'start_time',
        'end_time', 
        'total_working_time',
        'paid_hours',
        'hourly_rate',
        'is_fully_paid',
        'total_amount',
        'status',
        'per_minute_rate',
        'pending_amount',
        'pending_hours',
        'is_fully_paid',
        'total_paid_amount',
        'created_at'
    ];


    public function calculateWorkingTime()
    {
        if ($this->start_time && $this->end_time) {
            $startTime = Carbon::parse($this->start_time);
            $endTime = Carbon::parse($this->end_time);
            return $endTime->diffInMinutes($startTime); 
        }
        return 0;
    }

    public function adminProject()
    {
        return $this->belongsTo(AdminProject::class, 'project_id');
    }


    // ProjectUser.php
public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}

public function project()
{
    return $this->belongsTo(AdminProject::class,'project_id');
}


public function users(){
        return $this->belongsTo(User::class, 'user_id');
}


public function userPayment()
    {
        return $this->hasMany(UserPayment::class, 'project_id', 'project_id');
    }


    public function attendances()
{
    return $this->hasMany(ProjectAttandanc::class, 'project_id', 'project_id')
        ->where('user_id', $this->user_id);
}
}
