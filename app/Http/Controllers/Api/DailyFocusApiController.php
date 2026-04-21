<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiJson;
use App\Models\DailyFocus;
use App\Models\Task;
use Illuminate\Http\Request;

class DailyFocusApiController extends Controller
{
    public function today(Request $request)
    {
        if (! $request->user()->hasRole('super-admin')) {
            return ApiJson::unauthorized();
        }

        $user = $request->user();
        $today = now()->format('Y-m-d');
        $focus = DailyFocus::firstOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            []
        );

        $availableTasks = Task::query()
            ->where('is_active', true)
            ->where('status', '!=', 'completed')
            ->where(function ($q) use ($user) {
                $q->where('created_by', $user->id)->orWhere('assigned_to', $user->id);
            })
            ->orderByRaw("CASE priority WHEN 'critical' THEN 0 WHEN 'urgent' THEN 1 WHEN 'normal' THEN 2 ELSE 3 END")
            ->orderBy('due_date')
            ->get();

        $yesterday = DailyFocus::query()
            ->where('user_id', $user->id)
            ->whereDate('date', now()->subDay()->format('Y-m-d'))
            ->first();

        return ApiJson::ok([
            'focus' => $this->focusArray($focus),
            'available_tasks' => $availableTasks->map(fn (Task $t) => [
                'id' => $t->id,
                'title' => $t->title,
                'priority' => $t->priority,
                'due_date' => $t->due_date?->format('Y-m-d'),
            ]),
            'streak' => DailyFocus::currentStreak($user->id),
            'yesterday' => $yesterday ? $this->focusArray($yesterday) : null,
        ]);
    }

    public function store(Request $request)
    {
        if (! $request->user()->hasRole('super-admin')) {
            return ApiJson::unauthorized();
        }

        $request->validate(['date' => 'nullable|date']);

        $user = $request->user();
        $date = $request->input('date', now()->format('Y-m-d'));
        $focus = DailyFocus::firstOrCreate(
            ['user_id' => $user->id, 'date' => $date],
            []
        );

        return ApiJson::created($this->focusArray($focus), 'Success');
    }

    public function update(Request $request, DailyFocus $dailyFocus)
    {
        if (! $request->user()->hasRole('super-admin') || $dailyFocus->user_id !== $request->user()->id) {
            return ApiJson::unauthorized();
        }

        $validated = $request->validate([
            'task_1_title' => 'nullable|string|max:255',
            'task_2_title' => 'nullable|string|max:255',
            'task_3_title' => 'nullable|string|max:255',
            'task_1_id' => 'nullable|exists:tasks,id',
            'task_2_id' => 'nullable|exists:tasks,id',
            'task_3_id' => 'nullable|exists:tasks,id',
            'task_1_completed' => 'sometimes|boolean',
            'task_2_completed' => 'sometimes|boolean',
            'task_3_completed' => 'sometimes|boolean',
            'energy_level' => 'nullable|in:'.implode(',', DailyFocus::ENERGY_LEVELS),
            'end_of_day_note' => 'nullable|string',
            'wins' => 'nullable|string',
            'tomorrow_focus' => 'nullable|string',
        ]);

        $user = $request->user();
        foreach (['task_1', 'task_2', 'task_3'] as $slot) {
            $idKey = $slot.'_id';
            if (! empty($validated[$idKey] ?? null)) {
                $task = Task::find($validated[$idKey]);
                if (! $task || ($task->created_by !== $user->id && $task->assigned_to !== $user->id)) {
                    return response()->json([
                        'message' => 'Validation failed',
                        'errors' => [$idKey => ['The selected task is invalid or not yours.']],
                    ], 422);
                }
                $validated[$slot.'_title'] = $task->title;
            }
        }

        $dailyFocus->fill($validated);
        foreach (['task_1_completed', 'task_2_completed', 'task_3_completed'] as $k) {
            if ($request->has($k)) {
                $dailyFocus->{$k} = $request->boolean($k);
            }
        }
        $dailyFocus->save();

        return ApiJson::ok($this->focusArray($dailyFocus->fresh()), 'Updated');
    }

    public function history(Request $request)
    {
        if (! $request->user()->hasRole('super-admin')) {
            return ApiJson::unauthorized();
        }

        $from = now()->subDays(29)->startOfDay();
        $rows = DailyFocus::query()
            ->where('user_id', $request->user()->id)
            ->whereDate('date', '>=', $from)
            ->orderByDesc('date')
            ->get()
            ->map(fn (DailyFocus $f) => $this->focusArray($f));

        return ApiJson::ok($rows->values()->all());
    }

    /**
     * @return array<string, mixed>
     */
    private function focusArray(DailyFocus $f): array
    {
        return [
            'id' => $f->id,
            'date' => $f->date->format('Y-m-d'),
            'task_1_title' => $f->task_1_title,
            'task_2_title' => $f->task_2_title,
            'task_3_title' => $f->task_3_title,
            'task_1_id' => $f->task_1_id,
            'task_2_id' => $f->task_2_id,
            'task_3_id' => $f->task_3_id,
            'task_1_completed' => $f->task_1_completed,
            'task_2_completed' => $f->task_2_completed,
            'task_3_completed' => $f->task_3_completed,
            'completed_count' => $f->completed_count,
            'all_completed' => $f->all_completed,
            'energy_level' => $f->energy_level,
            'end_of_day_note' => $f->end_of_day_note,
            'wins' => $f->wins,
            'tomorrow_focus' => $f->tomorrow_focus,
        ];
    }
}
