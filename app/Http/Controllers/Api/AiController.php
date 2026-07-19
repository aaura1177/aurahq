<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiJson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Task;

class AiController extends Controller
{
    public function handleCommand(Request $request)
    {
        $user = $request->user();
        $command = $request->input('command');

        if (! $command) {
            return response()->json(['message' => 'No command provided'], 422);
        }

        try {
            $apiKey = config('services.gemini.api_key');
            $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apiKey;

            $tools = [[
                'functionDeclarations' => [
                    [
                        'name' => 'create_task',
                        'description' => 'Create a new task',
                        'parameters' => [
                            'type' => 'object',
                            'properties' => [
                                'title' => ['type' => 'string'],
                                'priority' => ['type' => 'string', 'enum' => ['normal', 'urgent', 'critical']],
                                'category' => ['type' => 'string', 'enum' => ['admin_personal', 'employee_assignment']],
                            ],
                            'required' => ['title'],
                        ],
                    ],
                ],
            ]];

            $payload = [
                'contents' => [[
                    'role' => 'user',
                    'parts' => [['text' => 'You interpret management commands and call the appropriate function. Current date: ' . now() . '. Command: ' . $command]],
                ]],
                'tools' => $tools,
                'generationConfig' => ['temperature' => 0.2, 'thinkingConfig' => ['thinkingBudget' => 0]],
            ];

            $response = Http::timeout(30)->post($url, $payload);

            if (! $response->successful()) {
                Log::error('Gemini AI error: ' . $response->body());
                return response()->json(['message' => 'AI service error. Check GEMINI_API_KEY and billing.'], 502);
            }

            $data = $response->json();
            $parts = $data['candidates'][0]['content']['parts'] ?? [];

            $results = [];
            foreach ($parts as $part) {
                if (! isset($part['functionCall'])) continue;
                $fnName = $part['functionCall']['name'] ?? '';
                $args = $part['functionCall']['args'] ?? [];

                if ($fnName === 'create_task') {
                    $category = in_array($args['category'] ?? null, ['admin_personal', 'employee_assignment'])
                        ? $args['category'] : 'admin_personal';
                    Task::create([
                        'title' => $args['title'],
                        'priority' => $args['priority'] ?? 'normal',
                        'category' => $category,
                        'created_by' => $user->id,
                        'status' => 'pending',
                        'is_active' => true,
                    ]);
                    $results[] = "Task '{$args['title']}' created successfully.";
                }
            }

            if (! empty($results)) {
                return ApiJson::ok(['results' => $results], implode(' ', $results));
            }

            return ApiJson::ok([], "I heard you, but wasn't sure what action to take.");
        } catch (\Exception $e) {
            Log::error('AI Error: ' . $e->getMessage());
            return response()->json(['message' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }
}
