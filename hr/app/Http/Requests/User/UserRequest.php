<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        $routePath = str_replace('user/', '', $this->path());


        if ($routePath === 'login-post') {
            return [
                'email' => 'required|email',
                'password' => 'required|min:6',
            ];
        }
        if (($this->isMethod('post') || $this->isMethod('put')) && $routePath === 'task-edit') {

            return [
                'id' => 'required|integer|exists:tasks,id',
                'employee_status' => 'required|in:pending,in_progress,completed',
                'remark' => 'nullable',

            ];
        }

        if (($this->isMethod('post') || $this->isMethod('put')) && $routePath === 'profile-update') {


            return [
                'id' => 'required|integer|exists:employees,id',
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:employees,email,' . auth('employee')->user()->id,
                'position' => 'required|string|in:frontend,backend',
                'address' => 'nullable|string|max:255',
                'mobile' => 'nullable|string|max:15',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',  // Image validation
            ];
        }

        if (($this->isMethod('post') || $this->isMethod('put')) && $routePath === 'password-update') {
            return [
                'current_password' => 'required|string',  
                'password' => 'required|string|min:6|confirmed',  
                'password_confirmation' => 'required|string|min:6', 
            ];
        }
        if ($this->isMethod('post') && $routePath === 'create-leaves') {
            return [
                'leave_type_id' => 'required|exists:leave_types,id',
                'start_at' => 'required|date|before_or_equal:end_at',
                'end_at' => 'required|date|after_or_equal:start_at',
                'reason' => 'nullable|string|min:10',
            ];
        }
      if ($this->isMethod('post') && $routePath === 'store/work-form-home') {
    return [
        'work_date' => 'required|date',
        'start_time' => 'required|date_format:H:i',
        'end_time' => 'required|date_format:H:i|after:start_time',
        'reason' => 'nullable|string|min:10',
        'location' => 'nullable|string|min:3',
    ];
}


        
        

        return [];
    }
}
