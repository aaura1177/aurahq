<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Support\ApiJson;
use App\Models\ContentTopic;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ContentTopicApiController extends Controller
{
    // Returns ONE random available topic and marks it used. For the agent.
    public function next(Request $request)
    {
        $type = $request->query('content_type'); // optional filter
        $query = ContentTopic::available();
        if ($type) $query->where('content_type', $type);

        $topic = $query->inRandomOrder()->first();

        // If nothing available, recycle: reset all used topics of this type to available
        if (! $topic) {
            ContentTopic::where('is_active', true)
                ->when($type, fn($q) => $q->where('content_type', $type))
                ->update(['status' => 'available', 'used_at' => null]);
            $topic = ContentTopic::available()
                ->when($type, fn($q) => $q->where('content_type', $type))
                ->inRandomOrder()->first();
        }

        if (! $topic) {
            return ApiJson::ok(null, 'No topics available');
        }

        $topic->update(['status' => 'used', 'used_at' => Carbon::now()]);

        return ApiJson::ok([
            'id' => $topic->id,
            'title' => $topic->title,
            'angle' => $topic->angle,
            'content_type' => $topic->content_type,
        ]);
    }

    public function index()
    {
        return ApiJson::ok(
            ContentTopic::where('is_active', true)->latest()->get()->map(fn($t) => [
                'id' => $t->id, 'title' => $t->title, 'angle' => $t->angle,
                'content_type' => $t->content_type, 'status' => $t->status,
            ])->values()->all()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'angle' => 'nullable|string',
            'content_type' => 'nullable|in:technical,win,founder',
        ]);
        $topic = ContentTopic::create([
            'title' => $request->title,
            'angle' => $request->angle,
            'content_type' => $request->content_type ?? 'technical',
            'created_by' => $request->user()->id,
        ]);
        return ApiJson::created(['id' => $topic->id], 'Topic added');
    }
}
