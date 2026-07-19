<?php

namespace App\Http\Controllers;

use App\Models\DailyFocus;
use App\Models\Task;
use Illuminate\Http\Request;

class DailyFocusController extends Controller
{
    public function today()
    {
        $user = auth()->user();
        if (! $user->hasRole('super-admin')) {
            abort(403);
        }

        $today = now()->format('Y-m-d');
        $focus = DailyFocus::firstOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            []
        );

        // Same pool as My Tasks → All Active (personal tasks only)
        $availableTasks = Task::query()
            ->where('category', 'admin_personal')
            ->where('created_by', $user->id)
            ->where('is_active', true)
            ->where('status', '!=', 'completed')
            ->orderByRaw("CASE priority WHEN 'critical' THEN 0 WHEN 'urgent' THEN 1 WHEN 'normal' THEN 2 ELSE 3 END")
            ->orderBy('due_date')
            ->orderBy('id')
            ->get();

        $personalTaskCount = $availableTasks->count();

        $streak = DailyFocus::currentStreak($user->id);

        $yesterday = DailyFocus::query()
            ->where('user_id', $user->id)
            ->whereDate('date', now()->subDay()->format('Y-m-d'))
            ->first();

        $timeBlocks = config('daily_focus.time_blocks', []);

        return view('daily-focus.today', compact(
            'focus',
            'availableTasks',
            'personalTaskCount',
            'streak',
            'yesterday',
            'timeBlocks'
        ));
    }

    public function update(Request $request, DailyFocus $dailyFocus)
    {
        $user = auth()->user();
        if (! $user->hasRole('super-admin') || $dailyFocus->user_id !== $user->id) {
            abort(403);
        }

        // Drag-reorder: order = [oldSlotInNewPos1, oldSlotInNewPos2, oldSlotInNewPos3]
        if ($request->has('order')) {
            $order = $request->validate([
                'order' => 'required|array|size:3',
                'order.*' => 'integer|in:1,2,3',
            ])['order'];

            if (count(array_unique($order)) !== 3) {
                return $request->wantsJson()
                    ? response()->json(['message' => 'Invalid order'], 422)
                    : back()->with('error', 'Invalid order.');
            }

            $snapshot = [];
            foreach ([1, 2, 3] as $s) {
                $snapshot[$s] = [
                    'title' => $dailyFocus->{"task_{$s}_title"},
                    'id' => $dailyFocus->{"task_{$s}_id"},
                    'completed' => $dailyFocus->{"task_{$s}_completed"},
                ];
            }
            foreach ([1, 2, 3] as $newSlot) {
                $old = (int) $order[$newSlot - 1];
                $dailyFocus->{"task_{$newSlot}_title"} = $snapshot[$old]['title'];
                $dailyFocus->{"task_{$newSlot}_id"} = $snapshot[$old]['id'];
                $dailyFocus->{"task_{$newSlot}_completed"} = $snapshot[$old]['completed'];
            }
            $dailyFocus->save();

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Reordered']);
            }

            return back()->with('success', 'Priority updated.');
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

        foreach (['task_1', 'task_2', 'task_3'] as $slot) {
            $idKey = $slot.'_id';
            $titleKey = $slot.'_title';
            if (! empty($validated[$idKey] ?? null)) {
                $task = Task::find($validated[$idKey]);
                if (
                    ! $task
                    || $task->category !== 'admin_personal'
                    || $task->created_by !== $user->id
                ) {
                    abort(403, 'Invalid task.');
                }
                $validated[$titleKey] = $task->title;
            } elseif (array_key_exists($titleKey, $validated)) {
                // Custom focus or clear — drop linked task id
                $validated[$idKey] = null;
            }
        }

        $dailyFocus->fill($validated);
        foreach (['task_1_completed', 'task_2_completed', 'task_3_completed'] as $k) {
            if ($request->has($k)) {
                $dailyFocus->{$k} = $request->boolean($k);
            }
        }
        $dailyFocus->save();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Saved',
                'data' => [
                    'completed_count' => $dailyFocus->completed_count,
                    'all_completed' => $dailyFocus->all_completed,
                ],
            ]);
        }

        return back()->with('success', 'Saved.');
    }

    public function history()
    {
        $user = auth()->user();
        if (! $user->hasRole('super-admin')) {
            abort(403);
        }

        $from = now()->subDays(29)->startOfDay();

        $focuses = DailyFocus::query()
            ->where('user_id', $user->id)
            ->whereDate('date', '>=', $from)
            ->orderByDesc('date')
            ->get();

        return view('daily-focus.history', compact('focuses'));
    }
}
