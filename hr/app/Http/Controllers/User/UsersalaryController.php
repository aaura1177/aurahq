<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmployeeSalary;

class UsersalaryController extends Controller
{
public function index(){
    $userId = auth('employee')->id();
     $employeesalary = EmployeeSalary::with('employee')->where('employee_id',$userId)->get();
    return view('user.salary.index',compact('employeesalary'));
}
}
