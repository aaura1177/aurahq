# MODULE 8: API Completeness & Mobile App Readiness

## Prerequisites
- ALL Modules 1-7 built and working

## What to Build
Ensure every web module has a matching API controller returning JSON. Standardize response shapes. Add any missing endpoints. This module is about auditing and filling gaps, not building new features.

## API Design Standards

### Response Format
All API responses should follow this shape:

**Success (single item):**
```json
{
    "data": { ... },
    "message": "Success"
}
```

**Success (list):**
```json
{
    "data": [ ... ],
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 25,
        "total": 120
    }
}
```

**Created (201):**
```json
{
    "data": { ... },
    "message": "Lead created successfully"
}
```

**Error (422 validation):**
```json
{
    "message": "Validation failed",
    "errors": {
        "business_name": ["The business name field is required."]
    }
}
```

**Error (403):**
```json
{
    "message": "Unauthorized"
}
```

### Existing API Controllers to Verify
These already exist — verify they return consistent response shapes:
- `Api/AuthController.php` — login/logout
- `Api/DashboardApiController.php` — dashboard data
- `Api/ProfileApiController.php` — profile CRUD
- `Api/UserApiController.php` — user management
- `Api/RoleApiController.php` — roles
- `Api/PermissionApiController.php` — permissions
- `Api/FinanceContactApiController.php` — finance contacts
- `Api/FinanceApiController.php` — finance transactions
- `Api/TaskApiController.php` — tasks + reports + todos
- `Api/GroceryApiController.php` — grocery
- `Api/GroceryExpenseApiController.php` — grocery expenses
- `Api/ReportApiController.php` — grocery reports
- `Api/HolidayApiController.php` — holidays
- `Api/AttendanceApiController.php` — attendance
- `Api/DailyReportApiController.php` — daily reports
- `Api/DailyReportManageApiController.php` — daily report admin
- `Api/AiController.php` — AI command

### New API Controllers (from Modules 2-6)
Verify these exist and are complete:
- `Api/LeadApiController.php` — leads + activities
- `Api/ClientApiController.php` — clients
- `Api/ProjectApiController.php` — projects + milestones
- `Api/InvoiceApiController.php` — invoices
- `Api/VentureApiController.php` — ventures + updates
- `Api/DailyFocusApiController.php` — My Day
- `Api/FinanceDashboardApiController.php` — P&L data
- `Api/RevenueTargetApiController.php` — revenue targets

## Checklist for Each API Controller

For each controller, verify:

1. **All CRUD operations exist** — index, show, store, update, destroy (where applicable)
2. **Permission middleware applied** — matching the web controller's permission checks
3. **Relationships loaded** — use `->with()` to avoid N+1 in JSON responses
4. **Pagination** — list endpoints use `->paginate(25)` not `->get()`
5. **Validation** — same rules as web controller
6. **HTTP status codes** — 200 (success), 201 (created), 204 (deleted), 403 (forbidden), 404 (not found), 422 (validation)
7. **Response shape** — matches the standard format above

## Complete routes/api.php

After all modules, `routes/api.php` should contain all these route groups (verify nothing is missing):

