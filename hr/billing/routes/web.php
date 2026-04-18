<?php

use App\Http\Controllers\Admin\AddEmployeeController;
use App\Http\Controllers\Admin\AdminAssignController;
use App\Http\Controllers\Admin\AdminAttendancController;
use App\Http\Controllers\Admin\AdminProjectController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BillesController;
use App\Http\Controllers\Admin\ChangePasswordController;
use App\Http\Controllers\Admin\ProfilController;
use App\Http\Controllers\Admin\RateController;
use App\Http\Controllers\Admin\RecevedController;
use App\Http\Controllers\Admin\ViewProjectController;
use App\Http\Controllers\User\PasswordChangeController;
use App\Http\Controllers\User\ProjectController;
use App\Http\Controllers\User\ProjectWorkingController;
use App\Http\Controllers\User\UserAuthController;
use App\Http\Controllers\User\UserProfileController;
use App\Http\Controllers\User\UserViewController;
use Illuminate\Support\Facades\Route;




// Route::get('/Signup',[AuthController::class,'index'])->name('register.form');
// Route::post('/signup', [AuthController::class, 'register'])->name('register.submit');
Route::get('/admin/login', [AuthController::class, 'showlogin'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');


Route::get('/admin/forgot-password', [AuthController::class, 'showForgotForm'])->name('forgot.form');
Route::post('/send-forgot-otp', [AuthController::class, 'sendOtp'])->name('forgot.send.otp');
Route::post('/verify-forgot-otp', [AuthController::class, 'verifyOtp'])->name('forgot.verify.otp');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('forgot.reset.password');



Route::middleware('admin')->group(function () {

  Route::get('/admin/hourly/rate',[RateController::class,'index'])->name('admin.hourly.rate');
  Route::get('/admin/add/rate',[RateController::class,'showadd'])->name('admin.add.rate');
  Route::post('admin/rate/store', [RateController::class, 'store'])->name('admin.rate.store');
  Route::delete('/admin/rates/{id}', [RateController::class,'ratedelete'])->name('admin.rate.delete');


  //Admin/dashboard Routes
  Route::get('/admin/billes',[BillesController::class,'index'])->name('admin.billes.payments');
  Route::delete('/admin/view_details/{id}', [RecevedController::class, 'deleteView'])->name('admin.project-user.delete');
  Route::get('/admin/view/payment/{user_id}/{project_id}',[RecevedController::class,'viewpayment'])->name('admin.view.payments');
  Route::get('/admin/all/projects/{user_id}', [BillesController::class,'getProjectUserData'])->name('admin.projects.all');
  Route::delete('/admin/project-user/{id}', [BillesController::class, 'deleteProjectUser'])->name('admin.project-user.delete');
  Route::delete('/admin/project-receved/{id}', [RecevedController::class, 'deleteProjectUser'])->name('admin.receved-user.delete');
  Route::get('/admin/payment/create', [RecevedController::class, 'create'])->name('admin.payament.create');


  Route::get('/admin/receved/view/{user_id}/{project_id}',[RecevedController::class,'index'])->name('admin.receveds');
  Route::post('/admin/add/payement/{user_id}/{project_id}', [RecevedController::class, 'store'])->name('admin.payament.store');
  Route::get('/admin/view/projects/{user_id}',[ViewProjectController::class,'index'])->name('admin.view.projects');
  Route::get('/admin/dashboard',[AddEmployeeController::class,'index'])->name('admin.dashboard');
  Route::get('/user/edit',[AddEmployeeController::class,'showedit'])->name('user/edit');
  Route::delete('/users/delete/{id}', [AddEmployeeController::class, 'destroy'])->name('users.destroy');
  Route::put('/users/update/{id}', [AddEmployeeController::class, 'update'])->name('user.update');
  Route::get('/add/user',[AddEmployeeController::class,'create'])->name('admin.add.employee');
  Route::post('/user/store', [AddEmployeeController::class, 'store'])->name('user.store');
  Route::get('/logout', [AuthController::class, 'logout'])->name('user.logout');
  
  Route::get('/admin/add/payement/{user_id}/{project_id}', [RecevedController::class, 'showadd'])->name('admin.payament.add');
  // Route::get('/admin/payment/{user_id}/{project_id}',[RecevedController::class,'showadd'])->name('admin.payament.add');

//Admin/ Project Routes  
Route::delete('/admin/assinged/project/delete/{id}', [AdminProjectController::class, 'assingdestroy'])->name('admin.project.delete');
Route::put('/admin/status/update/{id}', [AdminProjectController::class, 'statusupdate'])->name('admin.status.update');
Route::get('/projects', [AdminProjectController::class, 'createproject'])->name('admin.project.index');
Route::post('/add/projects',   [AdminProjectController::class, 'store'])->name('project.store');
Route::delete('/project/delete/{id}', [AdminProjectController::class, 'destroy'])->name('users.destroy');
// Route::put('/update/project/{id}', [AdminProjectController::class, 'update'])->name('admin.project.update');
Route::get('/project/index',[AdminProjectController::class,'index'])->name('admin.project');
Route::put('/admin/project/update/{id}', [AdminProjectController::class, 'update'])->name('admin.project.update');



// Route::get('/admin/assign/project',[AdminAssignController::class,'index'])->name('assinged.projects');
Route::post('/assign-project/{user_id}/{project_id}', [AdminAssignController::class, 'assignProject']);
Route::get('/admin/assign/user/{project_id}', [AdminAssignController::class, 'showusers'])->name('assign.user');
Route::put('/admin/user/update/{id}', [AdminAssignController::class, 'userupdate'])->name('assing.user.update');
// Route::delete('/admin/user/delete/{id}', [AdminAssignController::class, 'userdestroy'])->name('assing.users.destroy');
Route::get('/admin/assign-project/{project_id}', [AdminAssignController::class, 'showAssignForm'])->name('assign.form');
Route::get('/admin/user/working/timeing/{project_id}', [AdminAssignController::class, 'time'])->name('user_working_timeing');
// Route::get('/admin/assign-project/{id}', [AdminAssignController::class, 'showAssignProjectForm'])->name('admin.showAssignProjectForm');
Route::post('/admin/assign-user', [AdminAssignController::class, 'assignUser'])->name('admin.assignUser');
// Route::get('/admin/assign-project/{id}', [AdminAssignController::class, 'showAssignProjectForm'])->name('admin.showAssignProjectForm');

Route::get('/admin/assign/user/{projectId}', [AdminAssignController::class, 'showAssignProjectForm'])->name('admin.assignUserForm');
Route::delete('/admin/delete/user/{id}', [AdminAssignController::class, 'deleteUser'])->name('admin.deleteUser');

//Admin/Profiles Routes
Route::get('/admin/profile', [ProfilController::class, 'index'])->name('user.profile');
Route::post('/profile/update', [ProfilController::class, 'update'])->name('user.update');



Route::get('/admin/change-password', [ChangePasswordController::class, 'index'])->name('admin.change-password-form');
Route::post('/admin/send-otp', [ChangePasswordController::class, 'sendOtp'])->name('admin.send-otp');
Route::post('/admin/verify-otp', [ChangePasswordController::class, 'verifyOtp'])->name('admin.verify-otp');
Route::post('/admin/change-password', [ChangePasswordController::class, 'changePassword'])->name('admin.change-password');




});







// user route
Route::get('/user/login', [UserAuthController::class, 'index'])->name('user');
Route::post('/login/user', [UserAuthController::class, 'login'])->name('user.login');
Route::get('/user/forgot-password', [UserAuthController::class, 'showForgotForm'])->name('user.forgot.form');
Route::post('/user/send-forgot-otp', [UserAuthController::class, 'sendOtp'])->name('user.forgot.send.otp');
Route::post('/user/verify-forgot-otp', [UserAuthController::class, 'verifyOtp'])->name('user.forgot.verify.otp');
Route::post('/user/reset-password', [UserAuthController::class, 'resetPassword'])->name('user.forgot.reset.password');

Route::middleware('user')->group(function () {
Route::get('/user/password/change', [PasswordChangeController::class, 'index'])->name('password.user');
Route::post('/user/password/update', [PasswordChangeController::class, 'changePassword'])->name('update.password.user');
Route::get('/user/profile', [UserProfileController::class, 'index'])->name('updated.user.profile');
Route::post('/profile/user/update', [UserProfileController::class, 'update'])->name('user.update.profile');
Route::get('user/project', [ProjectController::class, 'index'])->name('project.index');
Route::put('/updated/projected/{id}', [ProjectController::class, 'update'])->name('user.project.update');
Route::delete('/project/deleted/{id}', [ProjectController::class, 'destroy'])->name('user.project.destroy');
Route::get('/user/logout', [UserAuthController::class, 'logout'])->name('user.logout');
Route::get('user/project/listing', [ProjectController::class, 'project'])->name('project.listing');
Route::put('/Status/projected/updated/{id}', [ProjectController::class, 'updatelist'])->name('project.status.update');
Route::post('/tap-in', [ProjectController::class, 'checkIn'])->name('project.tapin');
Route::post('/tap-out', [ProjectController::class, 'checkout'])->name('project.tapout');
Route::get('/user/projects/working/{project_id}',[ProjectWorkingController::class,'index'])->name('user.projects.working');
Route::post('/checkin', [UserViewController::class, 'usercheckIn'])->name('user.checkin');
Route::post('/checkout', [UserViewController::class, 'usercheckout'])->name('user.checkout');
Route::get('/user/view/{user_id}',[UserViewController::class,'index'])->name('user.view.');


});




