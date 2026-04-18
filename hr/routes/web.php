<?php

// cd domains/aurastatus.shop/public_html

use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\PDFController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\CounterController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\LeaveRequestController;
use App\Http\Controllers\Admin\EmployeeSalaryController;
use App\Http\Controllers\Admin\WorkFormHomeController;
use App\Http\Controllers\Admin\InternshipAttendanceController;
use App\Http\Controllers\Admin\EltercityReadingController;


use App\Http\Controllers\User\UserTaskController;
use  App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\User\UserHolidayController;
use  App\Http\Controllers\User\UserLoginController;
use App\Http\Controllers\User\UserLeaveRequestController;
use App\Http\Controllers\User\UserWorkFormHomeController;
use App\Http\Controllers\User\UserInternshipController;
use App\Http\Controllers\User\UsersalaryController;

Route::prefix('admin')->group(function () {

    Route::match(['get', 'post'], '/login', [LoginController::class, 'login'])->name('admin.login');


    Route::middleware('admin.auth')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/profile', [DashboardController::class, 'profile'])->name('admin.profile');
        Route::put('/profile-update', [DashboardController::class, 'profileupdate'])->name('admin.profile.update');
        Route::put('/password-update', [DashboardController::class, 'passwordupdate'])->name('admin.password.update');

        Route::get('/internship', [InternshipAttendanceController::class, 'index'])->name('admin.internship');
        Route::delete('/delete-internship/{id}', [InternshipAttendanceController::class, 'destroy'])->name('admin.internship.destroy');





        Route::get('/logout', [DashboardController::class, 'logout'])->name('admin.logout');


        // TaskController
        Route::get('/tasks', [TaskController::class, 'index'])->name('admin.tasks');
        Route::get('/tasks-add', [TaskController::class, 'addtask'])->name('admin.tasks.add');
        Route::post('/tasks-create', [TaskController::class, 'createtask'])->name('admin.tasks.create');
        Route::PUT('/tasks-edit', [TaskController::class, 'edit'])->name('admin.tasks.edit');
        Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('admin.tasks.destroy');

        // EmployeeController

        Route::get('/employee', [EmployeeController::class, 'index'])->name('admin.employee');
        Route::get('/add-employee', [EmployeeController::class, 'addemployee'])->name('admin.employee.add');
        Route::post('/employee-create', [EmployeeController::class, 'createemployee'])->name('admin.employee.create');

        Route::get('/employee-edit/{id?}', [EmployeeController::class, 'editemployee'])->name('admin.employee.edit');
        Route::post('/employee-edit-post', [EmployeeController::class, 'editpostemployee'])->name('admin.employee.edit.post');




        Route::get('/project', [ProjectController::class, 'index'])->name('admin.project');
        Route::get('/add-project', [ProjectController::class, 'addproject'])->name('admin.project.add');
        Route::post('/create-project', [ProjectController::class, 'createproject'])->name('admin.project.create');
        Route::put('/project-edit', [ProjectController::class, 'editproject'])->name('admin.project.edit');

        Route::delete('/delete-project/{id}', [ProjectController::class, 'destroy'])->name('admin.project.destroy');



        // HolidayController
        Route::get('/holiday', [HolidayController::class, 'index'])->name('admin.holiday');
        Route::get('/add-holiday', [HolidayController::class, 'addholiday'])->name('admin.holiday.add');
        Route::post('/create-holiday', [HolidayController::class, 'createholiday'])->name('admin.holiday.create');
        Route::put('/holiday-edit', [HolidayController::class, 'editholiday'])->name('admin.holiday.edit');
        Route::delete('/delete-holiday/{id}', [HolidayController::class, 'destroy'])->name('admin.holiday.destroy');


        Route::get('/counter', [CounterController::class, 'index'])->name('admin.counter');
        Route::get('/counter-add', [CounterController::class, 'addcounter'])->name('admin.counter.add');
        Route::post('/counter-create', [CounterController::class, 'createcounter'])->name('admin.counter.create');
        Route::put('/counter-edit', [CounterController::class, 'editcounter'])->name('admin.counter.edit');


        Route::get('/eltercity_readings',[EltercityReadingController::class,'index'])->name('admin.eltercity_readings');
        Route::get('/eltercity_readings/add',[EltercityReadingController::class,'create'])->name('admin.eltercity_readings.create');
        Route::post('/eltercity_readings/store',[EltercityReadingController::class,'store'])->name('admin.eltercity_readings.store');
                Route::put('/eltercity_readings-edit', [EltercityReadingController::class, 'update'])->name('admin.eltercity_readings.edit');

        Route::delete('/delete-eltercity_readings/{id}', [EltercityReadingController::class, 'destroy'])->name('admin.eltercity_readings.destroy');


        // DepartmentController   


        Route::get('/department', [DepartmentController::class, 'index'])->name('admin.department');
        Route::get('/department-add', [DepartmentController::class, 'adddepartment'])->name('admin.department.add');
        Route::post('/department-create', [DepartmentController::class, 'createdepartment'])->name('admin.department.create');
        Route::put('/department-edit', [DepartmentController::class, 'editdepartment'])->name('admin.department.edit');
        Route::delete('/department-destroy/{id?}', [DepartmentController::class, 'destroy'])->name('admin.department.destroy');




        Route::get('/attendance', [AttendanceController::class, 'index'])->name('admin.attendance');
        Route::post('/attendance-check-out', [AttendanceController::class, 'admincheckout'])->name('admin.attendance.check-out');
        Route::put('/attendance-edit', [AttendanceController::class, 'update'])->name('admin.attendace.edit');

        Route::delete('/attendance/delete{id}', [AttendanceController::class, 'destroy'])->name('admin.destroy');
        // Route::resource('attendance', AttendanceController::class);



        Route::get('/leaves', [LeaveRequestController::class, 'index'])->name('admin.leaves');
        Route::PUT('/leaves-edit', [LeaveRequestController::class, 'editstatus'])->name('admin.leaves.edit');



        // Route::get('download-pdf', [PDFController::class, 'download']);
        Route::get('/generate-pdf', [PDFController::class, 'generatePDF']);





        // admin.salary


        Route::get('/salary', [EmployeeSalaryController::class, 'index'])->name('admin.salary');
        Route::get('/add-salary', [EmployeeSalaryController::class, 'add_salary'])->name('admin.add.salary');
        Route::post('/add-salary-create', [EmployeeSalaryController::class, 'create_salary'])->name('admin.create.salary');
        Route::delete('/salary/{id}', [EmployeeSalaryController::class, 'destroy'])->name('admin.salary.destroy');
        Route::delete('/salary-destroy/{id}', [EmployeeSalaryController::class, 'destroy'])->name('admin.salary.destroy');


        Route::get('work-form-home', [WorkFormHomeController::class, 'index'])->name('admin.work-form-home');
        Route::put('/work-form-home-update', [WorkFormHomeController::class, 'update'])->name('admin.workformhome.update');

        Route::delete('work-form-home-destroy/{id}', [WorkFormHomeController::class, 'destroy'])->name('admin.workformhome.destroy');
    });
});

   Route::get('/js/pixel',[UserDashboardController::class,'storeVisitorLocation']);

