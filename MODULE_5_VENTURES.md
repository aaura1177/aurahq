# MODULE 5: Ventures (GicoGifts, AIGather, Medical AI)

## Prerequisites
- Modules 1-4 built (dashboard, CRM, clients/projects, financial intelligence)

## What to Build
Dedicated tracking for each venture (GicoGifts, AIGather, Medical AI, Aurateria Services). Each venture gets its own page with updates, linked projects, and financial summary. Dashboard shows venture health cards.

## Files to Create

### Migrations
1. `create_ventures_table.php`
2. `create_venture_updates_table.php`

### Models
3. `app/Models/Venture.php`
4. `app/Models/VentureUpdate.php`

### Controllers
5. `app/Http/Controllers/VentureController.php`
6. `app/Http/Controllers/Api/VentureApiController.php`

### Views
7. `resources/views/ventures/index.blade.php` — all ventures overview
8. `resources/views/ventures/show.blade.php` — venture detail with updates + linked data

### Seeder
9. `database/seeders/VentureSeeder.php` — seed the 4 ventures

### Modify
10. `database/seeders/DatabaseSeeder.php` — add permissions + call VentureSeeder
11. `routes/web.php` + `routes/api.php`
12. `resources/views/layouts/admin.blade.php` — add Ventures sidebar section
13. `resources/views/dashboard.blade.php` — add venture health cards

---

## Migrations

### ventures table
```php
Schema::create('ventures', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug', 100)->unique();
    $table->text('description')->nullable();
    $table->string('status', 50)->default('active');  // active, paused, planned
    $table->string('partner_name')->nullable();
    $table->boolean('partner_funded')->default(false);
    $table->string('color', 7)->default('#6C63FF');
    $table->string('icon', 50)->default('fa-rocket');
    $table->timestamps();
});
```

### venture_updates table
```php
Schema::create('venture_updates', function (Blueprint $table) {
    $table->id();
    $table->foreignId('venture_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained();
    $table->string('title');
    $table->text('content')->nullable();
    $table->string('type', 50)->default('update');  // update, milestone, decision, blocker
    $table->timestamps();
});
```

## VentureSeeder

```php
namespace Database\Seeders;
use App\Models\Venture;
use Illuminate\Database\Seeder;

class VentureSeeder extends Seeder
{
    public function run(): void
    {
        $ventures = [
            [
                'name' => 'Aurateria Services',
                'slug' => 'aurateria',
                'description' => 'Core software services business — Laravel, DevOps, AI integration for clients.',
                'status' => 'active',
                'partner_funded' => false,
                'color' => '#6C63FF',
                'icon' => 'fa-code',
            ],
            [
                'name' => 'GicoGifts',
                'slug' => 'gicogifts',
                'description' => 'Premium hyper-local artisan gift boxes from Rajasthan. E-commerce brand.',
                'status' => 'active',
                'partner_name' => 'Partner',
                'partner_funded' => true,
                'color' => '#E67E22',
                'icon' => 'fa-gift',
            ],
            [
                'name' => 'AIGather',
                'slug' => 'aigather',
                'description' => 'AI tools marketplace and directory platform.',
                'status' => 'planned',
                'partner_name' => 'Partner',
                'partner_funded' => true,
                'color' => '#2980B9',
                'icon' => 'fa-robot',
            ],
            [
                'name' => 'Medical AI Agents',
                'slug' => 'medical-ai',
                'description' => 'AI customer support system for medical shops — call handling, WhatsApp queries, inventory checks.',
                'status' => 'planned',
                'partner_name' => 'Partner',
                'partner_funded' => true,
                'color' => '#2D8F4E',
                'icon' => 'fa-stethoscope',
            ],
        ];

        foreach ($ventures as $v) {
            Venture::firstOrCreate(['slug' => $v['slug']], $v);
        }
    }
}
```

Call from DatabaseSeeder: `$this->call(VentureSeeder::class);`

## Models

### Venture.php
- Relationships: `updates()` hasMany VentureUpdate (latest first), `projects()` — hasMany Project where venture = this slug
- Accessor: `statusColor`, `openTasksCount` (tasks via projects), `latestUpdate`
- Note: `projects()` relationship uses string matching since projects.venture is a string field. If projects table uses FK later, update this.

```php
public function projects()
{
    return $this->hasMany(\App\Models\Project::class, 'venture', 'slug');
}
```

### VentureUpdate.php
- Constants: `TYPES = ['update', 'milestone', 'decision', 'blocker']`
- Relationships: `venture()`, `user()`
- Accessor: `typeIcon`, `typeColor`

## VentureController

- `index()` — all ventures as cards. Each card shows: name, icon, status badge, partner info, last update date, open projects count, quick "Add Update" button
- `show($venture)` — find by slug or id. Show: venture info, updates timeline, linked projects list, financial summary (sum of Finance where venture = slug), open tasks via projects
- `addUpdate($venture)` — POST to add a VentureUpdate
- No CRUD for ventures themselves (managed via seeder/admin). Only updates are user-created.

## Routes

### web.php
```php
use App\Http\Controllers\VentureController;

Route::get('/ventures', [VentureController::class, 'index'])->name('ventures.index');
Route::get('/ventures/{venture:slug}', [VentureController::class, 'show'])->name('ventures.show');
Route::post('/ventures/{venture}/updates', [VentureController::class, 'addUpdate'])->name('ventures.updates.store');
```

### api.php
```php
Route::get('ventures', [VentureApiController::class, 'index']);
Route::get('ventures/{venture}', [VentureApiController::class, 'show']);
Route::post('ventures/{venture}/updates', [VentureApiController::class, 'addUpdate']);
```

## Sidebar
```html
@role('super-admin')
<li class="pt-4 pb-1 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Ventures</li>
@foreach(\App\Models\Venture::all() as $venture)
<li><a href="{{ route('ventures.show', $venture->slug) }}" class="group flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition"><i class="fas {{ $venture->icon }} w-5 text-center" style="color: {{ $venture->color }}"></i> {{ $venture->name }}</a></li>
@endforeach
@endrole
```

Note: To avoid N+1 on every page load, cache ventures or use a View Composer to share ventures with the layout.

## Dashboard Integration
Add venture health cards to the CEO dashboard (below the pipeline row or as a new row):
- 4 small cards, one per venture
- Each shows: venture name with colored icon, status badge, last update (relative time), open projects count
- Link to venture detail page

## Seeder Update
Add to `$modules`: `'ventures', 'venture updates'`

## Verification
1. `php artisan migrate` + `php artisan db:seed` — 4 ventures created
2. Sidebar shows all 4 ventures (super-admin only)
3. Click venture → shows detail page
4. Add an update → appears in timeline
5. Projects with matching venture string show in venture detail
6. Dashboard shows venture cards
