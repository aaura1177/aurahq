<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

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
            new Middleware('permission:edit lead activities', only: ['updateActivity']),
            new Middleware('permission:delete lead activities', only: ['destroyActivity']),
        ];
    }

    public function index(Request $request)
    {
        $query = Lead::with('assignee')->active();

        if ($request->filled('stage')) {
            $query->where('stage', $request->stage);
        }
        if ($request->filled('industry')) {
            $query->where('industry', $request->industry);
        }
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }
        if ($request->filled('city')) {
            $query->where('city', 'like', '%'.$request->city.'%');
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('business_name', 'like', '%'.$s.'%')
                    ->orWhere('contact_person', 'like', '%'.$s.'%')
                    ->orWhere('phone', 'like', '%'.$s.'%')
                    ->orWhere('email', 'like', '%'.$s.'%');
            });
        }

        $leads = $query->latest()->paginate(25)->withQueryString();

        $totalLeads = Lead::active()->count();
        $pipelineValue = Lead::active()->whereNotIn('stage', ['won', 'lost'])->sum('estimated_value');
        $won = Lead::active()->where('stage', 'won')->count();
        $lost = Lead::active()->where('stage', 'lost')->count();
        $conversionRate = ($won + $lost) > 0 ? round(100 * $won / ($won + $lost), 1) : 0;

        $users = User::where('is_active', true)->orderBy('name')->get();

        return view('leads.index', [
            'leads' => $leads,
            'filters' => $request->only(['stage', 'industry', 'source', 'assigned_to', 'city', 'search']),
            'users' => $users,
            'stages' => Lead::STAGES,
            'industries' => Lead::INDUSTRIES,
            'sources' => Lead::SOURCES,
            'totalLeads' => $totalLeads,
            'pipelineValue' => $pipelineValue,
            'conversionRate' => $conversionRate,
        ]);
    }

    public function pipeline()
    {
        $leads = Lead::active()->with('assignee')->orderBy('business_name')->get();
        $grouped = $leads->groupBy('stage');

        $pipelineValue = Lead::active()->whereNotIn('stage', ['won', 'lost'])->sum('estimated_value');
        $openCount = Lead::active()->whereNotIn('stage', ['won', 'lost'])->count();

        $stageStats = [];
        foreach (Lead::STAGES as $stage) {
            $inStage = $grouped->get($stage, collect());
            $stageStats[$stage] = [
                'count' => $inStage->count(),
                'value' => $inStage->sum('estimated_value'),
            ];
        }

        return view('leads.pipeline', [
            'grouped' => $grouped,
            'pipelineStages' => Lead::PIPELINE_STAGES,
            'closedStages' => ['won', 'lost'],
            'stages' => Lead::STAGES,
            'pipelineValue' => $pipelineValue,
            'openCount' => $openCount,
            'stageStats' => $stageStats,
        ]);
    }

    public function show(Lead $lead)
    {
        if (! $lead->is_active && ! auth()->user()->hasRole(['super-admin', 'admin'])) {
            abort(404);
        }

        $lead->load(['activities.user', 'assignee', 'creator', 'clients']);

        return view('leads.show', compact('lead'));
    }

    public function create()
    {
        $users = User::where('is_active', true)->orderBy('name')->get();

        return view('leads.create', [
            'users' => $users,
            'stages' => Lead::STAGES,
            'industries' => Lead::INDUSTRIES,
            'sources' => Lead::SOURCES,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedLeadData($request);
        $data['created_by'] = auth()->id();

        if (empty($data['next_follow_up']) && ! in_array($data['stage'], ['won', 'lost'], true)) {
            $data['next_follow_up'] = Carbon::now()->addDays(4)->toDateString();
        }

        $lead = Lead::create($data);

        LeadActivity::create([
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'type' => 'note',
            'description' => 'Lead created',
        ]);

        return redirect()->route('leads.show', $lead)->with('success', 'Lead created.');
    }

    public function edit(Lead $lead)
    {
        $users = User::where('is_active', true)->orderBy('name')->get();

        return view('leads.edit', [
            'lead' => $lead,
            'users' => $users,
            'stages' => Lead::STAGES,
            'industries' => Lead::INDUSTRIES,
            'sources' => Lead::SOURCES,
        ]);
    }

    public function update(Request $request, Lead $lead)
    {
        $data = $this->validatedLeadData($request);

        if ($data['stage'] === 'won') {
            $data['converted_at'] = $lead->converted_at ?? now();
        } else {
            $data['converted_at'] = null;
        }

        $lead->update($data);

        return redirect()->route('leads.show', $lead)->with('success', 'Lead updated.');
    }

    public function updateStage(Request $request, Lead $lead)
    {
        $request->validate([
            'stage' => 'required|in:'.implode(',', Lead::STAGES),
            'lost_reason' => 'nullable|string|max:255',
        ]);

        $newStage = $request->stage;
        if ($newStage === 'lost' && ! $request->filled('lost_reason')) {
            return back()->with('error', 'Please provide a lost reason.');
        }

        $oldStage = $lead->stage;
        if ($oldStage === $newStage) {
            return back();
        }

        $lead->stage = $newStage;
        if ($newStage === 'won') {
            $lead->converted_at = now();
            $lead->lost_reason = null;
        } elseif ($newStage === 'lost') {
            $lead->lost_reason = $request->lost_reason;
            $lead->converted_at = null;
        } else {
            $lead->converted_at = null;
            if ($newStage !== 'lost') {
                $lead->lost_reason = null;
            }
        }

        if (! $lead->next_follow_up && ! in_array($newStage, ['won', 'lost'], true)) {
            $lead->next_follow_up = Carbon::now()->addDays(4)->toDateString();
        }

        $lead->last_contacted_at = now();
        $lead->save();

        LeadActivity::create([
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'type' => 'stage_change',
            'description' => "Stage changed from {$oldStage} to {$newStage}",
            'metadata' => ['from' => $oldStage, 'to' => $newStage],
        ]);

        return back()->with('success', 'Stage updated.');
    }

    public function addActivity(Request $request, Lead $lead)
    {
        $request->validate([
            'type' => 'required|in:'.implode(',', LeadActivity::TYPES),
            'description' => 'required|string|max:5000',
        ]);

        LeadActivity::create([
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'type' => $request->type,
            'description' => $request->description,
        ]);

        $lead->update(['last_contacted_at' => now()]);

        return back()->with('success', 'Activity logged.');
    }

    public function updateActivity(Request $request, Lead $lead, LeadActivity $activity)
    {
        abort_unless($activity->lead_id === $lead->id, 404);

        $request->validate([
            'type' => 'required|in:'.implode(',', LeadActivity::TYPES),
            'description' => 'required|string|max:5000',
        ]);

        $activity->type = $request->type;
        $activity->description = $request->description;
        if ($request->type !== 'stage_change') {
            $activity->metadata = null;
        }
        $activity->save();

        return back()->with('success', 'Activity updated.');
    }

    public function destroyActivity(Lead $lead, LeadActivity $activity)
    {
        abort_unless($activity->lead_id === $lead->id, 404);

        $activity->delete();

        return back()->with('success', 'Activity removed.');
    }

    public function overdue()
    {
        $leads = Lead::with('assignee')
            ->active()
            ->overdue()
            ->orderBy('next_follow_up')
            ->paginate(25);

        return view('leads.overdue', compact('leads'));
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();

        return redirect()->route('leads.index')->with('success', 'Lead deleted.');
    }

    private function validatedLeadData(Request $request): array
    {
        foreach (['industry', 'source', 'assigned_to', 'estimated_value', 'next_follow_up', 'website', 'lost_reason', 'contact_person', 'phone', 'email', 'city'] as $key) {
            if ($request->has($key) && $request->input($key) === '') {
                $request->merge([$key => null]);
            }
        }

        $request->validate([
            'business_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
            'industry' => 'nullable|in:'.implode(',', Lead::INDUSTRIES),
            'city' => 'nullable|string|max:100',
            'source' => 'nullable|in:'.implode(',', Lead::SOURCES),
            'stage' => 'required|in:'.implode(',', Lead::STAGES),
            'estimated_value' => 'nullable|numeric|min:0',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'lost_reason' => 'required_if:stage,lost|nullable|string|max:255',
            'next_follow_up' => 'nullable|date',
        ]);

        $data = $request->only([
            'business_name', 'contact_person', 'phone', 'email', 'website',
            'industry', 'city', 'source', 'stage', 'estimated_value', 'assigned_to',
            'notes', 'lost_reason', 'next_follow_up',
        ]);

        if (($data['website'] ?? '') !== '' && ! str_starts_with((string) $data['website'], 'http')) {
            $data['website'] = 'https://'.$data['website'];
        }

        return $data;
    }
}
