<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;



class Task extends Model
{
    use HasFactory, SoftDeletes;

    

    protected $fillable = [
        'name',
        'project_id',
        'user_id',
        'employee_id',
        'start_date',
        'due_date',
        'complete_date',
        'status',
        'employee_status',
        'priority',
        'estimated_date',
        'actual_hours',
        'remark',
        'description',
    ];


    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }







}
