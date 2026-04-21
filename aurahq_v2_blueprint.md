# AuraHQ v2 — Complete Redesign Blueprint
## Master Technical Specification for Ethan Stark
### April 2026

---

## CODEBASE AUDIT SUMMARY

### What Exists Today (Reviewed from GitHub repo)

**Main App (aurahq.online)** — Laravel 12 + Blade + Tailwind + Alpine.js + Sanctum API

| Module | Status | Usefulness for CEO |
|--------|--------|-------------------|
| Dashboard | Basic — shows finance totals, pending tasks, grocery due, 7-day chart, daily report compliance | LOW — shows operational data, not strategic data |
| Finance | Contacts + transactions (given/received), toggle active | MEDIUM — tracks money but no categorization, no P&L, no monthly targets |
| Tasks | Personal (admin) + employee assignments, reports, todos, review workflow | MEDIUM — functional but no project hierarchy, no client linkage |
| Grocery | Lists, items, templates, variable expenses, reports | LOW — personal utility, not business-critical |
| Daily Reports | Morning/evening slots, IST windows, overrides, email reminders | MEDIUM — good for compliance, bad for insight |
| Holidays & Attendance | CRUD + reports | HIGH — keep as-is, works well |
| AI Command | GPT-3.5 for creating tasks/grocery via natural language | LOW — barely used, outdated model |

**HR App (hr.aurahq.online)** — Separate Laravel app

| Module | Status |
|--------|--------|
| Employee Management | CRUD with departments |
| Attendance | Check-in/check-out with IP/location tracking |
| Leave Management | Request + approval workflow |
| Salary | Salary records + PDF slip generation |
| Holidays | Company holidays |
| Projects & Tasks | Basic project + task assignment |
| Work From Home | WFH request workflow |
| Internship Attendance | Tap-in/tap-out system |
| Counters, Electricity Readings | Misc operational |

### Critical Findings

1. **TWO SEPARATE APPS managing overlapping features** — both have attendance, holidays, tasks, users. This creates confusion and double data entry.
2. **Dashboard is useless for a CEO** — it shows grocery count and finance totals. No revenue targets, no pipeline, no profit tracking.
3. **Finance module has no categories** — you can't tell salary expenses from office expenses from EMIs. Everything is just "given" or "received."
4. **No CRM/lead pipeline anywhere** — Aman tracks in Google Sheets, completely disconnected.
5. **No client/project hierarchy** — tasks exist in isolation. You can't ask "how's the XYZ client project going?"
6. **No revenue goal tracking** — your ₹2L October target exists in a Word doc, not in your system.
7. **Grocery module occupies prime sidebar space** — it's the 3rd item in navigation for a business management tool.
8. **HR app is entirely separate** — employees log into a different URL, different database, different auth.
9. **Daily reports are compliance-focused, not insight-focused** — they tell you WHO submitted, not WHAT was accomplished.
10. **No venture/project separation** — Aurateria services, GicoGifts, AIGather, Medical AI all share the same flat task list.

---

## REDESIGN PLAN — BUILD ORDER

Build these modules one at a time in this exact sequence. Each module builds on the previous.

### Priority Order:
1. **Module 1: CEO Command Center** (new dashboard) — Week 1
2. **Module 2: CRM & Lead Pipeline** — Week 2
3. **Module 3: Client Projects** — Week 3
4. **Module 4: Financial Intelligence** — Week 4
5. **Module 5: Ventures (GicoGifts, AIGather, Medical AI)** — Week 5
6. **Module 6: My Day (Personal Productivity)** — Week 6
7. **Module 7: Sidebar Restructure + Navigation** — Week 6
8. **Module 8: API Endpoints for Mobile App** — Week 7-8

---

## MODULE 1: CEO COMMAND CENTER (New Dashboard)

### Purpose
Replace the current dashboard with a strategic overview that answers: "Am I on track? What needs my attention? How's the pipeline? How's the team?"

### What It Shows (Super-Admin View)

**Row 1 — Revenue Metrics (4 cards)**
- Monthly Revenue (this month, with % change from last month)
- Monthly Expenses (auto-calculated from salary + finance "given")
- Monthly Profit/Loss (revenue - expenses, colored green/red)
- Revenue Target Progress (gauge: current vs target ₹2L/₹3L)

