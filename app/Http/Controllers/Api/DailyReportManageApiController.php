<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReportEditGrant;
use App\Models\ReportSubmissionOverride;
use App\Models\User;
use App\Support\ApiJson;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DailyReportManageApiController extends Controller
{
    public function index()
    {
        $employees = User::role('employee')->where('is_active', true)->orderBy('name')->get(['id', 'name', 'email']);
        $submissionOverrides = ReportSubmissionOverride::with('user')->orderBy('date', 'desc')->get()->map(fn ($o) => [
            'user_id' => $o->user_id,
            'user_name' => $o->user?->name,
            'date' => $o->date->format('Y-m-d'),
            'allow_morning' => $o->allow_morning,
            'allow_evening' => $o->allow_evening,
        ]);
        $editGrants = ReportEditGrant::with(['user', 'grantedBy'])->where('expires_at', '>', now())->orderBy('expires_at')->get()->map(fn ($g) => [
            'user_id' => $g->user_id,
            'user_name' => $g->user?->name,
            'date' => $g->date->format('Y-m-d'),
            'expires_at' => $g->expires_at->toIso8601String(),
        ]);

        return ApiJson::ok([
            'employees' => $employees,
            'submission_overrides' => $submissionOverrides,
            'edit_grants' => $editGrants,
        ]);
    }

    public function allowSubmission(Request $request)
    {
        $request->validate(['user_id' => 'required|exists:users,id', 'date' => 'required|date', 'slot' => 'required|in:morning,evening']);
        $userId = (int) $request->user_id;
        if (! User::find($userId)?->hasRole('employee')) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => ['user_id' => ['User must be an employee.']],
            ], 422);
        }
        $dateStr = Carbon::parse($request->date)->format('Y-m-d');
        $override = ReportSubmissionOverride::firstOrNew(
            ['user_id' => $userId, 'date' => $dateStr],
            ['allow_morning' => false, 'allow_evening' => false]
        );
        if ($request->slot === 'morning') {
            $override->allow_morning = true;
        } else {
            $override->allow_evening = true;
        }
        $override->save();

        return ApiJson::ok(['user_id' => $userId, 'date' => $dateStr], 'Submission window updated');
    }

    public function revokeSubmissionOverride(Request $request)
    {
        $request->validate(['user_id' => 'required|exists:users,id', 'date' => 'required|date', 'slot' => 'required|in:morning,evening']);
        $override = ReportSubmissionOverride::where('user_id', $request->user_id)->where('date', $request->date)->first();
        if (! $override) {
            return response()->json([
                'message' => 'Not found',
            ], 404);
        }
        if ($request->slot === 'morning') {
            $override->allow_morning = false;
        } else {
            $override->allow_evening = false;
        }
        $override->save();
        if (! $override->allow_morning && ! $override->allow_evening) {
            $override->delete();
        }

        return ApiJson::ok([], 'Revoked');
    }

    public function grantEdit(Request $request)
    {
        $request->validate(['user_id' => 'required|exists:users,id', 'date' => 'required|date', 'duration_minutes' => 'required|integer|min:1|max:10080']);
        $userId = (int) $request->user_id;
        if (! User::find($userId)?->hasRole('employee')) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => ['user_id' => ['User must be an employee.']],
            ], 422);
        }
        $dateStr = Carbon::parse($request->date)->format('Y-m-d');
        $expiresAt = now()->addMinutes((int) $request->duration_minutes);
        $existing = ReportEditGrant::where('user_id', $userId)->where('date', $dateStr)->where('expires_at', '>', now())->first();
        if ($existing) {
            $existing->expires_at = $expiresAt;
            $existing->granted_by = $request->user()->id;
            $existing->save();
        } else {
            ReportEditGrant::create([
                'user_id' => $userId,
                'date' => $dateStr,
                'expires_at' => $expiresAt,
                'granted_by' => $request->user()->id,
            ]);
        }

        return ApiJson::ok(['user_id' => $userId, 'date' => $dateStr], 'Edit access granted');
    }

    public function revokeEditGrant(Request $request)
    {
        $request->validate(['user_id' => 'required|exists:users,id', 'date' => 'required|date']);
        ReportEditGrant::where('user_id', $request->user_id)->where('date', $request->date)->delete();

        return ApiJson::ok([], 'Revoked');
    }
}
