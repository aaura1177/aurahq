# MODULE 6: My Day (Personal Productivity for CEO)

## Prerequisites
- Modules 1-5 built

## What to Build
A personal productivity tool for the super-admin (Ethan). Based on the "3-Task Rule" — every morning pick 3 tasks, track completion, end-of-day reflection. Designed for someone with OCD: structured, predictable, calming.

This is SEPARATE from Daily Reports (which are for employees). My Day is only for super-admin.

## Files to Create

### Migration
1. `create_daily_focuses_table.php`

### Model
2. `app/Models/DailyFocus.php`

### Controllers
3. `app/Http/Controllers/DailyFocusController.php`
4. `app/Http/Controllers/Api/DailyFocusApiController.php`

### Views
5. `resources/views/daily-focus/today.blade.php` — main "My Day" view
6. `resources/views/daily-focus/history.blade.php` — past days review

### Modify
7. `routes/web.php` + `routes/api.php`
8. `resources/views/layouts/admin.blade.php` — add sidebar item

---

## Migration

```php
Schema::create('daily_focuses', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained();
    $table->date('date');

    // The 3 tasks
    $table->string('task_1_title')->nullable();
    $table->boolean('task_1_completed')->default(false);
    $table->foreignId('task_1_id')->nullable()->constrained('tasks')->nullOnDelete();

    $table->string('task_2_title')->nullable();
    $table->boolean('task_2_completed')->default(false);
    $table->foreignId('task_2_id')->nullable()->constrained('tasks')->nullOnDelete();

    $table->string('task_3_title')->nullable();
    $table->boolean('task_3_completed')->default(false);
    $table->foreignId('task_3_id')->nullable()->constrained('tasks')->nullOnDelete();

    // Reflection
    $table->string('energy_level', 20)->nullable();  // high, medium, low
    $table->text('end_of_day_note')->nullable();
    $table->text('wins')->nullable();  // what went well
    $table->text('tomorrow_focus')->nullable();

    $table->timestamps();
    $table->unique(['user_id', 'date']);
});
```

## Model — DailyFocus.php

```php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DailyFocus extends Model
{
    protected $guarded = [];
    protected $casts = [
        'date' => 'date',
        'task_1_completed' => 'boolean',
        'task_2_completed' => 'boolean',
        'task_3_completed' => 'boolean',
    ];

    const ENERGY_LEVELS = ['high', 'medium', 'low'];

    public function user() { return $this->belongsTo(User::class); }
    public function task1() { return $this->belongsTo(Task::class, 'task_1_id'); }
    public function task2() { return $this->belongsTo(Task::class, 'task_2_id'); }
    public function task3() { return $this->belongsTo(Task::class, 'task_3_id'); }

    public function getCompletedCountAttribute(): int {
        return ($this->task_1_completed ? 1 : 0) + ($this->task_2_completed ? 1 : 0) + ($this->task_3_completed ? 1 : 0);
    }

    public function getAllCompletedAttribute(): bool {
        return $this->task_1_completed && $this->task_2_completed && $this->task_3_completed;
    }

    // Streak: consecutive days with all 3 completed
    public static function currentStreak(int $userId): int {
        $streak = 0;
        $date = now()->subDay(); // start from yesterday (today might be in progress)
        while (true) {
            $focus = static::where('user_id', $userId)->where('date', $date->format('Y-m-d'))->first();
            if ($focus && $focus->all_completed) {
                $streak++;
                $date->subDay();
            } else {
                break;
            }
        }
        return $streak;
    }
}
```

## DailyFocusController

### today() — GET /my-day
The main view. Super-admin only.

```php
public function today()
{
    $user = auth()->user();
    if (!$user->hasRole('super-admin')) abort(403);

    $today = now()->format('Y-m-d');
    $focus = DailyFocus::firstOrCreate(
        ['user_id' => $user->id, 'date' => $today],
        ['user_id' => $user->id, 'date' => $today]
    );

    // Get available tasks for dropdown (user's personal tasks + assigned tasks, active, not completed)
    $availableTasks = Task::where('is_active', true)
        ->where('status', '!=', 'completed')
        ->where(function ($q) use ($user) {
            $q->where('created_by', $user->id)->orWhere('assigned_to', $user->id);
        })
        ->orderBy('priority', 'desc')
        ->orderBy('due_date')
        ->get();

    $streak = DailyFocus::currentStreak($user->id);

    // Yesterday's focus for reference
    $yesterday = DailyFocus::where('user_id', $user->id)
        ->where('date', now()->subDay()->format('Y-m-d'))
        ->first();

    return view('daily-focus.today', compact('focus', 'availableTasks', 'streak', 'yesterday'));
}
```

### update($focus) — PUT /my-day/{focus}
Update task titles, completion status, reflection fields. Use Alpine.js for inline editing on the frontend.

### history() — GET /my-day/history
Show past 30 days of DailyFocus entries. Calendar-style or list. Color code: green (3/3), yellow (1-2/3), red (0/3 or no entry).

