<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiJson;
use App\Models\Venture;
use App\Models\VentureUpdate;
use Illuminate\Http\Request;

class VentureApiController extends Controller
{
    public function index()
    {
        $paginator = Venture::query()
            ->with('lastUpdate.user')
            ->orderBy('name')
            ->paginate(25);

        return ApiJson::paginated($paginator, fn (Venture $v) => $this->venturePayload($v, true));
    }

    public function show(Venture $venture)
    {
        $venture->load([
            'updates.user',
            'projects' => fn ($q) => $q->with('client')->latest()->limit(50),
        ]);

        return ApiJson::ok($this->venturePayload($venture, false));
    }

    public function addUpdate(Request $request, Venture $venture)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'type' => 'required|in:'.implode(',', VentureUpdate::TYPES),
        ]);

        $u = VentureUpdate::create([
            'venture_id' => $venture->id,
            'user_id' => $request->user()->id,
            'title' => $data['title'],
            'content' => $data['content'] ?? null,
            'type' => $data['type'],
        ]);

        return ApiJson::created(['id' => $u->id], 'Venture update created successfully');
    }

    /**
     * @return array<string, mixed>
     */
    private function venturePayload(Venture $v, bool $summaryOnly): array
    {
        $last = $v->lastUpdate;
        $base = [
            'id' => $v->id,
            'name' => $v->name,
            'slug' => $v->slug,
            'description' => $v->description,
            'status' => $v->status,
            'status_color' => $v->status_color,
            'partner_name' => $v->partner_name,
            'partner_funded' => $v->partner_funded,
            'color' => $v->color,
            'icon' => $v->icon,
            'open_projects_count' => $v->open_projects_count,
            'open_tasks_count' => $v->open_tasks_count,
            'last_update' => $last ? [
                'id' => $last->id,
                'title' => $last->title,
                'type' => $last->type,
                'created_at' => $last->created_at?->toIso8601String(),
                'user' => $last->user ? ['id' => $last->user->id, 'name' => $last->user->name] : null,
            ] : null,
        ];

        if ($summaryOnly) {
            return $base;
        }

        $base['finance'] = [
            'received' => $v->financeReceivedTotal(),
            'given' => $v->financeGivenTotal(),
            'net' => $v->financeReceivedTotal() - $v->financeGivenTotal(),
        ];

        $base['updates'] = $v->updates->map(fn (VentureUpdate $u) => [
            'id' => $u->id,
            'title' => $u->title,
            'content' => $u->content,
            'type' => $u->type,
            'type_icon' => $u->type_icon,
            'created_at' => $u->created_at?->toIso8601String(),
            'user' => $u->user ? ['id' => $u->user->id, 'name' => $u->user->name] : null,
        ]);

        $base['projects'] = $v->projects->map(fn ($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'status' => $p->status,
            'client' => $p->client ? ['id' => $p->client->id, 'name' => $p->client->name] : null,
        ]);

        return $base;
    }
}
