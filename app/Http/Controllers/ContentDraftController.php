<?php
namespace App\Http\Controllers;
use App\Models\ContentDraft;
use App\Models\ContentTopic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ContentDraftController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->hasRole('super-admin'), 403);
        $status = $request->query('status', 'all');
        $q = ContentDraft::with('topic')->latest();
        if ($status !== 'all') $q->where('status', $status);
        $drafts = $q->get();
        $counts = [
            'all' => ContentDraft::count(),
            'draft' => ContentDraft::status('draft')->count(),
            'approved' => ContentDraft::status('approved')->count(),
            'scheduled' => ContentDraft::status('scheduled')->count(),
            'posted' => ContentDraft::status('posted')->count(),
        ];
        return view('content-drafts.index', compact('drafts','status','counts'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->hasRole('super-admin'), 403);
        $data = $request->validate([
            'platform' => 'required|string',
            'content_type' => 'required|string',
            'hook' => 'nullable|string',
            'body' => 'required|string',
            'hashtags' => 'nullable|string',
        ]);
        $data['created_by'] = $request->user()->id;
        $data['status'] = 'draft';
        ContentDraft::create($data);
        return back()->with('success', 'Draft saved.');
    }

    public function updateStatus(Request $request, ContentDraft $contentDraft)
    {
        abort_unless(auth()->user()->hasRole('super-admin'), 403);
        $data = $request->validate([
            'status' => 'required|in:draft,approved,scheduled,posted',
            'scheduled_for' => 'nullable|required_if:status,scheduled|date',
            'post_url' => 'nullable|url',
        ]);
        if ($data['status'] === 'posted' && empty($contentDraft->posted_at)) {
            $data['posted_at'] = now();
        }
        if ($data['status'] !== 'scheduled') {
            $data['scheduled_for'] = null;
        }
        $contentDraft->update($data);
        return back()->with('success', 'Updated.');
    }

    // Generate a fresh draft from a topic using Gemini (on-demand, not scheduled)
    public function generate(Request $request)
    {
        abort_unless(auth()->user()->hasRole('super-admin'), 403);
        $topic = ContentTopic::available()->inRandomOrder()->first();
        if (!$topic) return back()->with('error', 'No available topics. Add some first.');

        $apiKey = config('services.gemini.api_key');
        $prompt = "You are Ethan, founder of Aurateria (Laravel studio, Jaipur). Write a LinkedIn post. Founder-to-peer, specific, no engagement bait, 1-2 emoji max. Never combine client revenue+sector+market-count. TOPIC: {$topic->title}. ANGLE: {$topic->angle}. TYPE: {$topic->content_type}. Output STRICT JSON: {\"hook\":\"\",\"body\":\"\",\"hashtags\":\"\"}";

        $resp = Http::timeout(30)->post(
            "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}",
            ['contents'=>[['parts'=>[['text'=>$prompt]]]], 'generationConfig'=>['temperature'=>0.85,'thinkingConfig'=>['thinkingBudget'=>0],'responseMimeType'=>'application/json']]
        );

        $out = ['hook'=>'','body'=>'','hashtags'=>''];
        if ($resp->successful()) {
            $raw = data_get($resp->json(), 'candidates.0.content.parts.0.text', '{}');
            $out = json_decode($raw, true) ?: $out;
        }

        ContentDraft::create([
            'content_topic_id' => $topic->id,
            'platform' => 'linkedin',
            'content_type' => $topic->content_type,
            'hook' => $out['hook'] ?? '',
            'body' => $out['body'] ?? '',
            'hashtags' => $out['hashtags'] ?? '',
            'status' => 'draft',
            'created_by' => $request->user()->id,
        ]);
        $topic->update(['status' => 'used', 'used_at' => now()]);

        return back()->with('success', 'Draft generated from: ' . $topic->title);
    }

    public function destroy(ContentDraft $contentDraft)
    {
        abort_unless(auth()->user()->hasRole('super-admin'), 403);
        $contentDraft->delete();
        return back()->with('success','Deleted.');
    }
}
