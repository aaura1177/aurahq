# MODULE 1: CEO Command Center (Dashboard Redesign)

## Context
You are working on the AuraHQ Laravel 12 application at the project root.
- Stack: Laravel 12, Blade + Tailwind CSS, Alpine.js, Chart.js (loaded via CDN in layout), Spatie Permission, Sanctum API
- Layout: `resources/views/layouts/admin.blade.php` (do NOT modify this file)
- Existing dashboard: `app/Http/Controllers/DashboardController.php` + `resources/views/dashboard.blade.php`
- The app has roles: `super-admin`, `admin`, `employee`
- Finance model tracks transactions with `type` (given/received), `amount`, `transaction_date`, `is_active`
- Task model has `status`, `due_date`, `is_active` fields

## What to Build
Redesign ONLY the super-admin dashboard view. The employee view (the `@else` block at the bottom) stays unchanged.

## Files to Modify (ONLY THESE TWO)
1. `app/Http/Controllers/DashboardController.php`
2. `resources/views/dashboard.blade.php`

## Files to Create
1. `database/migrations/YYYY_MM_DD_create_revenue_targets_table.php`
2. `app/Models/RevenueTarget.php`

## Step 1: Create revenue_targets migration

```php
Schema::create('revenue_targets', function (Blueprint $table) {
    $table->id();
    $table->date('month');           // first day of the month (e.g., 2026-04-01)
    $table->decimal('target_amount', 12, 2);
    $table->text('notes')->nullable();
    $table->timestamps();
    $table->unique('month');
});
```

## Step 2: Create RevenueTarget model

```php
// app/Models/RevenueTarget.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RevenueTarget extends Model
{
    protected $guarded = [];
    protected $casts = ['month' => 'date', 'target_amount' => 'decimal:2'];
}
```

## Step 3: Rewrite DashboardController@index

Keep ALL existing variables (the employee view still needs them). ADD new variables for the CEO view.

New data to compute:

```php
// === CEO METRICS ===
$currentMonthStart = Carbon::now()->startOfMonth();
$currentMonthEnd = Carbon::now()->endOfMonth();
$lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
$lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

// Monthly Revenue (received this month, active only)
$monthlyRevenue = Finance::where('type', 'received')
    ->where('is_active', true)
    ->whereBetween('transaction_date', [$currentMonthStart, $currentMonthEnd])
    ->sum('amount');

// Last month revenue for % change
$lastMonthRevenue = Finance::where('type', 'received')
    ->where('is_active', true)
    ->whereBetween('transaction_date', [$lastMonthStart, $lastMonthEnd])
    ->sum('amount');

$revenueChange = $lastMonthRevenue > 0
    ? round((($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
    : ($monthlyRevenue > 0 ? 100 : 0);

// Monthly Expenses (given this month, active only)
$monthlyExpenses = Finance::where('type', 'given')
    ->where('is_active', true)
    ->whereBetween('transaction_date', [$currentMonthStart, $currentMonthEnd])
    ->sum('amount');

// Monthly Profit
$monthlyProfit = $monthlyRevenue - $monthlyExpenses;

// Revenue Target
$revenueTarget = RevenueTarget::where('month', $currentMonthStart->format('Y-m-01'))->first();
$targetAmount = $revenueTarget ? $revenueTarget->target_amount : 200000; // default ₹2L
$targetProgress = $targetAmount > 0 ? min(round(($monthlyRevenue / $targetAmount) * 100, 1), 100) : 0;

// 6-month revenue chart data
$sixMonthLabels = [];
$sixMonthRevenue = [];
$sixMonthTarget = [];
for ($i = 5; $i >= 0; $i--) {
    $m = Carbon::now()->subMonths($i);
    $sixMonthLabels[] = $m->format('M Y');
    $sixMonthRevenue[] = Finance::where('type', 'received')
        ->where('is_active', true)
        ->whereYear('transaction_date', $m->year)
        ->whereMonth('transaction_date', $m->month)
        ->sum('amount');
    $rt = RevenueTarget::where('month', $m->startOfMonth()->format('Y-m-01'))->first();
    $sixMonthTarget[] = $rt ? $rt->target_amount : 200000;
}

// Tasks due today and overdue
$tasksDueToday = Task::where('is_active', true)
    ->where('status', '!=', 'completed')
    ->whereDate('due_date', Carbon::today())
    ->count();

$tasksOverdue = Task::where('is_active', true)
    ->where('status', '!=', 'completed')
    ->whereDate('due_date', '<', Carbon::today())
    ->whereNotNull('due_date')
    ->count();
```

