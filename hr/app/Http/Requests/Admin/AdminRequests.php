<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class AdminRequests extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {


        $routePath = str_replace('admin/', '', $this->path());

        if ($this->isMethod('post') && $routePath === 'register') {
            return [
                'username' => 'required|string|max:99',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6|max:50'
            ];
        }
        if ($this->isMethod('post') && $routePath === 'login') {
            return [
                'email' => 'required|email|exists:users,email',
                'password' => 'required|min:6|max:50'
            ];
        }

        if ($this->isMethod('post') && $routePath === 'counter-create') {
            return [
                'counter_name' => 'required|string|unique:counters,counter_name|max:100',
                'prefix' => 'required|string|max:10',
                'status' => 'required|boolean',

            ];
        }


        if (($this->isMethod('post') || $this->isMethod('put'))  && $routePath === 'counter-edit') {
            return [
                'id' => 'required|integer|exists:counters,id',
                'counter_name' => 'required|string|max:100|unique:counters,counter_name,' . $this->input('id'),
                'prefix' => 'required|string|max:10',
            ];
        }


        if ($this->isMethod('post') && $routePath === 'department-create') {
            return [
                'code' => 'required|unique:departments,code',
                'name' =>        'required|string|max:100|unique:departments,name',
                'description' => 'nullable|string',
            ];
        }
        if (($this->isMethod('post') || $this->isMethod('put')) && $routePath === 'department-edit') {
            return [
                'id' => 'required|integer|exists:departments,id',
                'name' => 'required|string|max:100|unique:departments,name,' . $this->input('id'),
                'description' => 'nullable|string',
            ];
        }


        if ($this->isMethod('post') && $routePath === 'create-project') {

            return [
                'user_id' => 'exists:users,id',
                'name' => 'required|string|max:200',
                'code' => 'nullable|string|max:40',
                'client_name' => 'required|string|max:40',
                'start_date' => 'nullable|date',
                'received_date' => 'nullable|date',
                'client_delivery_date' => 'required|date',
                'company_delivery_date' => 'nullable|date',
                'status' => 'nullable|in:pending,running,completed,on_hold,canceled',
                'priority' => 'nullable|string|max:100',
                'budget' => 'nullable|numeric|min:0',
                'actual_cost' => 'nullable|numeric|min:0',
                'profit_loss' => 'nullable|numeric',
                'team_size' => 'nullable|integer|min:1',
                'project_category' => 'nullable|string|max:100',
                'location' => 'nullable|string|max:200',
                'remark' => 'nullable|string',
            ];
        }
        if (($this->isMethod('post') || $this->isMethod('put')) && $routePath === 'project-edit') {
            return [
                'id' => 'required|integer|exists:projects,id',
                'name' => 'required|string|max:200|unique:projects,name,' . $this->input('id'),
                'code' => 'nullable|string|max:40|unique:projects,code,' . $this->input('id'),
                'status' => 'required|string|in:pending,in_progress,completed,on_hold',
                'priority' => 'required|string|in:Low,Medium,High',
                'start_date' => 'nullable|date',
                'received_date' => 'nullable|date',
                'client_delivery_date' => 'required|date',
                'company_delivery_date' => 'nullable|date',
                'budget' => 'nullable|numeric|min:0',
                'actual_cost' => 'nullable|numeric|min:0',
                'profit_loss' => 'nullable|numeric',
                'team_size' => 'nullable|integer|min:1',
                'project_category' => 'nullable|string|max:100',
                'location' => 'nullable|string|max:200',
                'remark' => 'nullable|string',
            ];
        }


        if ($this->isMethod('post') && $routePath === 'employee-create') {
            return [
                'email' => 'required|email|unique:employees,email',
                'password' => 'required|string|min:6|max:50',
                'emp_id' => 'required|string|unique:employees,emp_id',
                'name' => 'required|string|max:255',
                'mobile' => 'required|digits:10|unique:employees,mobile',
                'date_of_joining' => 'required|date',
                'department_id' => 'required|integer|exists:departments,id',
                'position' => 'nullable|string|max:255',
                'salary' => 'nullable|numeric|min:0',
                'employee_type' => 'nullable|string|max:255',
                'login_attempts' => 'nullable|integer|min:0',
                'account_locked' => 'nullable|boolean',
                'annual_bonus' => 'nullable|numeric|min:0',
                'is_on_leave' => 'nullable|boolean',
                'notice_period' => 'nullable|string|max:255',
                'work_time' => 'nullable|string|max:255',
                'work_from_home' => 'nullable|boolean',
                'address' => 'nullable|string|max:500',
                'emergency_contact_number' => 'nullable|digits:10',
                'father_name' => 'nullable|string|max:255',
                'mother_name' => 'nullable|string|max:255',
                'bank_name' => 'nullable|string|max:255',
                'image' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
                'bank_account_number' => 'nullable|string|max:50',
                'ifsc_code' => 'nullable|string|max:20',
                'date_of_birth' => 'nullable|date',
                'gender' => 'nullable|string|in:male,female,other',
                'zipcode' => 'nullable|string|max:10',
                'resume' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
                'working_hours' => 'nullable|string|max:255',
                'biometric_id' => 'nullable|string|max:255',
            ];
        }

        if ($this->isMethod('post') && $routePath === 'employee-edit-post') {


            return [
                'id' => 'required|integer|exists:employees,id',
                'email' => 'sometimes|email|unique:employees,email,' . $this->input('id'),
                // 'password' => 'required|string|min:6|max:50',
                'emp_id' => 'sometimes|nullable|string|unique:employees,emp_id,' . $this->input('id'),
                'name' => 'required|string|max:255',
                'mobile' => 'sometimes|required|digits:10|unique:employees,mobile,' . $this->input('id'),
                'date_of_joining' => 'required|date',
                'department_id' => 'required|integer|exists:departments,id',
                'position' => 'nullable|string|max:255',
                'salary' => 'nullable|numeric|min:0',
                'status' => 'nullable',
                'employee_type' => 'nullable|string|max:255',
                'login_attempts' => 'nullable|integer|min:0',
                'account_locked' => 'nullable|boolean',
                'annual_bonus' => 'nullable|numeric|min:0',
                'is_on_leave' => 'nullable|boolean',
                'notice_period' => 'nullable|string|max:255',
                'work_time' => 'nullable|string|max:255',
                'image' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
                'work_from_home' => 'nullable|boolean',
                'address' => 'nullable|string|max:500',
                'emergency_contact_number' => 'nullable|digits:10',
                'father_name' => 'nullable|string|max:255',
                'mother_name' => 'nullable|string|max:255',
                'bank_name' => 'nullable|string|max:255',
                'bank_account_number' => 'nullable|string|max:50',
                'ifsc_code' => 'nullable|string|max:20',
                'date_of_birth' => 'nullable|date',
                'gender' => 'nullable|string|in:male,female,other',
                'zipcode' => 'nullable|string|max:10',
                'resume' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
                'working_hours' => 'nullable|string|max:255',

            ];
        }
        if ($this->isMethod('post') && $routePath === 'tasks-create') {

            return [
                'project_id'     => 'required|exists:projects,id',
                'employee_id'    => 'required|exists:employees,id',
                'name'           => 'required|string|max:255',
                'status'         => 'required|in:pending,in_progress,completed,on_hold,urgent',
                'priority'       => 'required|in:Low,Medium,High',
                'actual_hours'   => 'nullable',
                'start_date'     => 'required|date',
                'complete_date'  => 'nullable|date|after_or_equal:start_date',
                'estimated_date' => 'nullable|date|after_or_equal:start_date',
            ];
        }

        if (($this->isMethod('post') || $this->isMethod('put')) && $routePath === 'tasks-edit') {

            return [
                'id' => 'required|integer|exists:tasks,id',
                'name' => 'required|string|max:255',
                'project_id' => 'required|exists:projects,id',
                'employee_id' => 'required|exists:employees,id',
                'status' => 'required|in:pending,in_progress,completed,on_hold,urgent',
                'priority' => 'required|in:Low,Medium,High',
                'actual_hours' => 'nullable',
                'start_date' => 'required|date',
                'complete_date' => 'nullable|date|after_or_equal:start_date',
                'estimated_date' => 'required|date|after_or_equal:start_date',
            ];
        }

        if ($this->isMethod('post') && $routePath === 'create-holiday') {

            return [
                'name'           => 'required|string|max:255',
                'date'           => 'required|date',
                'is_active'      => 'required|boolean',
                'color'          => 'nullable|string|max:50',
                'remark'         => 'nullable|string|max:500',
            ];
        }
        if (($this->isMethod('post') || $this->isMethod('put')) && $this->route()->getName() === 'admin.holiday.edit') {
            return [
                'id' => 'required|integer|exists:holidays,id',
                'name' => 'required|string|max:100|unique:holidays,name,' . $this->input('id'),
                'approved_by' => 'required|string|max:100',
                'date' => 'required|date',
                'is_active' => 'required|boolean',
                'color' => 'nullable|string|max:7',
                'remark' => 'nullable|string|max:255',
            ];
        }

        if (($this->isMethod('post') || $this->isMethod('put')) && $this->route()->getName() === 'admin.profile.update') {
            return [
                'id' => 'required|integer|exists:users,id',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'username' => 'required|string|max:100',
                'mobile' => 'nullable|string|max:15',
                'email' => 'required|email|unique:users,email,' . $this->input('id'),
            ];
        }

        if (($this->isMethod('post') || $this->isMethod('put')) && $routePath === 'password-update') {
            return [
                'current_password' => 'required|string',  
                'password' => 'required|string|min:6|confirmed',  
                'password_confirmation' => 'required|string|min:6', 
            ];
        }

        if (($this->isMethod('post') || $this->isMethod('put')) && $this->route()->getName() === 'admin.leaves.edit') {
            return [
                'id' => 'required|integer|exists:leave_requests,id',
                'status' => 'required|in:pending,approved,rejected',
                'rejection_reason_text' => [
                    'nullable',
                    'string',
                    Rule::requiredIf(function () {
                        return $this->input('status') === 'rejected';
                    }),
                ],
            ];
        }


          if ($this->isMethod('post') && $routePath === 'add-salary-create') {

            return [
              
        'employee_id' => 'required',
        'salary_month' => 'required|date_format:Y-m',
 
               
            ];
        }

          if ($this->isMethod('put') && $this->routeIs('admin.workformhome.update')) {
    return [
        'id' => 'required|exists:work_forms,id',
        'status' => 'required|in:pending,approved,rejected',
    ];
}



        return [];
    }
}