**Row 2 — Pipeline & Clients (3 cards)**
- Active Pipeline Value (sum of all leads in proposal/negotiation stage)
- Active Clients (count of clients with ongoing projects)
- Pending Invoices (sum of invoices sent but not paid)

**Row 3 — Two Charts Side by Side**
- Left: Monthly Revenue vs Target (bar chart, last 6 months + current)
- Right: Pipeline Funnel (horizontal funnel: leads → contacted → proposal → won)

**Row 4 — Action Items**
- Overdue follow-ups from CRM
- Tasks due today (yours + team)
- Daily report compliance (existing feature, keep it)

**Employee View** — remains as-is (simple welcome + sidebar access)

### Database Changes
No new tables needed for the dashboard itself. It aggregates from other new modules (CRM, Projects, Financial Intelligence).

### Files to Create/Modify

```
MODIFY: app/Http/Controllers/DashboardController.php
MODIFY: resources/views/dashboard.blade.php
```

### Claude Code Prompt (Module 1)

```
You are working on the AuraHQ Laravel 12 application. The codebase uses Blade + Tailwind + Alpine.js + Chart.js.

TASK: Redesign the super-admin dashboard (DashboardController@index + resources/views/dashboard.blade.php).

CURRENT STATE: Dashboard shows finance totals (given/received/net), pending tasks, active users, grocery due, 7-day finance chart, and daily report compliance.

NEW DESIGN — Replace with CEO Command Center:

ROW 1 (4 stat cards):
- "Monthly Revenue" — sum of Finance where type='received' AND transaction_date is current month, is_active=true. Show % change vs last month.
- "Monthly Expenses" — sum of Finance where type='given' AND transaction_date is current month, is_active=true.
- "Monthly Profit" — revenue minus expenses. Green if positive, red if negative.
- "Revenue Target" — Create a new config value in config/app.php: 'monthly_revenue_target' => env('MONTHLY_REVENUE_TARGET', 200000). Show as a progress bar/gauge.

ROW 2 (3 cards) — These will use the CRM module tables (leads table) which may not exist yet. For now, show placeholder cards with "Coming Soon — CRM" styling. The cards should be:
- "Pipeline Value" — placeholder
- "Active Clients" — placeholder
- "Pending Invoices" — placeholder

ROW 3 (Charts):
- Left chart: Monthly Revenue vs Target — bar chart showing last 6 months of revenue (Finance received, grouped by month) with a horizontal target line.
- Right chart: Keep the existing weekly income/expense chart but make it smaller.

ROW 4 (Action Items):
- Keep the existing daily report compliance section (morningReportMissing, eveningReportMissing)
- Add "Tasks Due Today" — count of tasks where due_date = today, is_active = true
- Add "Overdue Tasks" — count of tasks where due_date < today, status != 'completed', is_active = true

STYLING:
- Use existing Tailwind classes matching the current design language (white cards, slate borders, rounded-xl, shadow-sm)
- Use Chart.js (already loaded in layout)
- Revenue/profit cards should use green/red color coding
- Keep responsive grid (mobile-friendly)

Do NOT change the employee view (the @else block at the bottom showing welcome message).
Do NOT change the admin layout (layouts/admin.blade.php).
Do NOT touch any other files.
```

---

## MODULE 2: CRM & LEAD PIPELINE

### Purpose
Replace Aman's Google Sheet with a proper CRM. Track every lead from first contact to signed client.

### Database Schema