Pass all new variables to the view via `compact()`. Keep ALL existing variables too.

## Step 4: Rewrite dashboard.blade.php (super-admin section only)

The `@role('super-admin')` section gets completely replaced. The `@else` employee block stays identical.

### Layout Structure:

**ROW 1 — Revenue Metrics (4 cards in a grid)**

Card 1: "Monthly Revenue"
- Large number: ₹{monthlyRevenue formatted}
- Small text below: "{revenueChange}% vs last month" (green arrow up if positive, red arrow down if negative)
- Icon: fa-arrow-trend-up in green circle

Card 2: "Monthly Expenses"
- Large number: ₹{monthlyExpenses formatted}
- Small text: "This month"
- Icon: fa-arrow-trend-down in red circle

Card 3: "Monthly Profit/Loss"
- Large number: ₹{monthlyProfit formatted}
- Color: green if positive, red if negative
- Small text: "{profit > 0 ? 'Profit' : 'Loss'} this month"
- Icon: fa-wallet in blue circle

Card 4: "Revenue Target"
- Show a circular progress indicator or simple progress bar
- Text: "₹{monthlyRevenue} / ₹{targetAmount}"
- Percentage: "{targetProgress}%"
- Color: green if > 75%, orange if 40-75%, red if < 40%
- Icon: fa-bullseye

**ROW 2 — Pipeline & Client Cards (3 cards)**
These are PLACEHOLDER cards since CRM module doesn't exist yet:

Card 1: "Pipeline Value" — Shows "—" with subtext "CRM module coming soon" — icon fa-funnel-dollar, purple bg
Card 2: "Active Clients" — Shows "—" with subtext "Projects module coming soon" — icon fa-handshake, cyan bg
Card 3: "Pending Invoices" — Shows "—" with subtext "Invoicing module coming soon" — icon fa-file-invoice, amber bg

Style these with a subtle dashed border or slightly muted opacity so they look intentionally "coming soon" but not broken.

**ROW 3 — Charts (2 columns: 2/3 + 1/3)**

Left (lg:col-span-2): "Revenue vs Target — Last 6 Months"
- Bar chart using Chart.js
- Bars = actual monthly revenue (green)
- Line overlay = target amount per month (dashed red line)
- Labels = sixMonthLabels
- Data = sixMonthRevenue, sixMonthTarget
- Same Chart.js options style as existing chart

Right (1 column): "Weekly Income & Expenses"
- Keep the existing weekly bar chart (income vs expense, last 7 days)
- Just reuse the existing chartLabels, incomeData, expenseData
- Same design but slightly smaller

**ROW 4 — Action Items (3 columns)**

Column 1: "Tasks Due Today"
- Show count: {tasksDueToday}
- Link to tasks.assignments
- If count > 0, yellow/amber background

Column 2: "Overdue Tasks"
- Show count: {tasksOverdue}
- Link to tasks.assignments with filter
- If count > 0, RED background (attention needed)

Column 3: Daily Report Compliance
- Keep the EXISTING morningReportMissing and eveningReportMissing sections
- Same design as current, just placed in this column

### Styling Rules:
- Use existing Tailwind classes: `bg-white p-6 rounded-xl shadow-sm border border-slate-100`
- Cards use `hover:shadow-md transition`
- Use `grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6` for Row 1
- Use `grid grid-cols-1 md:grid-cols-3 gap-6` for Row 2
- Use `grid grid-cols-1 lg:grid-cols-3 gap-6` for Row 3 (with `lg:col-span-2` on left chart)
- All numbers formatted with Indian number system: `number_format($amount, 0)` with ₹ prefix
- Font Awesome icons (already loaded in layout)
- Chart.js (already loaded in layout)

### Do NOT:
- Modify `layouts/admin.blade.php`
- Change the `@else` employee view block
- Add new sidebar items
- Create any new route — the dashboard route already exists
- Change any other controller or view file

### After building, verify:
1. `php artisan migrate` runs without errors
2. Login as super-admin — new dashboard renders
3. Login as employee — old welcome message shows
4. Charts render with Chart.js
5. Number formatting works correctly
