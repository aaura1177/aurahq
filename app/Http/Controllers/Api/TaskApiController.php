<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiJson;
use App\Models\Task;
use App\Models\TaskReport;
use App\Models\TaskTodo;
use App\Models\User;
use Illuminate\Http\Request;

class TaskApiController extends Controller
{
    public function personal(Request $request)
    {
        $user = $request->user();
        $query = Task::where('category', 'admin_personal')->where('created_by', $user->id)->where('is_active', true);
        $filter = $request->query('filter', 'daily');
        if ($filter === 'daily') $query->where('frequency', 'daily');
        elseif ($filter === 'top_five') $query->where('frequency', 'top_five');
        elseif ($filter === 'urgent') $query->whereIn('priority', ['urgent', 'critical']);
        elseif ($filter !== 'all') $query->where('frequency', $filter);
        $tasks = $query->latest()->get()->map(fn ($t) => $this->taskToArray($t));

        return ApiJson::ok($tasks->values()->all());
    }

    public function assignments(Request $request)
    {
        $user = $request->user();
        $query = Task::where('category', 'employee_assignment')->with('assignee')->where('is_active', true);
        if (!$user->hasRole(['super-admin', 'admin'])) {
            $query->where('assigned_to', $user->id);
        }
        if ($request->filled('employee_id')) $query->where('assigned_to', $request->employee_id);
        if ($request->filled('status')) $query->where('status', $request->status);
        $tasks = $query->latest()->get()->map(fn ($t) => $this->taskToArray($t));
        $employees = $user->hasRole(['super-admin', 'admin'])
            ? User::role('employee')->where('is_active', true)->get(['id', 'name'])->values()->all()
            : [];

        return ApiJson::ok([
            'tasks' => $tasks->values()->all(),
            'employees' => $employees,
        ]);
    }

    public function show(Request $request, Task $task)
    {
        $user = $request->user();
        if (! $task->is_active && ! $user->hasRole(['super-admin', 'admin'])) {
            return ApiJson::unauthorized();
        }
        if ($task->assigned_to != $user->id && ! $user->hasRole(['super-admin', 'admin']) && $task->category !== 'admin_personal') {
            return ApiJson::unauthorized();
        }
        $task->load(['reports.user', 'assignee', 'creator', 'todos']);

        return ApiJson::ok($this->taskToArray($task, true));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|in:low,normal,high,urgent,critical',
            'status' => 'nullable|in:pending,in_progress,completed',
            'category' => 'nullable|in:admin_personal,employee_assignment',
            'frequency' => 'nullable|string|max:50',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
        ]);
        $data = $request->only('title', 'description', 'priority', 'status', 'category', 'frequency', 'assigned_to', 'due_date');
        $data['created_by'] = $request->user()->id;
        $data['status'] = $data['status'] ?? 'pending';
        $data['category'] = $data['category'] ?? 'employee_assignment';
        $task = Task::create($data);
        return ApiJson::created($this->taskToArray($task), 'Task created successfully');
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|in:low,normal,high,urgent,critical',
            'status' => 'nullable|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
        ]);
        $task->update($request->only('title', 'description', 'priority', 'status', 'due_date'));
        return ApiJson::ok($this->taskToArray($task), 'Updated');
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return ApiJson::noContent();
    }

    public function toggleStatus(Task $task)
    {
        $task->is_active = !$task->is_active;
        $task->save();
        return ApiJson::ok(['is_active' => $task->is_active], 'Updated');
    }

    private function taskToArray(Task $task, bool $full = false): array
    {
        $arr = [
            'id' => $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'priority' => $task->priority,
            'status' => $task->status,
            'category' => $task->category,
            'frequency' => $task->frequency,
            'due_date' => $task->due_date?->format('Y-m-d'),
            'is_active' => $task->is_active,
            'assignee' => $task->assignee ? ['id' => $task->assignee->id, 'name' => $task->assignee->name] : null,
        ];
        if ($full && $task->relationLoaded('reports')) {
            $arr['reports'] = $task->reports->map(fn ($r) => [
                'id' => $r->id,
                'remark' => $r->remark ?? '',
                'time_note' => $r->time_note ?? '',
                'user' => $r->user ? ['id' => $r->user->id, 'name' => $r->user->name] : null,
            ]);
        }
        if ($full && $task->relationLoaded('todos')) {
            $arr['todos'] = $task->todos->map(fn ($t) => ['id' => $t->id, 'title' => $t->title, 'status' => $t->status]);
        }
        return $arr;
    }

    public function storeReport(Request $request, Task $task)
    {
        if ($request->user()->id != $task->assigned_to && !$request->user()->hasRole(['super-admin', 'admin'])) {
            return ApiJson::unauthorized();
        }
        $request->validate(['remark' => 'required|string', 'time_note' => 'nullable|string', 'status' => 'nullable|in:pending,in_progress,completed']);
        TaskReport::create([
            'task_id' => $task->id,
            'user_id' => $request->user()->id,
            'remark' => $request->remark,
            'time_note' => $request->time_note,
        ]);
        if ($request->filled('status')) {
            $task->update(['status' => $request->status]);
        }
        return ApiJson::created(['task_id' => $task->id], 'Report added successfully');
    }

    public function updateReport(Request $request, TaskReport $report)
    {
        if ($request->user()->id != $report->user_id && !$request->user()->hasRole(['super-admin', 'admin'])) {
            return ApiJson::unauthorized();
        }
        $request->validate(['remark' => 'required|string', 'time_note' => 'nullable|string']);
        $report->update($request->only('remark', 'time_note'));

        return ApiJson::ok(['id' => $report->id], 'Updated');
    }

    public function deleteReport(Request $request, TaskReport $report)
    {
        if ($request->user()->id != $report->user_id && !$request->user()->hasRole(['super-admin', 'admin'])) {
            return ApiJson::unauthorized();
        }
        $report->delete();

        return ApiJson::noContent();
    }

    public function storeTodo(Request $request, Task $task)
    {
        $request->validate(['title' => 'required|string|max:255']);
        $todo = TaskTodo::create(['task_id' => $task->id, 'title' => $request->title, 'status' => 'pending']);
        return ApiJson::created(['id' => $todo->id], 'Todo created successfully');
    }

    public function updateTodo(Request $request, TaskTodo $todo)
    {
        $request->validate(['title' => 'required|string|max:255']);
        $todo->update(['title' => $request->title]);

        return ApiJson::ok(['id' => $todo->id], 'Updated');
    }

    public function updateTodoStatus(Request $request, TaskTodo $todo)
    {
        $request->validate(['status' => 'required|in:pending,in_progress,done']);
        $todo->update(['status' => $request->status]);

        return ApiJson::ok(['id' => $todo->id, 'status' => $todo->status], 'Updated');
    }

    public function destroyTodo(TaskTodo $todo)
    {
        $todo->delete();

        return ApiJson::noContent();
    }

    public function reviewTask(Request $request, Task $task)
    {
        if (!$request->user()->hasRole(['super-admin', 'admin'])) {
            return ApiJson::unauthorized();
        }
        $request->validate([
            'rating' => 'nullable|integer|min:1|max:5',
            'admin_remark' => 'nullable|string',
            'admin_private_note' => 'nullable|string',
        ]);
        $task->update([
            'status' => 'reviewed',
            'rating' => $request->rating,
            'admin_remark' => $request->admin_remark,
            'admin_private_note' => $request->admin_private_note,
        ]);
        return ApiJson::ok([
            'id' => $task->id,
            'status' => $task->status,
            'rating' => $task->rating,
        ], 'Review saved');
    }
}
