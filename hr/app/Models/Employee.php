<?php   
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Employee extends Authenticatable
  
{
    use HasFactory;
 
   

    protected $fillable = [
        'user_id', 'email', 'password', 'emp_id', 'name', 'mobile',
        'date_of_joining', 'department_id', 'position', 'salary', 'status',
        'employee_type', 'login_attempts', 'account_locked', 'annual_bonus',
        'is_on_leave', 'notice_period', 'work_time', 'work_from_home', 'address',
        'emergency_contact_number', 'father_name', 'mother_name', 'bank_name',
        'bank_account_number', 'ifsc_code','increment_date', 'date_of_birth', 'gender', 'zipcode','image','monthly_leave',
        'resume', 'working_hours', 'biometric_id', 'deleted_at', 'created_at', 'updated_at'
    ];

    protected $hidden = ['password'];

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'employee_id');
    }
    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }
}
