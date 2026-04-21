# MODULE 3: Clients, Projects & Invoices

## Prerequisites
- Module 2 (CRM) must be built first — clients can link to leads via `lead_id`

## Context
Same AuraHQ Laravel 12 app. Follow patterns from Module 2 (LeadController) and existing TaskController. Spatie Permission, HasMiddleware, Sanctum API, Blade + Tailwind + Alpine.js.

## What to Build
Client directory, project management with milestones, invoice tracking, and a link from CRM (lead → won → client).

## Files to Create

### Migrations
1. `create_clients_table.php`
2. `create_projects_table.php`
3. `create_milestones_table.php`
4. `create_invoices_table.php`
5. `add_project_id_to_tasks_table.php`

### Models
6. `app/Models/Client.php`
7. `app/Models/Project.php`
8. `app/Models/Milestone.php`
9. `app/Models/Invoice.php`

### Controllers (Web)
10. `app/Http/Controllers/ClientController.php`
11. `app/Http/Controllers/ProjectController.php`
12. `app/Http/Controllers/InvoiceController.php`

### Controllers (API)
13. `app/Http/Controllers/Api/ClientApiController.php`
14. `app/Http/Controllers/Api/ProjectApiController.php`
15. `app/Http/Controllers/Api/InvoiceApiController.php`

### Views
16. `resources/views/clients/index.blade.php` — client directory
17. `resources/views/clients/show.blade.php` — client detail with projects + invoices
18. `resources/views/clients/create.blade.php`
19. `resources/views/clients/edit.blade.php`
20. `resources/views/projects/index.blade.php` — all projects board
21. `resources/views/projects/show.blade.php` — project detail with milestones + tasks
22. `resources/views/projects/create.blade.php`
23. `resources/views/projects/edit.blade.php`
24. `resources/views/invoices/index.blade.php` — all invoices list
25. `resources/views/invoices/create.blade.php`
26. `resources/views/invoices/edit.blade.php`

### Modify
27. `database/seeders/DatabaseSeeder.php` — add permissions for clients, projects, milestones, invoices
28. `routes/web.php` — add routes
29. `routes/api.php` — add API routes
30. `resources/views/layouts/admin.blade.php` — add sidebar section
31. `resources/views/tasks/create.blade.php` — add optional project_id dropdown
32. `resources/views/tasks/edit.blade.php` — add optional project_id dropdown

---

## Migrations

### clients table
```php
Schema::create('clients', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('contact_person')->nullable();
    $table->string('email')->nullable();
    $table->string('phone', 50)->nullable();
    $table->string('company')->nullable();
    $table->foreignId('lead_id')->nullable()->constrained('leads')->nullOnDelete();
    $table->text('notes')->nullable();
    $table->boolean('is_active')->default(true);
    $table->foreignId('created_by')->constrained('users');
    $table->timestamps();
});
```

