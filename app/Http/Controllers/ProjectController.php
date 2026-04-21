<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Milestone;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProjectController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view projects', only: ['index', 'show']),
            new Middleware('permission:create projects', only: ['create', 'store']),
            new Middleware('permission:edit projects', only: ['edit', 'update', 'addMilestone', 'completeMilestone', 'reorderMilestones']),
            new Middleware('permission:delete projects', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = Project::with(['client', 'milestones'])->active()->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('venture')) {
            $query->where('venture', $request->venture);
        }
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $projects = $query->get();
        $clients = Client::active()->orderBy('name')->get();

        return view('projects.index', [
            'projects' => $projects,
            'clients' => $clients,
            'filters' => $request->only(['status', 'venture', 'client_id']),
        ]);
    }

    public function create(Request $request)
    {
        $clients = Client::active()->orderBy('name')->get();
        $clientId = $request->get('client_id');

        return view('projects.create', compact('clients', 'clientId'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedProject($request);
        $data['created_by'] = auth()->id();
        $data['is_active'] = $request->boolean('is_active', true);
        $project = Project::create($data);

        return redirect()->route('projects.show', $project)->with('success', 'Project created.');
    }

    public function show(Project $project)
    {
        $project->load([
            'client',
            'milestones',
            'tasks.assignee',
            'invoices',
            'creator',
        ]);

        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $clients = Client::active()->orderBy('name')->get();

        return view('projects.edit', compact('project', 'clients'));
    }

    public function update(Request $request, Project $project)
    {
        $data = $this->validatedProject($request);
        $data['is_active'] = $request->boolean('is_active');
        $project->update($data);

        return redirect()->route('projects.show', $project)->with('success', 'Project updated.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project deleted.');
    }

    public function addMilestone(Request $request, Project $project)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        $maxOrder = (int) $project->milestones()->max('sort_order');

        Milestone::create([
            'project_id' => $project->id,
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'sort_order' => $maxOrder + 1,
        ]);

        return back()->with('success', 'Milestone added.');
    }

    public function completeMilestone(Request $request, Milestone $milestone)
    {
        if ($request->boolean('completed')) {
            $milestone->completed_at = now();
        } else {
            $milestone->completed_at = null;
        }
        $milestone->save();

        return back()->with('success', 'Milestone updated.');
    }

    public function reorderMilestones(Request $request, Project $project)
    {
        $request->validate([
            'milestone_ids' => 'required|array',
            'milestone_ids.*' => 'exists:milestones,id',
        ]);

        foreach ($request->milestone_ids as $order => $id) {
            $m = Milestone::where('project_id', $project->id)->where('id', $id)->first();
            if ($m) {
                $m->sort_order = $order;
                $m->save();
            }
        }

        return back()->with('success', 'Order saved.');
    }

    private function validatedProject(Request $request): array
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'client_id' => 'required|exists:clients,id',
            'description' => 'nullable|string',
            'venture' => 'required|in:'.implode(',', Project::VENTURES),
            'status' => 'required|in:'.implode(',', Project::STATUSES),
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'expected_end_date' => 'nullable|date',
            'actual_end_date' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        return $request->only([
            'name', 'client_id', 'description', 'venture', 'status', 'budget',
            'start_date', 'expected_end_date', 'actual_end_date', 'is_active',
        ]);
    }
}
