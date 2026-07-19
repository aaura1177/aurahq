<?php

namespace App\Http\Controllers;

use App\Models\ContentTopic;
use Illuminate\Http\Request;

class ContentTopicController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->hasRole('super-admin'), 403);

        $topics = ContentTopic::query()
            ->orderByRaw("CASE status WHEN 'available' THEN 0 ELSE 1 END")
            ->latest()
            ->get();

        return view('content-topics.index', compact('topics'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->hasRole('super-admin'), 403);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'angle' => 'nullable|string',
            'content_type' => 'nullable|in:technical,win,founder',
        ]);

        ContentTopic::create([
            'title' => $data['title'],
            'angle' => $data['angle'] ?? null,
            'content_type' => $data['content_type'] ?? 'technical',
            'status' => 'available',
            'created_by' => $request->user()->id,
            'is_active' => true,
        ]);

        return back()->with('success', 'Topic added.');
    }

    public function update(Request $request, ContentTopic $contentTopic)
    {
        abort_unless(auth()->user()->hasRole('super-admin'), 403);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'angle' => 'nullable|string',
            'content_type' => 'required|in:technical,win,founder',
        ]);

        $contentTopic->update($data);

        return back()->with('success', 'Topic updated.');
    }

    public function recycle(ContentTopic $contentTopic)
    {
        abort_unless(auth()->user()->hasRole('super-admin'), 403);

        $contentTopic->update(['status' => 'available', 'used_at' => null]);

        return back()->with('success', 'Topic marked available again.');
    }

    public function destroy(ContentTopic $contentTopic)
    {
        abort_unless(auth()->user()->hasRole('super-admin'), 403);

        $contentTopic->update(['is_active' => false]);

        return back()->with('success', 'Topic archived.');
    }
}
