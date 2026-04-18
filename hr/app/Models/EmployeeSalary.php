<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSalary extends Model
{
    use HasFactory;

    protected $table = 'employee_salaries'; 

    protected $fillable = [
        'user_id',
        'employee_id',
        'basic_salary',
        'hra',
        'da',
        'ta',
        'other_allowance',
        'workform_salary',
        'attendance_salay',
        'company_working_hours',
        'home_working_hours',
        'weekoffsalary',
        'holiday_salary',
        'deductions',
        'net_salary',
        'leave',
        'leave_bal',
        'salary_month',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
