# MODULE 4: Financial Intelligence

## Prerequisites
- Modules 1-3 must be built first (dashboard, CRM, clients/projects/invoices)

## What to Build
Transform the basic given/received finance tracker into a proper P&L system with expense categories, venture tagging, monthly P&L views, and revenue target management.

## Files to Create / Modify

### Migrations
1. `add_category_venture_to_finances_table.php` — adds `category`, `venture`, `is_recurring`, `recurring_day` to existing `finances` table

### Controllers
2. `app/Http/Controllers/FinanceDashboardController.php` — new controller for P&L views
3. `app/Http/Controllers/RevenueTargetController.php` — CRUD for revenue targets
4. `app/Http/Controllers/Api/FinanceDashboardApiController.php`
5. `app/Http/Controllers/Api/RevenueTargetApiController.php`

### Views
6. `resources/views/finance/dashboard.blade.php` — Monthly P&L dashboard
7. `resources/views/revenue-targets/index.blade.php` — manage targets
8. `resources/views/revenue-targets/create.blade.php`

### Modify
9. `resources/views/finance/create.blade.php` — add category + venture dropdowns
10. `resources/views/finance/edit.blade.php` — add category + venture dropdowns
11. `app/Http/Controllers/FinanceController.php` — update store/update to handle new fields
12. `database/seeders/DatabaseSeeder.php` — add 'revenue targets' to modules
13. `routes/web.php` + `routes/api.php`
14. `resources/views/layouts/admin.blade.php` — restructure Finance sidebar section

---

## Migration: Add columns to finances

```php
Schema::table('finances', function (Blueprint $table) {
    $table->string('category', 100)->nullable()->after('type');
    $table->string('venture', 50)->default('aurateria')->after('category');
    $table->boolean('is_recurring')->default(false)->after('venture');
    $table->integer('recurring_day')->nullable()->after('is_recurring');
});
```

### Category Constants (add to Finance model)
```php
// For type = 'given' (expenses)
const EXPENSE_CATEGORIES = [
    'salary' => 'Salary',
    'emi' => 'EMI / Loan Payment',
    'office' => 'Office Expenses',
    'subscription' => 'Subscriptions (AI, Microsoft, etc.)',
    'rent' => 'Rent',
    'medical' => 'Medical Expenses',
    'house' => 'House Expenses',
    'server' => 'Server / Hosting',
    'travel' => 'Travel',
    'misc' => 'Miscellaneous',
];

// For type = 'received' (income)
const INCOME_CATEGORIES = [
    'client_payment' => 'Client Payment',
    'maintenance' => 'Maintenance/Retainer',
    'refund' => 'Refund',
    'other' => 'Other Income',
];

const VENTURES = ['aurateria', 'gicogifts', 'aigather', 'medical_ai', 'personal'];
```

## FinanceDashboardController

### index() — Monthly P&L View
Query parameters: `month` (default current), `venture` (default 'all')

Compute:
- **Revenue**: sum of Finance where type=received, active, in selected month, filtered by venture
- **Expenses by category**: group Finance where type=given, active, in selected month by category, sum each
- **Total Expenses**: sum of all categories
- **Profit/Loss**: revenue - total expenses
- **Month-over-month**: compare with previous month

Display:
- Top row: Revenue | Expenses | Profit cards (same style as dashboard)
- Expense breakdown: horizontal bar chart or table showing each category's amount + % of total
- Revenue by client: if invoices module exists, show which clients paid what this month (use Finance contact names)
- Monthly trend: line chart of last 6 months' profit
- Venture filter: dropdown to see P&L for specific venture or all

### pnl() — Detailed P&L report (printable)
Show full income statement format:
```
REVENUE
  Client Payments:     ₹X
  Maintenance:         ₹X
  Other:               ₹X
  TOTAL REVENUE:       ₹X

EXPENSES
  Salaries:            ₹X
  EMIs:                ₹X
  Rent:                ₹X
  Subscriptions:       ₹X
  Office:              ₹X
  Medical:             ₹X
  Other:               ₹X
  TOTAL EXPENSES:      ₹X

NET PROFIT/LOSS:       ₹X
```

