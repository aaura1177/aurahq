<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiJson;
use App\Models\Milestone;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectApiController extends Controller
{
    public function index(Request $request)
    {
        $q = Project::with('client')->active()->latest();
        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }
        if ($request->filled('venture')) {
            $q->where('venture', $request->venture);
        }
        if ($request->filled('client_id')) {
            $q->where('client_id', $request->client_id);
        }

        return ApiJson::paginated($q->paginate(25));
    }

    public function show(Project $project)
    {
        $project->load(['client', 'milestones', 'tasks.assignee', 'invoices']);

        return ApiJson::ok($project);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['created_by'] = $request->user()->id;
        $data['is_active'] = $request->boolean('is_active', true);
        $project = Project::create($data);

        return ApiJson::created($project, 'Project created successfully');
    }

    public function update(Request $request, Project $project)
    {
        $data = $this->validated($request, false);
        $data['is_active'] = $request->boolean('is_active', $project->is_active);
        $project->update($data);

        return ApiJson::ok($project->fresh(), 'Updated');
    }

    public function addMilestone(Request $request, Project $project)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);
        $max = (int) $project->milestones()->max('sort_order');
        $m = Milestone::create([
            'project_id' => $project->id,
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'sort_order' => $max + 1,
        ]);

        return ApiJson::created($m, 'Milestone created successfully');
    }

    public function completeMilestone(Request $request, Milestone $milestone)
    {
        $milestone->completed_at = $request->boolean('completed') ? now() : null;
        $milestone->save();

        return ApiJson::ok($milestone);
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return ApiJson::noContent();
    }

    private function validated(Request $request, bool $allRequired = true): array
    {
        $rules = [
            'name' => ($allRequired ? 'required' : 'sometimes').'|string|max:255',
            'client_id' => ($allRequired ? 'required' : 'sometimes').'|exists:clients,id',
            'description' => 'nullable|string',
            'venture' => 'required|in:'.implode(',', Project::VENTURES),
            'status' => 'required|in:'.implode(',', Project::STATUSES),
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'expected_end_date' => 'nullable|date',
            'actual_end_date' => 'nullable|date',
        ];
        $request->validate($rules);

        return $request->only([
            'name', 'client_id', 'description', 'venture', 'status', 'budget',
            'start_date', 'expected_end_date', 'actual_end_date',
        ]);
    }
}
