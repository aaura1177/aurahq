# MODULE 2: CRM & Lead Pipeline

## Context
You are working on the AuraHQ Laravel 12 application.
- Stack: Laravel 12, Blade + Tailwind CSS, Alpine.js, Chart.js, Spatie Permission (`HasRoles`, `HasMiddleware` interface), Sanctum API
- Layout: `resources/views/layouts/admin.blade.php` — sidebar + main content area
- Existing patterns to follow:
  - Controllers: `app/Http/Controllers/TaskController.php` (uses `HasMiddleware` interface)
  - Models: `app/Models/Task.php` (uses `$guarded = []`, proper casts, relationships)
  - Views: extend `layouts.admin`, use `@section('title')`, `@section('header')`, `@section('content')`
  - Seeder: `database/seeders/DatabaseSeeder.php` has `$modules` array for auto-generating permissions
  - API: `app/Http/Controllers/Api/TaskApiController.php` pattern, routes in `routes/api.php`

## What to Build
A complete CRM module: leads table, lead activities (timeline), pipeline Kanban view, list view, overdue follow-ups, web + API.

## Files to Create

### Migrations
1. `database/migrations/YYYY_MM_DD_create_leads_table.php`
2. `database/migrations/YYYY_MM_DD_create_lead_activities_table.php`

### Models
3. `app/Models/Lead.php`
4. `app/Models/LeadActivity.php`

### Controllers
5. `app/Http/Controllers/LeadController.php`
6. `app/Http/Controllers/Api/LeadApiController.php`

### Views
7. `resources/views/leads/index.blade.php`
8. `resources/views/leads/pipeline.blade.php`
9. `resources/views/leads/show.blade.php`
10. `resources/views/leads/create.blade.php`
11. `resources/views/leads/edit.blade.php`
12. `resources/views/leads/overdue.blade.php`

### Files to Modify
13. `database/seeders/DatabaseSeeder.php` — add permissions
14. `routes/web.php` — add lead routes
15. `routes/api.php` — add API lead routes
16. `resources/views/layouts/admin.blade.php` — add sidebar section

---

## Step 1: Migration — leads table