### projects table
```php
Schema::create('projects', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->foreignId('client_id')->constrained()->cascadeOnDelete();
    $table->text('description')->nullable();
    $table->string('venture', 50)->default('aurateria');
    $table->string('status', 50)->default('active');
    $table->decimal('budget', 12, 2)->nullable();
    $table->date('start_date')->nullable();
    $table->date('expected_end_date')->nullable();
    $table->date('actual_end_date')->nullable();
    $table->foreignId('created_by')->constrained('users');
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

### milestones table
```php
Schema::create('milestones', function (Blueprint $table) {
    $table->id();
    $table->foreignId('project_id')->constrained()->cascadeOnDelete();
    $table->string('title');
    $table->text('description')->nullable();
    $table->date('due_date')->nullable();
    $table->timestamp('completed_at')->nullable();
    $table->integer('sort_order')->default(0);
    $table->timestamps();
});
```

### invoices table
```php
Schema::create('invoices', function (Blueprint $table) {
    $table->id();
    $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('client_id')->constrained();
    $table->string('invoice_number', 50);
    $table->decimal('amount', 12, 2);
    $table->decimal('tax_amount', 12, 2)->default(0);
    $table->decimal('total_amount', 12, 2);
    $table->string('status', 50)->default('draft');
    $table->date('issued_date')->nullable();
    $table->date('due_date')->nullable();
    $table->date('paid_date')->nullable();
    $table->string('payment_method', 50)->nullable();
    $table->text('notes')->nullable();
    $table->foreignId('created_by')->constrained('users');
    $table->timestamps();
});
```

### add project_id to tasks
```php
Schema::table('tasks', function (Blueprint $table) {
    $table->foreignId('project_id')->nullable()->after('category')->constrained()->nullOnDelete();
});
```

## Models

### Client.php
- Relationships: `lead()` belongsTo Lead, `projects()` hasMany Project, `invoices()` hasMany Invoice, `creator()` belongsTo User
- Scopes: `active()`, search by name/company
- Accessor: `totalInvoiced` (sum of invoice total_amount), `totalPaid` (sum where status=paid)

### Project.php
- Constants: `STATUSES = ['planning', 'active', 'on_hold', 'completed', 'cancelled']`, `VENTURES = ['aurateria', 'gicogifts', 'aigather', 'medical_ai']`
- Relationships: `client()` belongsTo Client, `milestones()` hasMany Milestone (ordered by sort_order), `tasks()` hasMany Task, `invoices()` hasMany Invoice, `creator()` belongsTo User
- Accessor: `milestoneProgress` (completed milestones / total milestones * 100), `statusColor`

### Milestone.php
- Relationships: `project()` belongsTo Project
- Accessor: `isCompleted` (completed_at is not null), `isOverdue` (due_date past and not completed)

### Invoice.php
- Constants: `STATUSES = ['draft', 'sent', 'paid', 'overdue', 'cancelled']`
- Relationships: `project()` belongsTo Project, `client()` belongsTo Client, `creator()` belongsTo User
- Accessor: `statusColor`, `isPaid`, `isOverdue`
- Auto-generate `invoice_number`: "INV-YYYYMM-XXX" (padded sequence per month)

## Controllers

### ClientController
- `index()` — paginated list with search, active filter. Show: name, company, projects count, total invoiced, total paid
- `show($client)` — detail page: client info + projects list + invoices list + linked lead info
- CRUD: `create()`, `store()`, `edit()`, `update()`, `destroy()`
- On create form: if `?lead_id=X` in query string, pre-fill client name/email/phone from lead data

### ProjectController
- `index()` — all projects with filters: status, venture, client. Card view showing: name, client, venture badge, status badge, budget, milestone progress bar, date range
- `show($project)` — project detail: info card + milestones checklist + linked tasks + linked invoices
- CRUD standard
- `addMilestone($project)` — POST to add milestone
- `completeMilestone($milestone)` — PATCH to set completed_at
- `reorderMilestones($project)` — POST to update sort_order

### InvoiceController
- `index()` — all invoices with filters: status, client, date range. Table: invoice_number, client, project, amount, status badge, issued_date, due_date, paid_date
- CRUD standard
- `updateStatus($invoice)` — PATCH to change status. If status=paid, set paid_date=now(). If status=sent, set issued_date=now() if null.

## Seeder Update
Add to `$modules` array:
```php
'clients', 'projects', 'milestones', 'invoices'
```

Employee permissions — add:
```php
'view projects', 'view clients'
```

## Routes

### web.php
```php
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\InvoiceController;

// Clients & Projects
Route::resource('clients', ClientController::class);
Route::resource('projects', ProjectController::class);
Route::post('/projects/{project}/milestones', [ProjectController::class, 'addMilestone'])->name('projects.milestones.store');
Route::patch('/milestones/{milestone}/complete', [ProjectController::class, 'completeMilestone'])->name('milestones.complete');
Route::patch('/invoices/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('invoices.status');
Route::resource('invoices', InvoiceController::class);
```

### api.php — follow same permission middleware pattern as leads API from Module 2

## Sidebar Addition
Add "Clients & Projects" section after "Sales & CRM":
```html
<li class="pt-4 pb-1 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Clients & Projects</li>
@can('view clients')
<li><a href="{{ route('clients.index') }}" ...><i class="fas fa-building ..."></i> Clients</a></li>
@endcan
@can('view projects')
<li><a href="{{ route('projects.index') }}" ...><i class="fas fa-project-diagram ..."></i> Projects</a></li>
@endcan
@can('view invoices')
<li><a href="{{ route('invoices.index') }}" ...><i class="fas fa-file-invoice-dollar ..."></i> Invoices</a></li>
@endcan
```

## Dashboard Integration (Update Module 1 placeholders)
After building this module, go back to `DashboardController.php` and replace the Row 2 placeholders:
- "Active Clients" → `Client::where('is_active', true)->count()`
- "Pending Invoices" → `Invoice::whereIn('status', ['sent', 'overdue'])->sum('total_amount')`
- "Pipeline Value" → `Lead::active()->whereNotIn('stage', ['won', 'lost'])->sum('estimated_value')` (from Module 2)

## Task Form Update
In `resources/views/tasks/create.blade.php` and `edit.blade.php`, add an optional `project_id` dropdown:
```html
<div class="mb-4">
    <label class="block text-sm font-medium text-slate-700 mb-1">Project (optional)</label>
    <select name="project_id" class="w-full rounded-lg border-slate-300">
        <option value="">— No project —</option>
        @foreach($projects as $project)
            <option value="{{ $project->id }}" {{ old('project_id', $task->project_id ?? '') == $project->id ? 'selected' : '' }}>
                {{ $project->name }} ({{ $project->client->name }})
            </option>
        @endforeach
    </select>
</div>
```
Update TaskController `create()` and `edit()` to pass `$projects = Project::with('client')->where('is_active', true)->get();`

## Verification Checklist
1. `php artisan migrate` — 5 new tables + tasks column added
2. Create a client → appears in directory
3. Create a project under client → shows in project board
4. Add milestones → checklist works, complete toggles
5. Create invoice → status changes work (draft → sent → paid)
6. Link a task to a project → shows in project detail
7. Dashboard placeholders replaced with real data
8. Employee can view projects/clients but not edit