```sql
-- Migration: create_leads_table
CREATE TABLE leads (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    business_name VARCHAR(255) NOT NULL,
    contact_person VARCHAR(255) NULL,
    phone VARCHAR(50) NULL,
    email VARCHAR(255) NULL,
    website VARCHAR(255) NULL,
    industry VARCHAR(100) NULL,  -- clinic, restaurant, hotel, gym, etc.
    city VARCHAR(100) NULL,
    source VARCHAR(100) NULL,    -- linkedin, upwork, whatsapp, referral, walk-in, facebook
    stage VARCHAR(50) NOT NULL DEFAULT 'prospect',
    -- Stages: prospect, contacted, discovery_call, proposal_sent, negotiation, won, lost
    estimated_value DECIMAL(12,2) NULL,  -- potential project value in INR
    assigned_to BIGINT UNSIGNED NULL,    -- FK to users (Aman, or you)
    notes TEXT NULL,
    lost_reason VARCHAR(255) NULL,
    next_follow_up DATE NULL,
    last_contacted_at TIMESTAMP NULL,
    converted_at TIMESTAMP NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (assigned_to) REFERENCES users(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Migration: create_lead_activities_table
CREATE TABLE lead_activities (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lead_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    type VARCHAR(50) NOT NULL,
    -- Types: note, call, whatsapp, email, meeting, follow_up, stage_change, proposal_sent
    description TEXT NOT NULL,
    metadata JSON NULL,  -- for stage changes: {from: 'prospect', to: 'contacted'}
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### Models

```
app/Models/Lead.php
app/Models/LeadActivity.php
```

### Controllers

```
app/Http/Controllers/LeadController.php          — Web CRUD + pipeline view
app/Http/Controllers/Api/LeadApiController.php    — API for mobile app
```

### Views

```
resources/views/leads/index.blade.php        — Pipeline/Kanban view + list view toggle
resources/views/leads/show.blade.php         — Lead detail with activity timeline
resources/views/leads/create.blade.php       — Add new lead form
resources/views/leads/edit.blade.php         — Edit lead
```

### Routes (add to web.php)

```php
// CRM / Leads
Route::get('/leads/pipeline', [LeadController::class, 'pipeline'])->name('leads.pipeline');
Route::patch('/leads/{lead}/stage', [LeadController::class, 'updateStage'])->name('leads.stage');
Route::post('/leads/{lead}/activity', [LeadController::class, 'addActivity'])->name('leads.activity');
Route::get('/leads/overdue', [LeadController::class, 'overdue'])->name('leads.overdue');
Route::resource('leads', LeadController::class);
```

### Permissions to Seed

```php
$modules[] = 'leads';
$modules[] = 'lead activities';
```

### Key Features

1. **Pipeline View** — Kanban board with columns: Prospect → Contacted → Discovery Call → Proposal Sent → Negotiation → Won/Lost. Drag-and-drop using Alpine.js.
2. **List View** — Table with filters by stage, industry, assigned_to, date range.
3. **Lead Detail Page** — Activity timeline (like a mini-chat showing all interactions), next follow-up date, estimated value, quick stage change buttons.
4. **Overdue Follow-ups** — Leads where next_follow_up < today and stage not in (won, lost).
5. **Aman's Daily Summary** — Dashboard widget showing: leads added today, messages sent, follow-ups due.
6. **Auto-activity on stage change** — When stage changes, auto-create a lead_activity with type='stage_change'.

### Claude Code Prompt (Module 2)

```
You are working on the AuraHQ Laravel 12 application (Blade + Tailwind + Alpine.js, Spatie Permission, Sanctum API).

TASK: Build a complete CRM/Lead Pipeline module.

STEP 1 — Create migrations:
- leads table (see schema in blueprint)
- lead_activities table (see schema in blueprint)

STEP 2 — Create models:
- app/Models/Lead.php with relationships: assignee (belongsTo User), creator (belongsTo User), activities (hasMany LeadActivity)
- app/Models/LeadActivity.php with relationships: lead (belongsTo Lead), user (belongsTo User)
- Use $guarded = [] and proper casts (dates, booleans, json for metadata)

STEP 3 — Update DatabaseSeeder.php:
- Add 'leads' and 'lead activities' to the $modules array so permissions are auto-generated

STEP 4 — Create LeadController.php with these methods:
- index() — list view with filters (stage, industry, assigned_to, date range). Paginate 25.
- pipeline() — kanban view grouped by stage with counts and total estimated value per stage
- show($lead) — detail page with all lead_activities ordered by created_at desc
- create() / store() — form with all fields. assigned_to dropdown of active users.
- edit() / update()
- updateStage($lead) — PATCH to change stage. Auto-creates a lead_activity of type 'stage_change' with metadata {from, to}. If stage='won', set converted_at. If next_follow_up is null and stage is not won/lost, auto-set to 4 days from now.
- addActivity($lead) — POST to add a note/call/whatsapp/meeting entry
- overdue() — leads where next_follow_up < today AND stage NOT IN ('won','lost')