```php
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::get('/user', function (Request $request) { /* existing */ });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/ai/command', [AiController::class, 'handleCommand']);

    // Dashboard
    Route::get('/dashboard', [DashboardApiController::class, 'index']);

    // Profile
    Route::get('/profile', [ProfileApiController::class, 'show']);
    Route::put('/profile', [ProfileApiController::class, 'update']);
    Route::put('/profile/password', [ProfileApiController::class, 'updatePassword']);

    // Admin only
    Route::middleware('role:super-admin')->prefix('admin')->group(function () {
        // Users, Roles, Permissions — existing
    });

    // Finance Contacts — existing
    // Finance Transactions — existing

    // Tasks — existing

    // Grocery — existing

    // Holidays — existing (super-admin)
    // Attendance — existing (super-admin)

    // Daily Reports — existing

    // === NEW FROM MODULES 2-6 ===

    // Leads (Module 2)
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
    Route::middleware(['permission:delete leads'])->delete('leads/{lead}', [LeadApiController::class, 'destroy']);

    // Clients (Module 3)
    Route::middleware(['permission:view clients'])->group(function () {
        Route::get('clients', [ClientApiController::class, 'index']);
        Route::get('clients/{client}', [ClientApiController::class, 'show']);
    });
    Route::middleware(['permission:create clients'])->post('clients', [ClientApiController::class, 'store']);
    Route::middleware(['permission:edit clients'])->put('clients/{client}', [ClientApiController::class, 'update']);
    Route::middleware(['permission:delete clients'])->delete('clients/{client}', [ClientApiController::class, 'destroy']);

    // Projects (Module 3)
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

    // Invoices (Module 3)
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

    // Financial Intelligence (Module 4)
    Route::middleware(['permission:view finance'])->group(function () {
        Route::get('finance/dashboard', [FinanceDashboardApiController::class, 'index']);
        Route::get('finance/pnl', [FinanceDashboardApiController::class, 'pnl']);
    });
    Route::middleware(['permission:view revenue targets'])->get('revenue-targets', [RevenueTargetApiController::class, 'index']);
    Route::middleware(['permission:create revenue targets'])->post('revenue-targets', [RevenueTargetApiController::class, 'store']);
    Route::middleware(['permission:edit revenue targets'])->put('revenue-targets/{revenueTarget}', [RevenueTargetApiController::class, 'update']);
    Route::middleware(['permission:delete revenue targets'])->delete('revenue-targets/{revenueTarget}', [RevenueTargetApiController::class, 'destroy']);

    // Ventures (Module 5)
    Route::get('ventures', [VentureApiController::class, 'index']);
    Route::get('ventures/{venture}', [VentureApiController::class, 'show']);
    Route::post('ventures/{venture}/updates', [VentureApiController::class, 'addUpdate']);

    // My Day (Module 6) — super-admin only
    Route::middleware('role:super-admin')->group(function () {
        Route::get('daily-focus', [DailyFocusApiController::class, 'today']);
        Route::post('daily-focus', [DailyFocusApiController::class, 'store']);
        Route::put('daily-focus/{dailyFocus}', [DailyFocusApiController::class, 'update']);
        Route::get('daily-focus/history', [DailyFocusApiController::class, 'history']);
    });
});
```

## Mobile App Considerations

When building the Flutter app, each API endpoint maps to a screen/feature:

| API Group | App Screen |
|-----------|-----------|
| `/api/login` | Login screen |
| `/api/dashboard` | Home/Dashboard tab |
| `/api/daily-focus` | My Day tab (super-admin) |
| `/api/leads/*` | CRM tab with pipeline + list |
| `/api/clients/*` | Clients list + detail |
| `/api/projects/*` | Projects list + detail |
| `/api/invoices/*` | Invoices list |
| `/api/tasks/*` | Tasks tab |
| `/api/finance/*` | Finance tab |
| `/api/ventures/*` | Ventures tab (super-admin) |
| `/api/daily-reports/*` | Daily Reports (employees) |
| `/api/attendance` | Attendance (admin) |

### App Role-Based Navigation:
- **Super-Admin (Ethan)**: Full access — all tabs
- **Admin**: Dashboard, CRM, Clients, Projects, Tasks, Finance, Team
- **Employee (Aman, Dharmendra, Tanushree)**: Dashboard (simple), CRM (leads they're assigned), Tasks (their assignments), Daily Reports

## Verification
1. Use Postman or similar: `POST /api/login` → get token
2. Hit every endpoint with the token → correct responses
3. Test permission denial: login as employee, try admin-only endpoint → 403
4. Test pagination: endpoints with lists return `meta` pagination info
5. Test validation: POST with missing required fields → 422 with errors
6. `php artisan route:list --path=api` — verify all routes registered
