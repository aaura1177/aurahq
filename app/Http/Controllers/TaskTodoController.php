<?php
namespace App\Http\Controllers;
use App\Models\Task;
use App\Models\TaskTodo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class TaskTodoController extends Controller implements HasMiddleware
{
    public static function middleware(): array {
        return [
            new Middleware('permission:create task todos', only: ['store']),
            new Middleware('permission:edit task todos', only: ['update', 'updateStatus']),
            new Middleware('permission:delete task todos', only: ['destroy']),
        ];
    }

    public function store(Request $request, Task $task) {
        $request->validate(['title' => 'required']);
        TaskTodo::create([
            'task_id' => $task->id,
            'title' => $request->title,
            'status' => 'pending'
        ]);
        return back()->with('success', 'Todo added.');
    }

    public function update(Request $request, TaskTodo $todo) {
        $request->validate(['title' => 'required']);
        $todo->update(['title' => $request->title]);
        return back()->with('success', 'Todo updated.');
    }

    public function updateStatus(Request $request, TaskTodo $todo) {
        $request->validate(['status' => 'required|in:pending,in_progress,done']);
        $todo->update(['status' => $request->status]);
        return back()->with('success', 'Todo status updated.');
    }

    public function destroy(TaskTodo $todo) {
        $todo->delete();
        return back()->with('success', 'Todo deleted.');
    }
}
