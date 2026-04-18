<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\CounterController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\TaskController;


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// cd domains/aurastatus.shop/public_html


// Route::prefix('admin')->group(function () {
//     Route::post('/register', [AdminController::class, 'register']);
//     Route::post('/login', [AdminController::class, 'login']);



//     // CounterController //
//     Route::post('/add-counter', [CounterController::class, 'add_counter']);
//     Route::post('/edit-counter', [CounterController::class, 'edit_counter']);

//     // DepartmentController //
//     Route::post('/add-department', [DepartmentController::class, 'add_department']);
//     Route::post('/edit-department', [DepartmentController::class, 'edit_department']);


//     // EmployeeController  //
//     Route::post('/add-employee', [EmployeeController::class, 'add_employee']);
//     Route::post('/edit-employee', [EmployeeController::class, 'edit_employee']);
//     Route::post('/emp-id', [EmployeeController::class, 'emp_id']);



//     Route::post('/add-project', [ProjectController::class, 'addproject']);






//     Route::post('/add-task', [TaskController::class, 'addtask']);



// });
