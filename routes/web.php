<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\FinanceContactController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskTodoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\FinanceDashboardController;
use App\Http\Controllers\RevenueTargetController;
use App\Http\Controllers\VentureController;
use App\Http\Controllers\DailyFocusController;
use App\Http\Controllers\ContentTopicController;
use App\Http\Controllers\ContentDraftController;
use App\Http\Controllers\AiCommandController;

Route::get('/', function () { return redirect()->route('login'); });

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware(['role:super-admin'])->group(function () {
        Route::get('/my-day/history', [DailyFocusController::class, 'history'])->name('daily-focus.history');
        Route::get('/my-day', [DailyFocusController::class, 'today'])->name('daily-focus.today');
        Route::match(['put', 'patch'], '/my-day/{dailyFocus}', [DailyFocusController::class, 'update'])->name('daily-focus.update');
    });

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

        Route::get('/content-topics', [ContentTopicController::class, 'index'])->name('content-topics.index');
        Route::post('/content-topics', [ContentTopicController::class, 'store'])->name('content-topics.store');
        Route::patch('/content-topics/{contentTopic}', [ContentTopicController::class, 'update'])->name('content-topics.update');
        Route::post('/content-topics/{contentTopic}/recycle', [ContentTopicController::class, 'recycle'])->name('content-topics.recycle');
        Route::delete('/content-topics/{contentTopic}', [ContentTopicController::class, 'destroy'])->name('content-topics.destroy');

        Route::get('/content-drafts', [ContentDraftController::class, 'index'])->name('content-drafts.index');
        Route::post('/content-drafts', [ContentDraftController::class, 'store'])->name('content-drafts.store');
        Route::post('/content-drafts/generate', [ContentDraftController::class, 'generate'])->name('content-drafts.generate');
        Route::patch('/content-drafts/{contentDraft}/status', [ContentDraftController::class, 'updateStatus'])->name('content-drafts.status');
        Route::delete('/content-drafts/{contentDraft}', [ContentDraftController::class, 'destroy'])->name('content-drafts.destroy');

        Route::post('/ai/command', AiCommandController::class)->name('ai.command');
    });

    // Finance
    Route::patch('/finance-contacts/{financeContact}/toggle', [FinanceContactController::class, 'toggleStatus'])->name('finance-contacts.toggle');
    Route::resource('finance-contacts', FinanceContactController::class);
    Route::get('/finance/dashboard', [FinanceDashboardController::class, 'index'])->name('finance.dashboard');
    Route::get('/finance/pnl', [FinanceDashboardController::class, 'pnl'])->name('finance.pnl');
    Route::patch('/finance/{finance}/toggle', [FinanceController::class, 'toggleStatus'])->name('finance.toggle');
    Route::resource('finance', FinanceController::class);
    Route::resource('revenue-targets', RevenueTargetController::class)->except(['show']);

    // Ventures
    Route::get('/ventures', [VentureController::class, 'index'])->name('ventures.index');
    Route::middleware(['role:super-admin'])->group(function () {
        Route::get('/ventures/create', [VentureController::class, 'create'])->name('ventures.create');
        Route::post('/ventures', [VentureController::class, 'store'])->name('ventures.store');
        Route::get('/ventures/{venture:slug}/edit', [VentureController::class, 'edit'])->name('ventures.edit');
        Route::put('/ventures/{venture:slug}', [VentureController::class, 'update'])->name('ventures.update');
        Route::delete('/ventures/{venture:slug}', [VentureController::class, 'destroy'])->name('ventures.destroy');
    });
    Route::get('/ventures/{venture:slug}', [VentureController::class, 'show'])->name('ventures.show');
    Route::post('/ventures/{venture:slug}/updates', [VentureController::class, 'addUpdate'])->name('ventures.updates.store');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Tasks (Advanced)
    Route::get('/tasks/personal', [TaskController::class, 'personal'])->name('tasks.personal');
    Route::get('/tasks/assignments', [TaskController::class, 'assignments'])->name('tasks.assignments');
    Route::patch('/tasks/reorder', [TaskController::class, 'reorder'])->name('tasks.reorder');
    Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggleStatus'])->name('tasks.toggle');
    Route::patch('/tasks/{task}/star', [TaskController::class, 'toggleStar'])->name('tasks.star')->middleware('permission:edit tasks');
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

    // CRM / Leads
    Route::get('/leads/pipeline', [LeadController::class, 'pipeline'])->name('leads.pipeline');
    Route::get('/leads/overdue', [LeadController::class, 'overdue'])->name('leads.overdue');
    Route::patch('/leads/{lead}/stage', [LeadController::class, 'updateStage'])->name('leads.stage');
    Route::post('/leads/{lead}/activity', [LeadController::class, 'addActivity'])->name('leads.activity');
    Route::patch('/leads/{lead}/activities/{activity}', [LeadController::class, 'updateActivity'])->name('leads.activities.update');
    Route::delete('/leads/{lead}/activities/{activity}', [LeadController::class, 'destroyActivity'])->name('leads.activities.destroy');
    Route::resource('leads', LeadController::class);

    // Clients & Projects
    Route::resource('clients', ClientController::class);
    Route::post('/projects/{project}/milestones', [ProjectController::class, 'addMilestone'])->name('projects.milestones.store');
    Route::post('/projects/{project}/milestones/reorder', [ProjectController::class, 'reorderMilestones'])->name('projects.milestones.reorder');
    Route::patch('/milestones/{milestone}/complete', [ProjectController::class, 'completeMilestone'])->name('milestones.complete');
    Route::resource('projects', ProjectController::class);
    Route::patch('/invoices/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('invoices.status');
    Route::resource('invoices', InvoiceController::class);

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