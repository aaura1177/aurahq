<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class LeaveRequest extends Model
{ 
    
    use HasFactory;
    protected $table = 'leave_requests'; 

    protected $fillable = [
        
        'employee_id',
        'leave_type_id',
        'start_at',
        'end_at',
        'status',
        'reason',
        'applied_on',
        'approved_by',
        'rejection_reason_tex',
       
        
    ];


    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id','id');
    }


    public function user()
{
    return $this->belongsTo(User::class, 'approved_by','id');
}

}
