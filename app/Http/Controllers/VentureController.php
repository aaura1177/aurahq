<?php

namespace App\Http\Controllers;

use App\Models\Venture;
use App\Models\VentureUpdate;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class VentureController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view ventures', only: ['index', 'show']),
            new Middleware('permission:create venture updates', only: ['addUpdate']),
        ];
    }

    public function index()
    {
        $ventures = Venture::query()
            ->with('lastUpdate.user')
            ->orderBy('name')
            ->get();

        return view('ventures.index', compact('ventures'));
    }

    public function show(Venture $venture)
    {
        $venture->load([
            'updates.user',
            'projects' => fn ($q) => $q->with('client')->active()->orderByDesc('id')->limit(50),
        ]);

        $received = $venture->financeReceivedTotal();
        $given = $venture->financeGivenTotal();

        return view('ventures.show', [
            'venture' => $venture,
            'financeReceived' => $received,
            'financeGiven' => $given,
            'financeNet' => $received - $given,
            'openTasksCount' => $venture->open_tasks_count,
            'openProjectsCount' => $venture->open_projects_count,
        ]);
    }

    public function addUpdate(Request $request, Venture $venture)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'type' => 'required|in:'.implode(',', VentureUpdate::TYPES),
        ]);

        VentureUpdate::create([
            'venture_id' => $venture->id,
            'user_id' => $request->user()->id,
            'title' => $data['title'],
            'content' => $data['content'] ?? null,
            'type' => $data['type'],
        ]);

        return redirect()
            ->route('ventures.show', $venture)
            ->with('success', 'Update posted.');
    }
}