## View — daily-focus/today.blade.php

This should feel calm and focused. Minimal UI. No clutter.

### Layout:
```
┌─────────────────────────────────────────────┐
│  Good morning, Ethan.        🔥 5-day streak │
│  Today is Monday, April 21, 2026            │
├─────────────────────────────────────────────┤
│                                             │
│  YOUR 3 TASKS TODAY                         │
│                                             │
│  ☐ 1. [Task title or dropdown to select]    │
│  ☐ 2. [Task title or dropdown to select]    │
│  ☐ 3. [Task title or dropdown to select]    │
│                                             │
├─────────────────────────────────────────────┤
│                                             │
│  TIME BLOCKS (read-only schedule reference) │
│  9:00-9:15  | Morning setup                 │
│  9:15-10:15 | Outreach (Aurateria Sales)    │
│  10:15-12:30| Deep Work (Aurateria)         │
│  ... (the daily schedule from master plan)  │
│                                             │
├─────────────────────────────────────────────┤
│                                             │
│  END OF DAY (fill in evening)               │
│                                             │
│  Energy: [High] [Medium] [Low]              │
│  What went well today:  [textarea]          │
│  Notes for tomorrow:    [textarea]          │
│                                             │
└─────────────────────────────────────────────┘
```

### Implementation Details:

**Task Selection:**
- If task_X_title is empty, show a dropdown of available tasks + a "Custom task" text input
- If task_X_title is set, show it with a checkbox for completion
- Clicking the checkbox sends an AJAX PATCH (or form submit) to toggle task_X_completed
- Use Alpine.js for the toggle behavior

**Streak Display:**
- Show a fire emoji + streak count
- If streak > 0, show in orange/red
- If streak is 0, show "Start your streak today!"

**Time Blocks:**
- Static/read-only reference block showing the daily schedule
- Hardcode the schedule from the master plan (or store in config):
  ```
  9:00-9:15   Morning Report + Plan
  9:15-10:15  Outreach (Aurateria Sales)
  10:15-12:30 Deep Work (Aurateria Delivery)
  12:30-1:30  Lunch + English Practice
  1:30-2:00   Main Client
  2:00-4:30   Deep Work (Aurateria Delivery)
  4:30-5:00   Break + Team Check-in
  5:00-5:30   Outreach Block 2
  5:30-6:00   Main Client
  6:00-7:00   Partner Projects
  7:00-7:15   Evening Report
  ```
- Color-code by track: green=Aurateria, blue=Main Client, purple=Partner Projects, gray=breaks

**End of Day Section:**
- Energy level: 3 buttons (High/Medium/Low) — use Alpine.js to toggle active state
- Textareas for wins and tomorrow_focus
- Save button at bottom

**Yesterday's Reference:**
- Small collapsible section: "Yesterday: 2/3 completed. Focus was: [task titles]"
- If yesterday had a tomorrow_focus note, show it as a suggestion

### Styling:
- Calm, minimal design. White background, generous padding, soft shadows
- No aggressive colors. Use slate-600 for text, subtle borders
- Checkboxes should be satisfying to click (larger, with a smooth transition)
- The streak counter should be the only "gamification" element

## Routes

### web.php
```php
use App\Http\Controllers\DailyFocusController;

Route::middleware('role:super-admin')->group(function () {
    Route::get('/my-day', [DailyFocusController::class, 'today'])->name('daily-focus.today');
    Route::put('/my-day/{dailyFocus}', [DailyFocusController::class, 'update'])->name('daily-focus.update');
    Route::get('/my-day/history', [DailyFocusController::class, 'history'])->name('daily-focus.history');
});
```

### api.php
```php
Route::middleware('role:super-admin')->group(function () {
    Route::get('daily-focus', [DailyFocusApiController::class, 'today']);
    Route::post('daily-focus', [DailyFocusApiController::class, 'store']);
    Route::put('daily-focus/{dailyFocus}', [DailyFocusApiController::class, 'update']);
    Route::get('daily-focus/history', [DailyFocusApiController::class, 'history']);
});
```

## Sidebar
Add "My Day" as the FIRST item in the "My Work" section, with a special icon:

```html
@role('super-admin')
<li class="pt-4 pb-1 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">My Work</li>
<li><a href="{{ route('daily-focus.today') }}" class="group flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition"><i class="fas fa-sun w-5 text-center group-hover:text-amber-400"></i> My Day</a></li>
@endrole
```

## Verification
1. Login as super-admin → "My Day" appears in sidebar
2. Click → today's page loads with empty 3 tasks
3. Select tasks from dropdown → saves correctly
4. Check off tasks → completion toggles
5. Fill end-of-day reflection → saves
6. Next day → new empty focus, yesterday's data shown as reference
7. Complete 3/3 for 3 days → streak shows 3
8. Login as employee → no access to My Day (403)