STEP 5 — Create Blade views:
- leads/index.blade.php — table view with filters. Show: business_name, contact_person, stage (colored badge), industry, estimated_value, next_follow_up (red if overdue), assigned_to. Link to show page.
- leads/pipeline.blade.php — kanban board. Each column is a stage. Each card shows business_name, estimated_value, next_follow_up, assigned_to avatar. Use Alpine.js for drag-and-drop (use simple form-based stage change buttons if drag-drop is complex).
- leads/show.blade.php — top section: lead details card. Below: activity timeline (vertical timeline, each activity shows type icon, description, user name, timestamp). Quick-action buttons: "Log Call", "Log WhatsApp", "Add Note", "Change Stage" dropdown. Right sidebar: quick stats (days since created, total activities, days since last contact).
- leads/create.blade.php and leads/edit.blade.php — standard forms matching existing form style.

STEP 6 — Add routes to routes/web.php (inside the auth middleware group):
Route::get('/leads/pipeline', [LeadController::class, 'pipeline'])->name('leads.pipeline');
Route::patch('/leads/{lead}/stage', [LeadController::class, 'updateStage'])->name('leads.stage');
Route::post('/leads/{lead}/activity', [LeadController::class, 'addActivity'])->name('leads.activity');
Route::get('/leads/overdue', [LeadController::class, 'overdue'])->name('leads.overdue');
Route::resource('leads', LeadController::class);

STEP 7 — Add to sidebar in layouts/admin.blade.php:
Add a new section "Sales & CRM" before the "Modules" section with:
- Pipeline (leads.pipeline) — icon: fa-columns
- All Leads (leads.index) — icon: fa-user-plus
- Overdue Follow-ups (leads.overdue) — icon: fa-clock (show count badge if overdue > 0)
Wrap in @can('view leads')

STEP 8 — Create API controller:
- app/Http/Controllers/Api/LeadApiController.php mirroring web controller with JSON responses
- Add API routes in routes/api.php with permission middleware

STYLING: Match existing app design language. White cards, slate borders, rounded-xl, Tailwind utilities. Stage badges use colors: prospect=slate, contacted=blue, discovery_call=cyan, proposal_sent=purple, negotiation=orange, won=green, lost=red.

