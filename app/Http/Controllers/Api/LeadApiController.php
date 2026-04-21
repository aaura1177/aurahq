<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiJson;
use App\Models\Lead;
use App\Models\LeadActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LeadApiController extends Controller
{
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
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('business_name', 'like', '%'.$s.'%')
                    ->orWhere('contact_person', 'like', '%'.$s.'%');
            });
        }

        $leads = $query->latest()->paginate(25);

        return ApiJson::paginated($leads, fn ($l) => $this->leadToArray($l));
    }

    public function pipeline()
    {
        $leads = Lead::active()->with('assignee')->get();
        $grouped = $leads->groupBy('stage');
        $out = [];
        foreach (Lead::STAGES as $stage) {
            $inStage = $grouped->get($stage, collect());
            $out[$stage] = [
                'count' => $inStage->count(),
                'estimated_value_sum' => (float) $inStage->sum('estimated_value'),
                'leads' => $inStage->map(fn ($l) => $this->leadToArray($l))->values(),
            ];
        }

        $pipelineValue = Lead::active()->whereNotIn('stage', ['won', 'lost'])->sum('estimated_value');

        return ApiJson::ok([
            'pipeline_value_open' => (float) $pipelineValue,
            'stages' => $out,
        ]);
    }

    public function overdue()
    {
        $leads = Lead::with('assignee')->active()->overdue()->orderBy('next_follow_up')->get();

        return ApiJson::ok($leads->map(fn ($l) => $this->leadToArray($l))->values()->all());
    }

    public function show(Request $request, Lead $lead)
    {
        $lead->load(['activities.user', 'assignee', 'creator']);

        return ApiJson::ok($this->leadToArray($lead, true));
    }

    public function store(Request $request)
    {
        $data = $this->validateLeadApi($request);
        $data['created_by'] = $request->user()->id;

        if (empty($data['next_follow_up']) && ! in_array($data['stage'], ['won', 'lost'], true)) {
            $data['next_follow_up'] = Carbon::now()->addDays(4)->toDateString();
        }

        $lead = Lead::create($data);

        LeadActivity::create([
            'lead_id' => $lead->id,
            'user_id' => $request->user()->id,
            'type' => 'note',
            'description' => 'Lead created',
        ]);

        return ApiJson::created($this->leadToArray($lead, true), 'Lead created successfully');
    }

    public function update(Request $request, Lead $lead)
    {
        $data = $this->validateLeadApiPartial($request);
        if (array_key_exists('stage', $data)) {
            if ($data['stage'] === 'won') {
                $data['converted_at'] = $lead->converted_at ?? now();
            } elseif ($data['stage'] === 'lost') {
                $data['converted_at'] = null;
            } elseif (! in_array($data['stage'], ['won', 'lost'], true)) {
                $data['converted_at'] = null;
            }
        }
        $lead->update($data);

        return ApiJson::ok($this->leadToArray($lead->fresh(), true), 'Updated');
    }

    public function updateStage(Request $request, Lead $lead)
    {
        $request->validate([
            'stage' => 'required|in:'.implode(',', Lead::STAGES),
            'lost_reason' => 'nullable|string|max:255',
        ]);

        $newStage = $request->stage;
        if ($newStage === 'lost' && ! $request->filled('lost_reason')) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => ['lost_reason' => ['The lost reason field is required when stage is lost.']],
            ], 422);
        }

        $oldStage = $lead->stage;
        if ($oldStage === $newStage) {
            return ApiJson::ok($this->leadToArray($lead, true));
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
            'user_id' => $request->user()->id,
            'type' => 'stage_change',
            'description' => "Stage changed from {$oldStage} to {$newStage}",
            'metadata' => ['from' => $oldStage, 'to' => $newStage],
        ]);

        return ApiJson::ok($this->leadToArray($lead->fresh(), true), 'Stage updated');
    }

    public function addActivity(Request $request, Lead $lead)
    {
        $request->validate([
            'type' => 'required|in:'.implode(',', LeadActivity::TYPES),
            'description' => 'required|string|max:5000',
        ]);

        LeadActivity::create([
            'lead_id' => $lead->id,
            'user_id' => $request->user()->id,
            'type' => $request->type,
            'description' => $request->description,
        ]);

        $lead->update(['last_contacted_at' => now()]);

        return ApiJson::created($this->leadToArray($lead->fresh()->load('activities.user'), true), 'Activity created successfully');
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

        return ApiJson::ok($this->leadToArray($lead->fresh()->load('activities.user'), true), 'Activity updated');
    }

    public function destroyActivity(Lead $lead, LeadActivity $activity)
    {
        abort_unless($activity->lead_id === $lead->id, 404);

        $activity->delete();

        return ApiJson::ok($this->leadToArray($lead->fresh()->load('activities.user'), true), 'Activity removed');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();

        return ApiJson::noContent();
    }

    private function validateLeadApi(Request $request): array
    {
        foreach (['industry', 'source', 'assigned_to', 'estimated_value', 'next_follow_up', 'website', 'lost_reason', 'contact_person', 'phone', 'email', 'city'] as $key) {
            if ($request->has($key) && $request->input($key) === '') {
                $request->merge([$key => null]);
            }
        }

        $v = $request->validate([
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

        if (! empty($v['website']) && ! str_starts_with((string) $v['website'], 'http')) {
            $v['website'] = 'https://'.$v['website'];
        }

        return $v;
    }

    private function validateLeadApiPartial(Request $request): array
    {
        foreach (['industry', 'source', 'assigned_to', 'estimated_value', 'next_follow_up', 'website', 'lost_reason', 'contact_person', 'phone', 'email', 'city'] as $key) {
            if ($request->has($key) && $request->input($key) === '') {
                $request->merge([$key => null]);
            }
        }

        $v = $request->validate([
            'business_name' => 'sometimes|required|string|max:255',
            'contact_person' => 'sometimes|nullable|string|max:255',
            'phone' => 'sometimes|nullable|string|max:50',
            'email' => 'sometimes|nullable|email|max:255',
            'website' => 'sometimes|nullable|string|max:255',
            'industry' => 'sometimes|nullable|in:'.implode(',', Lead::INDUSTRIES),
            'city' => 'sometimes|nullable|string|max:100',
            'source' => 'sometimes|nullable|in:'.implode(',', Lead::SOURCES),
            'stage' => 'sometimes|required|in:'.implode(',', Lead::STAGES),
            'estimated_value' => 'sometimes|nullable|numeric|min:0',
            'assigned_to' => 'sometimes|nullable|exists:users,id',
            'notes' => 'sometimes|nullable|string',
            'lost_reason' => 'sometimes|nullable|required_if:stage,lost|string|max:255',
            'next_follow_up' => 'sometimes|nullable|date',
        ]);

        if (array_key_exists('website', $v) && ! empty($v['website']) && ! str_starts_with((string) $v['website'], 'http')) {
            $v['website'] = 'https://'.$v['website'];
        }

        return $v;
    }

    private function leadToArray(Lead $lead, bool $detailed = false): array
    {
        $base = [
            'id' => $lead->id,
            'business_name' => $lead->business_name,
            'contact_person' => $lead->contact_person,
            'phone' => $lead->phone,
            'email' => $lead->email,
            'website' => $lead->website,
            'industry' => $lead->industry,
            'city' => $lead->city,
            'source' => $lead->source,
            'stage' => $lead->stage,
            'stage_label' => $lead->stage_label,
            'estimated_value' => $lead->estimated_value !== null ? (float) $lead->estimated_value : null,
            'assigned_to' => $lead->assigned_to,
            'assignee' => $lead->assignee ? ['id' => $lead->assignee->id, 'name' => $lead->assignee->name] : null,
            'notes' => $lead->notes,
            'lost_reason' => $lead->lost_reason,
            'next_follow_up' => $lead->next_follow_up?->format('Y-m-d'),
            'last_contacted_at' => $lead->last_contacted_at?->toIso8601String(),
            'converted_at' => $lead->converted_at?->toIso8601String(),
            'is_active' => $lead->is_active,
            'created_by' => $lead->created_by,
            'created_at' => $lead->created_at?->toIso8601String(),
            'updated_at' => $lead->updated_at?->toIso8601String(),
        ];

        if ($detailed) {
            $base['activities'] = $lead->relationLoaded('activities')
                ? $lead->activities->map(function ($a) {
                    return [
                        'id' => $a->id,
                        'type' => $a->type,
                        'description' => $a->description,
                        'metadata' => $a->metadata,
                        'user' => $a->relationLoaded('user') && $a->user ? ['id' => $a->user->id, 'name' => $a->user->name] : null,
                        'created_at' => $a->created_at?->toIso8601String(),
                    ];
                })->values()->all()
                : [];
        }

        return $base;
    }
}
