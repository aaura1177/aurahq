<?php
namespace App\Http\Controllers;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskReport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Carbon\Carbon;

class TaskController extends Controller implements HasMiddleware
{
    public static function middleware(): array {
        return [
            new Middleware('permission:view tasks', only: ['index', 'personal', 'assignments', 'show']),
            new Middleware('permission:create tasks', only: ['create', 'store']),
            new Middleware('permission:edit tasks', only: ['edit', 'update', 'toggleStatus', 'reviewTask']),
            // Specific permissions for reporting
            new Middleware('permission:create task reports', only: ['storeReport']),
            new Middleware('permission:edit task reports', only: ['updateReport']),
            new Middleware('permission:delete task reports', only: ['deleteReport']),
        ];
    }
    
    public function index() { return redirect()->route('tasks.assignments'); }

    public function personal(Request $request) {
        $user = auth()->user();
        $query = Task::where('category', 'admin_personal')
            ->where('created_by', $user->id);
            
        $filter = $request->query('filter', 'daily');
        $status = $request->query('status');

        switch ($filter) {
            case 'daily': 
                $query->where('frequency', 'daily');
                break;
            case 'top_five': 
                $query->where('frequency', 'top_five');
                break;
            case 'urgent': 
                $query->whereIn('priority', ['urgent', 'critical']);
                break;
            case 'all': 
                if (!$status) {
                     $query->where('is_active', true)
                           ->where('status', '!=', 'completed')
                           ->where('frequency', '!=', 'top_five');
                }
                break;
            default: 
                $query->where('frequency', $filter);
                break;
        }

        if ($status) {
            if ($status === 'active') $query->where('is_active', true);
            elseif ($status === 'inactive') $query->where('is_active', false);
            else $query->where('status', $status)->where('is_active', true);
        } else {
             if ($filter !== 'all') $query->where('is_active', true);
        }

        $adminTasks = $query->latest()->get();
        return view('tasks.personal', compact('adminTasks', 'filter'));
    }

    public function assignments(Request $request) {
        $user = auth()->user();
        $employees = [];

        $query = Task::where('category', 'employee_assignment')->with('assignee');

        if ($request->has('priority') && $request->priority) $query->where('priority', $request->priority);
        if ($request->has('status') && $request->status) $query->where('status', $request->status);
        if ($request->has('employee_id') && $request->employee_id) $query->where('assigned_to', $request->employee_id);

        if ($request->has('state')) {
            if ($request->state === 'disabled') $query->where('is_active', false);
            else $query->where('is_active', true);
        } else {
             $query->where('is_active', true);
        }

        if ($user->hasRole(['super-admin', 'admin'])) {
            $employees = User::role('employee')->where('is_active', true)->get();
        } else {
            $query->where('assigned_to', $user->id);
        }

        $assignedTasks = $query->latest()->get();
        return view('tasks.assignments', compact('assignedTasks', 'employees'));
    }
    
    public function show(Task $task) {
        $user = auth()->user();
        if (!$task->is_active && !$user->hasRole(['super-admin', 'admin'])) abort(403, 'Task is disabled.');
        if ($task->assigned_to != $user->id && !$user->hasRole(['super-admin', 'admin']) && $task->category != 'admin_personal') abort(403, 'Unauthorized.');
        
        $task->load(['reports.user', 'assignee', 'creator', 'todos']);
        return view('tasks.show', compact('task'));
    }

    public function create(Request $request) { 
        $employees = User::role('employee')->where('is_active', true)->get();
        
        $context = $request->query('context');
        $defaultPriority = 'normal';
        $defaultFreq = 'daily'; 
        $defaultDate = null; 

        if ($context === 'urgent') $defaultPriority = 'urgent';
        if ($context === 'top_five') $defaultFreq = 'top_five';
        if ($context === 'weekly') {
            $defaultFreq = 'weekly';
             $defaultDate = Carbon::now()->endOfWeek()->format('Y-m-d');
        }
        if ($context === 'daily') {
            $defaultFreq = 'daily';
            $defaultDate = Carbon::today()->format('Y-m-d');
        }

        $projects = Project::active()->with('client')->orderBy('name')->get();

        return view('tasks.create', compact('employees', 'defaultPriority', 'defaultFreq', 'defaultDate', 'projects')); 
    }
    
