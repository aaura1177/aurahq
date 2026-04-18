<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'description', 'user_id', 'total_employee'];


    public function employees()
{
    return $this->hasMany(Employee::class, 'department_id', 'id');
}


}