```php
Schema::create('leads', function (Blueprint $table) {
    $table->id();
    $table->string('business_name');
    $table->string('contact_person')->nullable();
    $table->string('phone', 50)->nullable();
    $table->string('email')->nullable();
    $table->string('website')->nullable();
    $table->string('industry', 100)->nullable();
    $table->string('city', 100)->nullable();
    $table->string('source', 100)->nullable();
    $table->string('stage', 50)->default('prospect');
    $table->decimal('estimated_value', 12, 2)->nullable();
    $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
    $table->text('notes')->nullable();
    $table->string('lost_reason')->nullable();
    $table->date('next_follow_up')->nullable();
    $table->timestamp('last_contacted_at')->nullable();
    $table->timestamp('converted_at')->nullable();
    $table->foreignId('created_by')->constrained('users');
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

## Step 2: Migration — lead_activities table

```php
Schema::create('lead_activities', function (Blueprint $table) {
    $table->id();
    $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained();
    $table->string('type', 50);
    $table->text('description');
    $table->json('metadata')->nullable();
    $table->timestamps();
});
```

## Step 3: Models

### Lead.php
```php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = [
        'is_active' => 'boolean',
        'estimated_value' => 'decimal:2',
        'next_follow_up' => 'date',
        'last_contacted_at' => 'datetime',
        'converted_at' => 'datetime',
    ];

    // Stages constant for reuse
    const STAGES = ['prospect', 'contacted', 'discovery_call', 'proposal_sent', 'negotiation', 'won', 'lost'];
    const SOURCES = ['linkedin', 'upwork', 'whatsapp', 'referral', 'walk_in', 'facebook', 'website', 'other'];
    const INDUSTRIES = ['clinic', 'restaurant', 'hotel', 'gym', 'real_estate', 'coaching', 'retail', 'ecommerce', 'saas', 'manufacturing', 'other'];

    public function assignee() { return $this->belongsTo(User::class, 'assigned_to'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function activities() { return $this->hasMany(LeadActivity::class)->latest(); }

    // Scopes
    public function scopeOverdue($query) {
        return $query->whereNotNull('next_follow_up')
            ->where('next_follow_up', '<', now())
            ->whereNotIn('stage', ['won', 'lost']);
    }
    public function scopeByStage($query, $stage) {
        return $query->where('stage', $stage);
    }
    public function scopeActive($query) {
        return $query->where('is_active', true);
    }

    // Helpers
    public function isOverdue(): bool {
        return $this->next_follow_up && $this->next_follow_up->isPast() && !in_array($this->stage, ['won', 'lost']);
    }

    public function getStageLabelAttribute(): string {
        return str_replace('_', ' ', ucfirst($this->stage));
    }

    public function getStageColorAttribute(): string {
        return match($this->stage) {
            'prospect' => 'slate',
            'contacted' => 'blue',
            'discovery_call' => 'cyan',
            'proposal_sent' => 'purple',
            'negotiation' => 'orange',
            'won' => 'green',
            'lost' => 'red',
            default => 'slate',
        };
    }
}
```

### LeadActivity.php
```php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LeadActivity extends Model
{
    protected $guarded = [];
    protected $casts = ['metadata' => 'array'];

    const TYPES = ['note', 'call', 'whatsapp', 'email', 'meeting', 'follow_up', 'stage_change', 'proposal_sent'];

    public function lead() { return $this->belongsTo(Lead::class); }
    public function user() { return $this->belongsTo(User::class); }

    public function getTypeIconAttribute(): string {
        return match($this->type) {
            'call' => 'fa-phone',
            'whatsapp' => 'fa-brands fa-whatsapp',
            'email' => 'fa-envelope',
            'meeting' => 'fa-handshake',
            'stage_change' => 'fa-arrow-right',
            'proposal_sent' => 'fa-file-alt',
            'follow_up' => 'fa-clock',
            default => 'fa-sticky-note',
        };
    }

    public function getTypeColorAttribute(): string {
        return match($this->type) {
            'call' => 'blue',
            'whatsapp' => 'green',
            'email' => 'purple',
            'meeting' => 'cyan',
            'stage_change' => 'orange',
            'proposal_sent' => 'indigo',
            'follow_up' => 'amber',
            default => 'slate',
        };
    }
}
```

## Step 4: LeadController.php

```php
namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Carbon\Carbon;

class LeadController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view leads', only: ['index', 'pipeline', 'show', 'overdue']),
            new Middleware('permission:create leads', only: ['create', 'store']),
            new Middleware('permission:edit leads', only: ['edit', 'update', 'updateStage']),
            new Middleware('permission:delete leads', only: ['destroy']),
            new Middleware('permission:create lead activities', only: ['addActivity']),
        ];
    }

    public function index(Request $request) { /* ... */ }
    public function pipeline() { /* ... */ }
    public function show(Lead $lead) { /* ... */ }
    public function create() { /* ... */ }
    public function store(Request $request) { /* ... */ }
    public function edit(Lead $lead) { /* ... */ }
    public function update(Request $request, Lead $lead) { /* ... */ }
    public function updateStage(Request $request, Lead $lead) { /* ... */ }
    public function addActivity(Request $request, Lead $lead) { /* ... */ }
    public function overdue() { /* ... */ }
    public function destroy(Lead $lead) { /* ... */ }
}
```

### Detailed method logic:

**index():**
- Accept filters: `stage`, `industry`, `source`, `assigned_to`, `city`, `search` (business_name/contact_person)
- Query `Lead::with('assignee')->active()`, apply filters, `latest()->paginate(25)`
- Pass filters, leads, users (for filter dropdown), stage/industry/source constants
- Show summary stats at top: total leads, total pipeline value (exclude won/lost), conversion rate

**pipeline():**
- Group leads by stage: `Lead::active()->get()->groupBy('stage')`
- For each stage, calculate count and sum of estimated_value
- Order stages in the defined constant order
- Pass to a Kanban-style view

**show($lead):**
- Load `$lead->load(['activities.user', 'assignee', 'creator'])`
- Show lead details card at top
- Activity timeline below (vertical timeline, newest first)
- Right sidebar: quick stats (days since created, total activities, days since last contact)
- Quick action buttons: "Log Call", "Log WhatsApp", "Add Note", "Change Stage" (dropdown)

**store():**
- Validate: `business_name` required, others nullable, `stage` must be in Lead::STAGES
- Set `created_by = auth()->id()`
- If `next_follow_up` is null and stage is not won/lost, auto-set to 4 days from now
- Auto-create LeadActivity: type='note', description='Lead created'
- Redirect to leads.show

**updateStage($lead):**
- Validate: `stage` required, must be in Lead::STAGES
- Get old stage for activity metadata
- Update lead stage
- If new stage is 'won', set `converted_at = now()`
- If new stage is 'lost', require `lost_reason`
- If `next_follow_up` is null and new stage not in ['won', 'lost'], set to 4 days from now
- Auto-create LeadActivity: type='stage_change', metadata={from: old, to: new}
- Set `last_contacted_at = now()`
- Redirect back with success

**addActivity($lead):**
- Validate: `type` required (must be in LeadActivity::TYPES), `description` required
- Create LeadActivity with `user_id = auth()->id()`
- Update lead's `last_contacted_at = now()`
- Redirect back

**overdue():**
- `Lead::with('assignee')->active()->overdue()->orderBy('next_follow_up')->get()`
- Same table view as index but pre-filtered

## Step 5: Views

### leads/index.blade.php
- Extend `layouts.admin`, title "All Leads", header "Lead Management"
- Top bar: filter dropdowns (stage, industry, source, assigned_to) + search input + "Add Lead" button
- Summary row: X total leads | ₹Y pipeline value | Z% conversion rate
- Table columns: Business Name (link to show), Contact Person, Phone, Stage (colored badge), Industry, Est. Value, Follow-up Date (red if overdue), Assigned To, Actions (edit, delete)
- Pagination at bottom
- Stage badges use Tailwind colors: `bg-{stageColor}-100 text-{stageColor}-700` (use inline conditionals)

### leads/pipeline.blade.php
- Extend `layouts.admin`, title "Pipeline", header "Sales Pipeline"
- Top: total pipeline value (excluding won/lost), lead count
- Grid: `grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4` (show prospect through negotiation; won/lost as smaller sections below or collapsed)
- Each column: header with stage name + count + value sum
- Each lead card in column: business_name (bold), estimated_value, next_follow_up (red if overdue), assigned_to initials avatar
- Each card has a "Move to" dropdown (form with POST to leads.stage route) — use Alpine.js for the dropdown
- Alternative: simple stage change buttons instead of drag-and-drop (more reliable)

### leads/show.blade.php
- Extend `layouts.admin`, title "{lead.business_name}", header "Lead Details"
- Left section (2/3 width):
  - Lead info card: business_name, contact_person, phone, email, website, industry, city, source, stage (large colored badge), estimated_value, notes
  - Quick action row: buttons for "Log Call", "Log WhatsApp", "Log Email", "Add Note", "Log Meeting" — each opens a small Alpine.js form/modal that POSTs to leads.activity
  - Activity Timeline: vertical timeline with colored dots per type, each showing: type icon, description, user name, relative time (diffForHumans). Newest at top.
- Right sidebar (1/3 width):
  - Stage: current stage with "Change Stage" dropdown (form POST to leads.stage)
  - Quick Stats: days since created, total activities, days since last contact, assigned to
  - Follow-up: next_follow_up date with "Update" button
  - If stage is won: "Create Client" button (link to clients.create with lead_id — this will work once Module 3 exists; for now just show the button disabled)

### leads/create.blade.php and leads/edit.blade.php
- Standard form matching existing form styling (see tasks/create.blade.php for reference)
- Fields: business_name (required), contact_person, phone, email, website, industry (dropdown from Lead::INDUSTRIES), city, source (dropdown from Lead::SOURCES), stage (dropdown from Lead::STAGES, default 'prospect' on create), estimated_value, assigned_to (dropdown of active users), next_follow_up (date picker), notes (textarea)
- assigned_to should default to current user on create

### leads/overdue.blade.php
- Same layout as index but with header "Overdue Follow-ups" and pre-filtered data
- Add urgency indicator: days overdue in red

## Step 6: Update DatabaseSeeder.php

In the `$modules` array, ADD:
```php
$modules = ['users', 'roles', 'finance', 'finance contacts', 'tasks', 'grocery', 'grocery templates', 'grocery expenses', 'reports', 'task reports', 'task todos', 'holidays', 'attendance', 'daily reports', 'leads', 'lead activities'];
```

In employee permissions, ADD:
```php
$employee->givePermissionTo([
    // existing
    'view tasks', 'create task reports', 'view task reports', 'create daily reports',
    // new — Aman needs these for BD work
    'view leads', 'create leads', 'edit leads',
    'create lead activities',
]);
```

## Step 7: Add Routes

### In routes/web.php (inside the `Route::middleware(['auth'])` group):

```php
use App\Http\Controllers\LeadController;