// User Route

Route::prefix('user')->group(function () {

    Route::get('/login', [UserLoginController::class, 'index'])->name('user.login');
    Route::post('/login-post', [UserLoginController::class, 'login'])->name('user.login.post');

    Route::middleware('user.auth')->group(function () {

        Route::get('/logout', [UserLoginController::class, 'logout'])->name('user.logout');

        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
        Route::get('/attendance', [UserDashboardController::class, 'attendance'])->name('user.attendance');
        Route::get('/profile', [UserDashboardController::class, 'profile'])->name('user.profile');
        Route::put('/profile-update', [UserDashboardController::class, 'profileupdate'])->name('user.profile.update');
        Route::put('/password-update', [UserDashboardController::class, 'passwordupdate'])->name('user.password.update');
        Route::post('/check-in', [UserDashboardController::class, 'checkin'])->name('user.check-in');
        Route::post('/check-out', [UserDashboardController::class, 'checkout'])->name('user.check-out');



        Route::get('/task', [UserTaskController::class, 'index'])->name('user.task');
        Route::PUT('/task-edit', [UserTaskController::class, 'edittask'])->name('user.tasks.edit');

        Route::get('/holiday', [UserHolidayController::class, 'index'])->name('user.holiday');





        Route::get('/leaves', [UserLeaveRequestController::class, 'index'])->name('user.leave');
        Route::get('/add-leaves', [UserLeaveRequestController::class, 'addleaves'])->name('add.leave');
        Route::post('/create-leaves', [UserLeaveRequestController::class, 'createleaves'])->name('create.leave');
        Route::delete('/leaves-destroy/{id?}', [UserLeaveRequestController::class, 'destroy'])->name('user.leave.destroy');


        Route::get('salary', [UsersalaryController::class, 'index'])->name('user.salary');

        Route::get('work-form-home', [UserWorkFormHomeController::class, 'index'])->name('user.work.form.home');
        Route::get('add/work-form-home', [UserWorkFormHomeController::class, 'create'])->name('user.work.formhome.create');
        Route::post('store/work-form-home', [UserWorkFormHomeController::class, 'store'])->name('user.work.formhome.store');
        Route::delete('work-form-home/{id}', [UserWorkFormHomeController::class, 'destroy'])->name('user.work.formhome.destroy');

        Route::get('/internship', [UserInternshipController::class, 'index'])->name('user.internship');
        Route::get('/internship/check-status/{employee_id}', [UserInternshipController::class, 'checkStatus']);
        Route::get('/internship/tapin/{employee_id}', [UserInternshipController::class, 'tapIn']);
        Route::get('/internship/tapout/{employee_id}', [UserInternshipController::class, 'tapOut']);
    });
});



// require __DIR__.'/auth.php';