Existing patterns to follow:
- Controller permission middleware: use HasMiddleware interface like TaskController
- Model style: use $guarded = [] like Task model
- Form validation: inline in controller like TaskController
- View extending: @extends('layouts.admin') with @section('title'), @section('header'), @section('content')
```

---

## MODULE 3: CLIENT PROJECTS

### Purpose
When a lead converts to "won", create a client project. Track milestones, delivery, hours, and invoicing.

### Database Schema

```sql
-- Migration: create_clients_table
CREATE TABLE clients (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    contact_person VARCHAR(255) NULL,
    email VARCHAR(255) NULL,
    phone VARCHAR(50) NULL,
    company VARCHAR(255) NULL,
    lead_id BIGINT UNSIGNED NULL,  -- FK to leads (if converted from CRM)
    notes TEXT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Migration: create_projects_table
CREATE TABLE projects (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    client_id BIGINT UNSIGNED NOT NULL,
    description TEXT NULL,
    venture VARCHAR(50) DEFAULT 'aurateria',
    -- Ventures: aurateria, gicogifts, aigather, medical_ai
    status VARCHAR(50) DEFAULT 'active',
    -- Statuses: planning, active, on_hold, completed, cancelled
    budget DECIMAL(12,2) NULL,
    start_date DATE NULL,
    expected_end_date DATE NULL,
    actual_end_date DATE NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Migration: create_milestones_table
CREATE TABLE milestones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    due_date DATE NULL,
    completed_at TIMESTAMP NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);

-- Migration: create_invoices_table
CREATE TABLE invoices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id BIGINT UNSIGNED NULL,
    client_id BIGINT UNSIGNED NOT NULL,
    invoice_number VARCHAR(50) NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    tax_amount DECIMAL(12,2) DEFAULT 0,
    total_amount DECIMAL(12,2) NOT NULL,
    status VARCHAR(50) DEFAULT 'draft',
    -- Statuses: draft, sent, paid, overdue, cancelled
    issued_date DATE NULL,
    due_date DATE NULL,
    paid_date DATE NULL,
    payment_method VARCHAR(50) NULL,
    notes TEXT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL,
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Migration: add_project_id_to_tasks_table
ALTER TABLE tasks ADD COLUMN project_id BIGINT UNSIGNED NULL AFTER category;
ALTER TABLE tasks ADD FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL;
```

### Key Features

1. **Client Directory** — all clients with their projects, total invoiced, total paid
2. **Project Board** — list of projects with status, budget, milestone progress bar
3. **Project Detail** — milestones checklist, linked tasks, linked invoices, team assigned
4. **Invoice Management** — create invoices, track sent/paid/overdue status
5. **Link tasks to projects** — existing tasks get an optional project_id dropdown
6. **Auto-create client from lead** — when lead stage changes to "won", prompt to create client + project

---

## MODULE 4: FINANCIAL INTELLIGENCE

### Purpose
Transform the basic given/received finance tracker into a proper P&L system with expense categories, salary auto-tracking, and goal monitoring.

### Database Changes

```sql
-- Migration: add_category_to_finances_table
ALTER TABLE finances ADD COLUMN category VARCHAR(100) NULL AFTER type;
-- Categories for 'given': salary, emi, office, subscription, rent, medical, house, misc
-- Categories for 'received': client_payment, maintenance, refund, other

ALTER TABLE finances ADD COLUMN venture VARCHAR(50) DEFAULT 'aurateria' AFTER category;
-- Ventures: aurateria, gicogifts, aigather, medical_ai, personal

ALTER TABLE finances ADD COLUMN is_recurring BOOLEAN DEFAULT FALSE;
ALTER TABLE finances ADD COLUMN recurring_day INT NULL; -- day of month for recurring

-- Migration: create_revenue_targets_table
CREATE TABLE revenue_targets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    month DATE NOT NULL,  -- first day of month
    target_amount DECIMAL(12,2) NOT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE(month)
);
```

### Key Features

1. **Monthly P&L Dashboard** — revenue, expenses broken by category, profit. Comparison with previous months.
2. **Expense Categories** — when adding a "given" transaction, select category. Pre-fill recurring expenses (salary, EMI, rent).
3. **Revenue by Client** — which clients are paying what, when.
4. **Revenue Target Tracking** — set monthly target, see progress bar, projected end-of-month based on current pace.
5. **Venture-wise P&L** — see Aurateria revenue vs GicoGifts vs AIGather separately.
6. **Cash Flow Forecast** — based on recurring expenses + pipeline estimated values.

---

## MODULE 5: VENTURES

### Purpose
Give each venture (GicoGifts, AIGather, Medical AI) its own dashboard card and basic tracking without building entire apps.

### Database Schema

```sql
-- Migration: create_ventures_table
CREATE TABLE ventures (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NULL,
    status VARCHAR(50) DEFAULT 'active',  -- active, paused, planned
    partner_name VARCHAR(255) NULL,
    partner_funded BOOLEAN DEFAULT FALSE,
    color VARCHAR(7) DEFAULT '#6C63FF',   -- hex color for UI
    icon VARCHAR(50) DEFAULT 'fa-rocket', -- Font Awesome icon
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Migration: create_venture_updates_table
CREATE TABLE venture_updates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    venture_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NULL,
    type VARCHAR(50) DEFAULT 'update', -- update, milestone, decision, blocker
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (venture_id) REFERENCES ventures(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### Key Features

1. **Venture Cards on Dashboard** — each venture shows: status, last update date, open tasks count, open blockers
2. **Venture Detail Page** — timeline of updates, linked projects, linked tasks, linked finance entries
3. **Quick Update** — add a text update to any venture (like a mini-journal for each venture)
4. **Seed Data** — pre-create: Aurateria (services), GicoGifts, AIGather, Medical AI Agents

---

## MODULE 6: MY DAY (Personal Productivity)

### Purpose
Replace the rigid morning/evening daily report with a flexible productivity system designed for someone with OCD — structured, predictable, calming.

### Database Schema

```sql
-- Migration: create_daily_focuses_table
CREATE TABLE daily_focuses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    date DATE NOT NULL,
    task_1_title VARCHAR(255) NULL,
    task_1_completed BOOLEAN DEFAULT FALSE,
    task_2_title VARCHAR(255) NULL,
    task_2_completed BOOLEAN DEFAULT FALSE,
    task_3_title VARCHAR(255) NULL,
    task_3_completed BOOLEAN DEFAULT FALSE,
    energy_level VARCHAR(20) NULL,  -- high, medium, low
    end_of_day_note TEXT NULL,
    wins TEXT NULL,  -- what went well
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE(user_id, date),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### Key Features (Super-Admin Only)

1. **My 3 Tasks** — every morning, pick 3 tasks (can link to existing tasks or type custom)
2. **Time Block View** — show today's schedule based on the 70/15/15 split (visual blocks)
3. **Quick Win Logger** — button to log a quick win/accomplishment during the day
4. **End of Day Reflection** — 3 things that went well, energy level check, note for tomorrow
5. **Streak Tracker** — how many consecutive days you've completed all 3 tasks

---

## MODULE 7: SIDEBAR RESTRUCTURE

### Current Sidebar (from layouts/admin.blade.php)
```
Dashboard
-- Administration --
  Users, Roles, Permissions
-- Task Management --
  My Personal Tasks, Assignments
-- Modules --
  Finance Contacts, All Transactions, Grocery,
  Holidays, Attendance, Daily Reports, Report Access
```

### New Sidebar
```
Command Center (dashboard)

-- Sales & Growth --
  Pipeline (leads.pipeline)
  All Leads (leads.index)
  Overdue Follow-ups (leads.overdue)

-- Clients & Projects --
  Clients (clients.index)
  Projects (projects.index)
  Invoices (invoices.index)

-- My Work --
  My Day (daily-focus)
  My Tasks (tasks.personal)
  Team Assignments (tasks.assignments)

-- Finance --
  Monthly P&L (finance.dashboard)
  Transactions (finance.index)
  Revenue Targets (revenue-targets.index)

-- Ventures --
  GicoGifts (ventures.show, slug=gicogifts)
  AIGather (ventures.show, slug=aigather)
  Medical AI (ventures.show, slug=medical-ai)

-- Team --
  Daily Reports (daily-reports.index)
  Attendance (attendance.index)
  Holidays (holidays.index)
  Report Access (daily-reports.manage)

-- Administration -- (super-admin only)
  Users, Roles, Permissions

-- Secondary -- (collapsed/bottom)
  Grocery (keep but deprioritize)
```

---

## MODULE 8: API ENDPOINTS FOR MOBILE APP

Every web controller should have a matching API controller. The existing API structure in routes/api.php already follows this pattern. For new modules, add:

```php
// CRM
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

// Clients
Route::middleware(['permission:view clients'])->group(function () {
    Route::get('clients', [ClientApiController::class, 'index']);
    Route::get('clients/{client}', [ClientApiController::class, 'show']);
});
Route::middleware(['permission:create clients'])->post('clients', [ClientApiController::class, 'store']);
Route::middleware(['permission:edit clients'])->put('clients/{client}', [ClientApiController::class, 'update']);

// Projects
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

// Invoices
Route::middleware(['permission:view invoices'])->get('invoices', [InvoiceApiController::class, 'index']);
Route::middleware(['permission:create invoices'])->post('invoices', [InvoiceApiController::class, 'store']);
Route::middleware(['permission:edit invoices'])->group(function () {
    Route::put('invoices/{invoice}', [InvoiceApiController::class, 'update']);
    Route::patch('invoices/{invoice}/status', [InvoiceApiController::class, 'updateStatus']);
});

// Ventures
Route::get('ventures', [VentureApiController::class, 'index']);
Route::get('ventures/{venture}', [VentureApiController::class, 'show']);
Route::post('ventures/{venture}/updates', [VentureApiController::class, 'addUpdate']);

// My Day
Route::get('daily-focus', [DailyFocusApiController::class, 'today']);
Route::post('daily-focus', [DailyFocusApiController::class, 'store']);
Route::put('daily-focus/{dailyFocus}', [DailyFocusApiController::class, 'update']);

// Financial Intelligence
Route::get('finance/dashboard', [FinanceDashboardApiController::class, 'index']);
Route::get('finance/pnl', [FinanceDashboardApiController::class, 'pnl']);
Route::apiResource('revenue-targets', RevenueTargetApiController::class);
```

---

## PERMISSIONS MASTER LIST (Update DatabaseSeeder)

Add these to the $modules array:

```php
$modules = [
    'users', 'roles',
    'finance', 'finance contacts',
    'tasks', 'task reports', 'task todos',
    'grocery', 'grocery templates', 'grocery expenses',
    'reports',
    'holidays', 'attendance', 'daily reports',
    // NEW
    'leads', 'lead activities',
    'clients',
    'projects', 'milestones',
    'invoices',
    'ventures', 'venture updates',
    'revenue targets',
];
```

Employee role additions:
```php
$employee->givePermissionTo([
    // existing
    'view tasks', 'create task reports', 'view task reports', 'create daily reports',
    // new
    'view leads', 'create leads', 'edit leads',
    'create lead activities',
    'view projects',
]);
```

---

## HR APP CONSOLIDATION PLAN (Future — After v2 Modules)

The HR app at hr.aurahq.online has features that overlap with AuraHQ. Long-term plan:

1. **Keep HR app running as-is for now** — it works for salary slips and check-in/check-out
2. **Do NOT rebuild it** — focus on revenue-generating modules first
3. **Phase 2 (post-October)**: migrate salary management and leave management into AuraHQ
4. **Phase 3**: retire hr.aurahq.online entirely, everything runs from aurahq.online

---

## IMPLEMENTATION NOTES FOR CLAUDE CODE / CURSOR

### Patterns to Follow (from existing codebase)

1. **Controller Middleware**: Use `HasMiddleware` interface with `Middleware` class (see TaskController.php)
2. **Model Convention**: `$guarded = []`, proper casts, relationship methods
3. **View Convention**: `@extends('layouts.admin')`, `@section('title')`, `@section('header')`, `@section('content')`
4. **Form Styling**: White card bg, rounded-xl, slate borders, Tailwind form classes
5. **Table Styling**: Clean tables with hover states, colored badges for statuses
6. **API Response**: Return `response()->json([...])` with proper HTTP codes
7. **Route Organization**: Group by middleware, use resource routes where possible
8. **Validation**: Inline in controller methods, not Form Request classes (existing pattern)

### Build Order Checklist

- [ ] Module 1: CEO Dashboard — modify existing files only
- [ ] Module 2: CRM — migrations, models, controller, views, routes, sidebar, API
- [ ] Module 3: Client Projects — migrations, models, controllers, views, routes, sidebar, API
- [ ] Module 4: Financial Intelligence — add category/venture to finances, new views, API
- [ ] Module 5: Ventures — migrations, models, controller, views, sidebar, seeder, API
- [ ] Module 6: My Day — migration, model, controller, view, sidebar, API
- [ ] Module 7: Sidebar Restructure — modify layouts/admin.blade.php
- [ ] Module 8: API Completeness — ensure all new modules have API controllers

### Testing Each Module

After building each module:
1. Run `php artisan migrate` — verify no errors
2. Run `php artisan db:seed` — verify new permissions are created
3. Login as super-admin — verify sidebar shows new items
4. Test CRUD operations — create, edit, view, delete
5. Test permission — login as employee, verify restricted access
6. Test API — use Postman/Insomnia to verify JSON endpoints

---

## SUMMARY

This blueprint transforms AuraHQ from an "employee compliance tool with grocery lists" into a "CEO command center that tracks revenue, pipeline, clients, ventures, and team productivity."

**Total new tables**: 9 (leads, lead_activities, clients, projects, milestones, invoices, ventures, venture_updates, daily_focuses, revenue_targets)
**Total modified tables**: 2 (tasks gets project_id, finances gets category + venture)
**Total new controllers**: ~12 (web + API for each module)
**Total new views**: ~20 blade templates
**Estimated build time**: 6-8 weeks if building one module per week

Start with Module 1 (dashboard) — it's the fastest win and immediately changes how you interact with the system every morning.