    public function store(Request $request) {
        $request->validate([
            'title' => 'required',
            'category' => 'required',
            'media' => 'nullable|file|max:2048',
            'project_id' => 'nullable|exists:projects,id',
        ]);
        $data = $request->except('media'); 
        $data['project_id'] = $request->filled('project_id') ? (int) $request->project_id : null;
        $data['created_by'] = auth()->id();
        $data['priority'] = $request->priority ?? 'normal';
        $data['frequency'] = $request->frequency ?? 'daily';
        $data['is_active'] = true; 

        if ($request->category === 'admin_personal') {
            $data['assigned_to'] = null;
            if ($data['frequency'] === 'top_five') {
                $activeTopFive = Task::where('category', 'admin_personal')
                    ->where('created_by', auth()->id())
                    ->where('frequency', 'top_five')
                    ->where('is_active', true)
                    ->count();
                if ($activeTopFive >= 5) {
                    $data['is_active'] = false; 
                    session()->flash('error', 'Limit Reached: Top 5 list is full. Task saved as INACTIVE.');
                }
            }
        }

        if ($request->hasFile('media')) {
            $data['media_path'] = $request->file('media')->store('task_media', 'public');
        }

        Task::create($data);
        $route = $request->category == 'admin_personal' ? 'tasks.personal' : 'tasks.assignments';
        $params = $request->category == 'admin_personal' ? ['filter' => $data['frequency']] : [];
        return redirect()->route($route, $params)->with('success', 'Task created.');
    }

    public function edit(Task $task) {
        $employees = User::role('employee')->where('is_active', true)->get();
        $projects = Project::active()->with('client')->orderBy('name')->get();
        return view('tasks.edit', compact('task', 'employees', 'projects'));
    }

    public function update(Request $request, Task $task) {
        $request->validate([
            'title' => 'required',
            'media' => 'nullable|file|max:2048',
            'project_id' => 'nullable|exists:projects,id',
        ]);
        $data = $request->except('media'); 
        $data['project_id'] = $request->filled('project_id') ? (int) $request->project_id : null;

        if ($request->hasFile('media')) {
            if($task->media_path) Storage::disk('public')->delete($task->media_path);
            $data['media_path'] = $request->file('media')->store('task_media', 'public');
        }

        $task->update($data);
        return redirect()->route($task->category == 'admin_personal' ? 'tasks.personal' : 'tasks.assignments')->with('success', 'Task updated.');
    }

    public function toggleStatus(Task $task) {
        if (!$task->is_active && $task->category === 'admin_personal' && $task->frequency === 'top_five') {
             $activeTopFive = Task::where('category', 'admin_personal')
                ->where('created_by', auth()->id())
                ->where('frequency', 'top_five')
                ->where('is_active', true)
                ->count();
            if ($activeTopFive >= 5) {
                return back()->with('error', 'Limit Reached: Cannot enable. Max 5 active Top Tasks allowed.');
            }
        }
        $task->is_active = !$task->is_active;
        $task->save();
        return back()->with('success', 'Task availability updated.');
    }

    public function storeReport(Request $request, Task $task) {
        // Security: Assigned User or Admin
        if (auth()->id() != $task->assigned_to && !auth()->user()->hasRole(['super-admin', 'admin'])) abort(403);

        $request->validate(['remark' => 'required', 'media' => 'nullable|file|max:2048']);
        $path = null;
        if ($request->hasFile('media')) {
            $path = $request->file('media')->store('report_media', 'public');
        }
        TaskReport::create([
            'task_id' => $task->id, 'user_id' => auth()->id(), 'remark' => $request->remark, 'time_note' => $request->time_note, 'media_path' => $path
        ]);
        if ($request->has('status') && $request->status) {
            $task->update(['status' => $request->status]);
        }
        return back()->with('success', 'Report submitted successfully.');
    }

    public function updateReport(Request $request, TaskReport $report) {
        if (auth()->id() != $report->user_id && !auth()->user()->hasRole(['super-admin', 'admin'])) abort(403);
        $request->validate(['remark' => 'required', 'media' => 'nullable|file|max:2048']);
        $data = $request->only('remark', 'time_note');
        if ($request->hasFile('media')) {
            if($report->media_path) Storage::disk('public')->delete($report->media_path);
            $data['media_path'] = $request->file('media')->store('report_media', 'public');
        }
        $report->update($data);
        return back()->with('success', 'Report updated.');
    }

    public function deleteReport(TaskReport $report) {
        if (auth()->id() != $report->user_id && !auth()->user()->hasRole(['super-admin', 'admin'])) abort(403);
        if($report->media_path) Storage::disk('public')->delete($report->media_path);
        $report->delete();
        return back()->with('success', 'Report deleted.');
    }

    public function reviewTask(Request $request, Task $task) {
        $request->validate(['rating' => 'required|integer|min:1|max:5', 'admin_media' => 'nullable|file|max:2048']);
        $data = ['status' => 'reviewed', 'rating' => $request->rating, 'admin_remark' => $request->admin_remark, 'admin_private_note' => $request->admin_private_note];
        if ($request->hasFile('admin_media')) {
            if($task->admin_media_path) Storage::disk('public')->delete($task->admin_media_path);
            $data['admin_media_path'] = $request->file('admin_media')->store('review_media', 'public');
        }
        $task->update($data);
        return back()->with('success', 'Task reviewed successfully.');
    }
    
    public function destroy(Task $task) {
        $cat = $task->category;
        if($task->media_path) Storage::disk('public')->delete($task->media_path);
        $task->delete();
        return redirect()->route($cat == 'admin_personal' ? 'tasks.personal' : 'tasks.assignments')->with('success', 'Task deleted.');
    }
}