## RevenueTargetController
Simple CRUD for setting monthly targets.
- `index()` — show all targets in a table (month, target amount, actual revenue, % achieved)
- `create()` / `store()` — month picker + amount input. Validate unique month.
- `edit()` / `update()` — update amount
- `destroy()` — delete target

After building this, UPDATE DashboardController to use `RevenueTarget` from DB instead of env config fallback.

## Update Finance Forms
In `create.blade.php` and `edit.blade.php`, add:

```html
<!-- Category dropdown (changes based on type selection) -->
<div class="mb-4" x-data="{ type: '{{ old('type', $finance->type ?? 'given') }}' }">
    <label>Category</label>
    <select name="category" class="w-full rounded-lg border-slate-300">
        <option value="">Select category...</option>
        <template x-if="type === 'given'">
            <!-- Expense categories -->
            @foreach(\App\Models\Finance::EXPENSE_CATEGORIES as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </template>
        <template x-if="type === 'received'">
            <!-- Income categories -->
            @foreach(\App\Models\Finance::INCOME_CATEGORIES as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </template>
    </select>
</div>

<!-- Venture dropdown -->
<div class="mb-4">
    <label>Venture</label>
    <select name="venture" class="w-full rounded-lg border-slate-300">
        @foreach(\App\Models\Finance::VENTURES as $v)
            <option value="{{ $v }}">{{ ucfirst(str_replace('_', ' ', $v)) }}</option>
        @endforeach
    </select>
</div>
```

Use Alpine.js to dynamically switch category options when type changes. The existing form already has a type field — bind it with x-model.

## Routes

### web.php
```php
use App\Http\Controllers\FinanceDashboardController;
use App\Http\Controllers\RevenueTargetController;

Route::get('/finance/dashboard', [FinanceDashboardController::class, 'index'])->name('finance.dashboard');
Route::get('/finance/pnl', [FinanceDashboardController::class, 'pnl'])->name('finance.pnl');
Route::resource('revenue-targets', RevenueTargetController::class)->except(['show']);
```

### api.php
```php
Route::get('finance/dashboard', [FinanceDashboardApiController::class, 'index']);
Route::get('finance/pnl', [FinanceDashboardApiController::class, 'pnl']);
Route::middleware(['permission:view revenue targets'])->get('revenue-targets', [RevenueTargetApiController::class, 'index']);
Route::middleware(['permission:create revenue targets'])->post('revenue-targets', [RevenueTargetApiController::class, 'store']);
Route::middleware(['permission:edit revenue targets'])->put('revenue-targets/{revenueTarget}', [RevenueTargetApiController::class, 'update']);
Route::middleware(['permission:delete revenue targets'])->delete('revenue-targets/{revenueTarget}', [RevenueTargetApiController::class, 'destroy']);
```

## Sidebar Update
Restructure the Finance section in sidebar:
```html
<li class="pt-4 pb-1 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Finance</li>
@can('view finance')
<li><a href="{{ route('finance.dashboard') }}" ...><i class="fas fa-chart-pie ..."></i> Monthly P&L</a></li>
<li><a href="{{ route('finance.index') }}" ...><i class="fas fa-wallet ..."></i> Transactions</a></li>
@endcan
@can('view revenue targets')
<li><a href="{{ route('revenue-targets.index') }}" ...><i class="fas fa-bullseye ..."></i> Revenue Targets</a></li>
@endcan
```

Remove or move "Finance Contacts" to a sub-page or less prominent position.

## Seeder Update
Add to `$modules`:
```php
'revenue targets'
```

## Verification
1. Add a finance transaction → category and venture dropdowns work
2. Visit /finance/dashboard → see monthly P&L with category breakdown
3. Create revenue targets → dashboard gauge uses DB value
4. Filter by venture → shows only that venture's P&L
5. API endpoints return correct JSON