// CRM / Leads
Route::get('/leads/pipeline', [LeadController::class, 'pipeline'])->name('leads.pipeline');
Route::get('/leads/overdue', [LeadController::class, 'overdue'])->name('leads.overdue');
Route::patch('/leads/{lead}/stage', [LeadController::class, 'updateStage'])->name('leads.stage');
Route::post('/leads/{lead}/activity', [LeadController::class, 'addActivity'])->name('leads.activity');
Route::resource('leads', LeadController::class);
```

IMPORTANT: Place the named routes (pipeline, overdue) BEFORE `Route::resource('leads', ...)` to avoid route conflicts.

### In routes/api.php (inside the `auth:sanctum` middleware group):

```php
use App\Http\Controllers\Api\LeadApiController;

// Leads API
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
```

## Step 8: Add to Sidebar

In `resources/views/layouts/admin.blade.php`, ADD a new section AFTER the "Task Management" section and BEFORE the "Modules" section:

```html
{{-- Sales & CRM --}}
@if(auth()->user()->can('view leads'))
<li class="pt-4 pb-1 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Sales & CRM</li>

<li><a href="{{ route('leads.pipeline') }}" class="group flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition"><i class="fas fa-columns w-5 text-center group-hover:text-indigo-400"></i> Pipeline</a></li>

