<?php

use App\Support\ApiJson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\ProfileApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\RoleApiController;
use App\Http\Controllers\Api\PermissionApiController;
use App\Http\Controllers\Api\FinanceContactApiController;
use App\Http\Controllers\Api\FinanceApiController;
use App\Http\Controllers\Api\TaskApiController;
use App\Http\Controllers\Api\GroceryApiController;
use App\Http\Controllers\Api\GroceryExpenseApiController;
use App\Http\Controllers\Api\ReportApiController;
use App\Http\Controllers\Api\HolidayApiController;
use App\Http\Controllers\Api\AttendanceApiController;
use App\Http\Controllers\Api\DailyReportApiController;
use App\Http\Controllers\Api\DailyReportManageApiController;
use App\Http\Controllers\Api\LeadApiController;
use App\Http\Controllers\Api\ClientApiController;
use App\Http\Controllers\Api\ProjectApiController;
use App\Http\Controllers\Api\InvoiceApiController;
use App\Http\Controllers\Api\FinanceDashboardApiController;
use App\Http\Controllers\Api\RevenueTargetApiController;
use App\Http\Controllers\Api\VentureApiController;
use App\Http\Controllers\Api\DailyFocusApiController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        $user = $request->user();

        return ApiJson::ok([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'is_active' => $user->is_active,
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ]);
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/ai/command', [AiController::class, 'handleCommand']);

    Route::get('/dashboard', [DashboardApiController::class, 'index']);

    Route::get('/profile', [ProfileApiController::class, 'show']);
    Route::put('/profile', [ProfileApiController::class, 'update']);
    Route::put('/profile/password', [ProfileApiController::class, 'updatePassword']);

    Route::middleware('role:super-admin')->prefix('admin')->group(function () {
        Route::get('users', [UserApiController::class, 'index']);
        Route::post('users', [UserApiController::class, 'store']);
        Route::get('users/{user}', [UserApiController::class, 'show']);
        Route::put('users/{user}', [UserApiController::class, 'update']);
        Route::delete('users/{user}', [UserApiController::class, 'destroy']);
        Route::patch('users/{user}/toggle', [UserApiController::class, 'toggle']);

        Route::get('roles', [RoleApiController::class, 'index']);
        Route::post('roles', [RoleApiController::class, 'store']);
        Route::get('roles/{role}', [RoleApiController::class, 'show']);
        Route::put('roles/{role}', [RoleApiController::class, 'update']);
        Route::delete('roles/{role}', [RoleApiController::class, 'destroy']);
        Route::get('permission-names', [RoleApiController::class, 'permissionsList']);
        Route::get('role-names', [RoleApiController::class, 'roleNames']);
        Route::get('permissions', [PermissionApiController::class, 'index']);
    });

    Route::middleware(['permission:view finance contacts'])->group(function () {
        Route::get('finance-contacts', [FinanceContactApiController::class, 'index']);
        Route::get('finance-contacts/{financeContact}', [FinanceContactApiController::class, 'show']);
    });
    Route::middleware(['permission:create finance contacts'])->post('finance-contacts', [FinanceContactApiController::class, 'store']);
    Route::middleware(['permission:edit finance contacts'])->group(function () {
        Route::put('finance-contacts/{financeContact}', [FinanceContactApiController::class, 'update']);
        Route::patch('finance-contacts/{financeContact}/toggle', [FinanceContactApiController::class, 'toggle']);
    });
    Route::middleware(['permission:delete finance contacts'])->delete('finance-contacts/{financeContact}', [FinanceContactApiController::class, 'destroy']);

    Route::middleware(['permission:view finance'])->group(function () {
        Route::get('finance/dashboard', [FinanceDashboardApiController::class, 'index']);
        Route::get('finance/pnl', [FinanceDashboardApiController::class, 'pnl']);
        Route::get('finance', [FinanceApiController::class, 'index']);
        Route::get('finance/{finance}', [FinanceApiController::class, 'show']);
    });
    Route::middleware(['permission:create finance'])->post('finance', [FinanceApiController::class, 'store']);
    Route::middleware(['permission:edit finance'])->group(function () {
        Route::put('finance/{finance}', [FinanceApiController::class, 'update']);
        Route::patch('finance/{finance}/toggle', [FinanceApiController::class, 'toggle']);
    });
    Route::middleware(['permission:delete finance'])->delete('finance/{finance}', [FinanceApiController::class, 'destroy']);

    Route::middleware(['permission:view revenue targets'])->get('revenue-targets', [RevenueTargetApiController::class, 'index']);
    Route::middleware(['permission:create revenue targets'])->post('revenue-targets', [RevenueTargetApiController::class, 'store']);
    Route::middleware(['permission:edit revenue targets'])->put('revenue-targets/{revenueTarget}', [RevenueTargetApiController::class, 'update']);
    Route::middleware(['permission:delete revenue targets'])->delete('revenue-targets/{revenueTarget}', [RevenueTargetApiController::class, 'destroy']);

    Route::middleware(['permission:view ventures'])->group(function () {
        Route::get('ventures', [VentureApiController::class, 'index']);
        Route::get('ventures/{venture:slug}', [VentureApiController::class, 'show']);
    });
    Route::middleware(['permission:create venture updates'])->post('ventures/{venture:slug}/updates', [VentureApiController::class, 'addUpdate']);

    Route::middleware(['permission:view tasks'])->group(function () {
        Route::get('tasks/personal', [TaskApiController::class, 'personal']);
        Route::get('tasks/assignments', [TaskApiController::class, 'assignments']);
        Route::get('tasks/{task}', [TaskApiController::class, 'show']);
    });
    Route::middleware(['permission:create tasks'])->post('tasks', [TaskApiController::class, 'store']);
    Route::middleware(['permission:edit tasks'])->group(function () {
        Route::put('tasks/{task}', [TaskApiController::class, 'update']);
        Route::patch('tasks/{task}/toggle', [TaskApiController::class, 'toggleStatus']);
        Route::patch('tasks/{task}/review', [TaskApiController::class, 'reviewTask']);
    });
    Route::middleware(['permission:delete tasks'])->delete('tasks/{task}', [TaskApiController::class, 'destroy']);

    Route::middleware(['permission:view leads'])->group(function () {
        Route::get('leads', [LeadApiController::class, 'index']);
        Route::get('leads/pipeline', [LeadApiController::class, 'pipeline']);
        Route::get('leads/overdue', [LeadApiController::class, 'overdue']);
        Route::get('leads/{lead}', [LeadApiController::class, 'show']);
    });
    Route::middleware(['permission:create leads'])->post('leads', [LeadApiController::class, 'store']);
    Route::middleware(['permission:edit leads'])->group(function () {
        Route::put('leads/{lead}', [LeadApiController::class, 'update']);
        Route::patch('leads/{lead}/stage', [LeadApiController::class, 'updateStage']);
        Route::post('leads/{lead}/activity', [LeadApiController::class, 'addActivity']);
    });
    Route::middleware(['permission:edit lead activities'])->patch('leads/{lead}/activities/{activity}', [LeadApiController::class, 'updateActivity']);
    Route::middleware(['permission:delete lead activities'])->delete('leads/{lead}/activities/{activity}', [LeadApiController::class, 'destroyActivity']);
    Route::middleware(['permission:delete leads'])->delete('leads/{lead}', [LeadApiController::class, 'destroy']);

    Route::middleware(['permission:view clients'])->group(function () {
        Route::get('clients', [ClientApiController::class, 'index']);
        Route::get('clients/{client}', [ClientApiController::class, 'show']);
    });
    Route::middleware(['permission:create clients'])->post('clients', [ClientApiController::class, 'store']);
    Route::middleware(['permission:edit clients'])->put('clients/{client}', [ClientApiController::class, 'update']);
    Route::middleware(['permission:delete clients'])->delete('clients/{client}', [ClientApiController::class, 'destroy']);

    Route::middleware(['permission:view projects'])->group(function () {
        Route::get('projects', [ProjectApiController::class, 'index']);
        Route::get('projects/{project}', [ProjectApiController::class, 'show']);
    });
    Route::middleware(['permission:create projects'])->post('projects', [ProjectApiController::class, 'store']);
    Route::middleware(['permission:edit projects'])->group(function () {
        Route::put('projects/{project}', [ProjectApiController::class, 'update']);
        Route::post('projects/{project}/milestones', [ProjectApiController::class, 'addMilestone']);
        Route::patch('milestones/{milestone}/complete', [ProjectApiController::class, 'completeMilestone']);
    });
    Route::middleware(['permission:delete projects'])->delete('projects/{project}', [ProjectApiController::class, 'destroy']);

    Route::middleware(['permission:view invoices'])->group(function () {
        Route::get('invoices', [InvoiceApiController::class, 'index']);
        Route::get('invoices/{invoice}', [InvoiceApiController::class, 'show']);
    });
    Route::middleware(['permission:create invoices'])->post('invoices', [InvoiceApiController::class, 'store']);
    Route::middleware(['permission:edit invoices'])->group(function () {
        Route::put('invoices/{invoice}', [InvoiceApiController::class, 'update']);
        Route::patch('invoices/{invoice}/status', [InvoiceApiController::class, 'updateStatus']);
    });
    Route::middleware(['permission:delete invoices'])->delete('invoices/{invoice}', [InvoiceApiController::class, 'destroy']);

    Route::middleware(['permission:create task reports'])->post('tasks/{task}/reports', [TaskApiController::class, 'storeReport']);
    Route::middleware(['permission:edit task reports'])->put('task-reports/{report}', [TaskApiController::class, 'updateReport']);
    Route::middleware(['permission:delete task reports'])->delete('task-reports/{report}', [TaskApiController::class, 'deleteReport']);

    Route::middleware(['permission:create task todos'])->post('tasks/{task}/todos', [TaskApiController::class, 'storeTodo']);
    Route::middleware(['permission:edit task todos'])->group(function () {
        Route::put('task-todos/{todo}', [TaskApiController::class, 'updateTodo']);
        Route::patch('task-todos/{todo}/status', [TaskApiController::class, 'updateTodoStatus']);
    });
    Route::middleware(['permission:delete task todos'])->delete('task-todos/{todo}', [TaskApiController::class, 'destroyTodo']);

    Route::middleware(['permission:view grocery'])->group(function () {
        Route::get('grocery', [GroceryApiController::class, 'index']);
        Route::get('grocery/{grocery}', [GroceryApiController::class, 'show']);
    });
    Route::middleware(['permission:create grocery'])->group(function () {
        Route::post('grocery', [GroceryApiController::class, 'store']);
        Route::post('grocery/variable-expense', [GroceryApiController::class, 'variableExpense']);
        Route::get('grocery-templates', [GroceryApiController::class, 'templates']);
        Route::post('grocery-templates', [GroceryApiController::class, 'storeTemplate']);
    });
    Route::middleware(['permission:edit grocery'])->group(function () {
        Route::put('grocery/{grocery}', [GroceryApiController::class, 'update']);
        Route::patch('grocery/{grocery}/toggle', [GroceryApiController::class, 'toggle']);
        Route::post('grocery/{grocery}/purchase', [GroceryApiController::class, 'markPurchased']);
        Route::post('grocery/{grocery}/pending', [GroceryApiController::class, 'markPending']);
        Route::put('grocery-templates/{template}', [GroceryApiController::class, 'updateTemplate']);
    });
    Route::middleware(['permission:delete grocery'])->delete('grocery/{grocery}', [GroceryApiController::class, 'destroy']);
    Route::middleware(['permission:delete grocery templates'])->delete('grocery-templates/{template}', [GroceryApiController::class, 'destroyTemplate']);

    Route::middleware(['permission:view grocery expenses'])->get('grocery-expenses', [GroceryExpenseApiController::class, 'index']);
    Route::middleware(['permission:create grocery expenses'])->post('grocery-expenses', [GroceryExpenseApiController::class, 'store']);
    Route::middleware(['permission:edit grocery expenses'])->group(function () {
        Route::put('grocery-expenses/{groceryExpense}', [GroceryExpenseApiController::class, 'update']);
    });
    Route::middleware(['permission:delete grocery expenses'])->delete('grocery-expenses/{groceryExpense}', [GroceryExpenseApiController::class, 'destroy']);

    Route::middleware(['permission:view reports'])->get('reports/grocery', [ReportApiController::class, 'index']);

    Route::middleware('role:super-admin')->group(function () {
        Route::apiResource('holidays', HolidayApiController::class)->names([
            'index' => 'api.holidays.index',
            'store' => 'api.holidays.store',
            'show' => 'api.holidays.show',
            'update' => 'api.holidays.update',
            'destroy' => 'api.holidays.destroy',
        ]);
        Route::get('attendance', [AttendanceApiController::class, 'index']);
        Route::get('attendance/report', [AttendanceApiController::class, 'report']);
        Route::post('attendance', [AttendanceApiController::class, 'store']);
        Route::put('attendance/{attendance}', [AttendanceApiController::class, 'update']);
        Route::delete('attendance/{attendance}', [AttendanceApiController::class, 'destroy']);

        Route::get('daily-reports/manage', [DailyReportManageApiController::class, 'index']);
        Route::post('daily-reports/allow-submission', [DailyReportManageApiController::class, 'allowSubmission']);
        Route::post('daily-reports/revoke-submission-override', [DailyReportManageApiController::class, 'revokeSubmissionOverride']);
        Route::post('daily-reports/grant-edit', [DailyReportManageApiController::class, 'grantEdit']);
        Route::post('daily-reports/revoke-edit-grant', [DailyReportManageApiController::class, 'revokeEditGrant']);
    });

    Route::get('daily-reports', [DailyReportApiController::class, 'index']);
    Route::get('daily-reports/create-context', [DailyReportApiController::class, 'createContext']);
    Route::post('daily-reports', [DailyReportApiController::class, 'store']);
    Route::get('daily-reports/{daily_report}', [DailyReportApiController::class, 'show']);
    Route::put('daily-reports/{daily_report}', [DailyReportApiController::class, 'update']);
    Route::middleware('role:super-admin')->delete('daily-reports/{daily_report}', [DailyReportApiController::class, 'destroy']);

    Route::middleware('role:super-admin')->group(function () {
        Route::get('daily-focus/history', [DailyFocusApiController::class, 'history']);
        Route::get('daily-focus', [DailyFocusApiController::class, 'today']);
        Route::post('daily-focus', [DailyFocusApiController::class, 'store']);
        Route::match(['put', 'patch'], 'daily-focus/{dailyFocus}', [DailyFocusApiController::class, 'update']);
    });
});
