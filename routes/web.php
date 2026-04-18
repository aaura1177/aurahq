<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\FinanceContactController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskTodoController;
use App\Http\Controllers\GroceryController;
use App\Http\Controllers\GroceryExpenseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DailyReportController;

Route::get('/', function () { return redirect()->route('login'); });

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update'); // New
    
    // Admin
    Route::middleware(['role:super-admin'])->group(function () {
        Route::patch('/users/{user}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');
        Route::resource('users', UserController::class);
        Route::resource('roles', RoleController::class);
        Route::resource('permissions', PermissionController::class);
    });

    // Finance
    Route::patch('/finance-contacts/{financeContact}/toggle', [FinanceContactController::class, 'toggleStatus'])->name('finance-contacts.toggle');
    Route::resource('finance-contacts', FinanceContactController::class);
    Route::patch('/finance/{finance}/toggle', [FinanceController::class, 'toggleStatus'])->name('finance.toggle');
    Route::resource('finance', FinanceController::class);

    // Reports (Grocery Specific)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Tasks (Advanced)
    Route::get('/tasks/personal', [TaskController::class, 'personal'])->name('tasks.personal');
    Route::get('/tasks/assignments', [TaskController::class, 'assignments'])->name('tasks.assignments');
    Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggleStatus'])->name('tasks.toggle');
    Route::post('/tasks/{task}/report', [TaskController::class, 'storeReport'])->name('tasks.report.store');
    Route::patch('/tasks/{task}/review', [TaskController::class, 'reviewTask'])->name('tasks.review');
    
    // Task Report Management
    Route::put('/tasks/reports/{report}', [TaskController::class, 'updateReport'])->name('tasks.reports.update');
    Route::delete('/tasks/reports/{report}', [TaskController::class, 'deleteReport'])->name('tasks.reports.destroy');

    // Admin Task Todos
    Route::post('/tasks/{task}/todos', [TaskTodoController::class, 'store'])->name('tasks.todos.store');
    Route::put('/tasks/todos/{todo}', [TaskTodoController::class, 'update'])->name('tasks.todos.update');
    Route::patch('/tasks/todos/{todo}/status', [TaskTodoController::class, 'updateStatus'])->name('tasks.todos.status');
    Route::delete('/tasks/todos/{todo}', [TaskTodoController::class, 'destroy'])->name('tasks.todos.destroy');

    Route::resource('tasks', TaskController::class);

    // Grocery Expenses
    Route::resource('grocery-expenses', GroceryExpenseController::class)->except(['index', 'create', 'show']);

    // Grocery
    Route::get('/grocery/templates', [GroceryController::class, 'templates'])->name('grocery.templates');
    Route::get('/grocery/templates/{template}/edit', [GroceryController::class, 'editTemplate'])->name('grocery.templates.edit');
    Route::post('/grocery/templates', [GroceryController::class, 'storeTemplate'])->name('grocery.templates.store');
    Route::put('/grocery/templates/{template}', [GroceryController::class, 'updateTemplate'])->name('grocery.templates.update');
    Route::delete('/grocery/templates/{template}', [GroceryController::class, 'destroyTemplate'])->name('grocery.templates.destroy');
    
    Route::post('/grocery/variable', [GroceryController::class, 'storeVariableExpense'])->name('grocery.variable');
    Route::post('/grocery/{grocery}/purchase', [GroceryController::class, 'markPurchased'])->name('grocery.purchase');
    Route::post('/grocery/{grocery}/pending', [GroceryController::class, 'markPending'])->name('grocery.pending');
    
    Route::patch('/grocery/{grocery}/toggle', [GroceryController::class, 'toggleStatus'])->name('grocery.toggle');
    Route::resource('grocery', GroceryController::class);

    // Holidays
    Route::resource('holidays', HolidayController::class);

    // Attendance
    Route::get('/attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');
    Route::resource('attendance', AttendanceController::class);

    // Daily Reports (till 11:00 AM & 5:15 PM IST)
    Route::get('daily-reports/manage', [DailyReportController::class, 'manage'])->name('daily-reports.manage');
    Route::post('daily-reports/allow-submission', [DailyReportController::class, 'allowSubmission'])->name('daily-reports.allow-submission');
    Route::post('daily-reports/revoke-submission-override', [DailyReportController::class, 'revokeSubmissionOverride'])->name('daily-reports.revoke-submission-override');
    Route::post('daily-reports/grant-edit', [DailyReportController::class, 'grantEditAccess'])->name('daily-reports.grant-edit');
    Route::post('daily-reports/revoke-edit-grant', [DailyReportController::class, 'revokeEditGrant'])->name('daily-reports.revoke-edit-grant');
    Route::resource('daily-reports', DailyReportController::class);
});
require __DIR__.'/auth.php';