<?php

namespace App\Http\Controllers;

use App\Models\Venture;
use App\Models\VentureUpdate;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;

class VentureController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view ventures', only: ['index', 'show']),
            new Middleware('role:super-admin', only: ['create', 'store', 'edit', 'update', 'destroy']),
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

    public function create()
    {
        return view('ventures.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? $data['name']);
        $venture = Venture::create($data);

        return redirect()->route('ventures.show', $venture)->with('success', 'Venture created.');
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

    public function edit(Venture $venture)
    {
        return view('ventures.edit', compact('venture'));
    }

    public function update(Request $request, Venture $venture)
    {
        $data = $this->validated($request, $venture);
        $slug = $this->uniqueSlug($data['slug'] ?? $data['name'], $venture->id);
        $data['slug'] = $slug;
        $venture->update($data);

        return redirect()->route('ventures.show', $venture->fresh())->with('success', 'Venture updated.');
    }

    public function destroy(Venture $venture)
    {
        $venture->delete();

        return redirect()->route('ventures.index')->with('success', 'Venture deleted.');
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

    private function validated(Request $request, ?Venture $venture = null): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:100|alpha_dash',
            'description' => 'nullable|string',
            'status' => 'required|in:'.implode(',', Venture::STATUSES),
            'partner_name' => 'nullable|string|max:255',
            'partner_funded' => 'sometimes|boolean',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
        ]);

        $data['partner_funded'] = $request->boolean('partner_funded');
        $data['color'] = $data['color'] ?: '#6C63FF';
        $data['icon'] = $data['icon'] ?: 'fa-rocket';

        return $data;
    }

    private function uniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug(Str::limit($source, 80, ''));
        if ($base === '') {
            $base = 'venture';
        }
        $slug = $base;
        $i = 2;
        while (
            Venture::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }
}