<li><a href="{{ route('leads.index') }}" class="group flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition"><i class="fas fa-user-plus w-5 text-center group-hover:text-blue-400"></i> All Leads</a></li>

<li><a href="{{ route('leads.overdue') }}" class="group flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition"><i class="fas fa-clock w-5 text-center group-hover:text-red-400"></i> Overdue Follow-ups</a></li>
@endif
```

## Step 9: API Controller

Create `app/Http/Controllers/Api/LeadApiController.php` mirroring LeadController but returning JSON responses:
- `index()` → return paginated leads as JSON
- `pipeline()` → return leads grouped by stage with counts/sums
- `show($lead)` → return lead with activities
- `store()` → validate, create, return 201
- `update()` → validate, update, return 200
- `updateStage()` → same logic as web, return JSON
- `addActivity()` → create activity, return 201
- `overdue()` → return overdue leads
- `destroy()` → delete, return 204

Follow the exact same pattern as `app/Http/Controllers/Api/TaskApiController.php`.

## After Building — Verification Checklist
1. `php artisan migrate` — no errors
2. `php artisan db:seed --class=DatabaseSeeder` — new permissions created (you may need to clear permission cache: `php artisan permission:cache-reset`)
3. Login as super-admin → sidebar shows "Sales & CRM" section
4. Create a lead → appears in list and pipeline
5. Change stage → activity auto-created in timeline
6. Set next_follow_up to yesterday → appears in overdue
7. Login as employee → can see leads (if Aman's user has employee role)
8. API: `POST /api/login` then `GET /api/leads` returns JSON
