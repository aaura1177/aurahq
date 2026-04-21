<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiJson;
use Illuminate\Http\Request;
use OpenAI;
use App\Models\Task;
use App\Models\GroceryListItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AiController extends Controller
{
    public function handleCommand(Request $request) {
        $user = $request->user();
        $command = $request->input('command'); 

        try {
            $client = OpenAI::client(config('services.openai.api_key'));

            // We tell the AI what tools it has available
            $response = $client->chat()->create([
                'model' => 'gpt-3.5-turbo', // Cheaper model
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant for a management system. You interpret commands and output JSON. Current Date: ' . now()],
                    ['role' => 'user', 'content' => $command],
                ],
                'tools' => [
                    [
                        'type' => 'function',
                        'function' => [
                            'name' => 'create_task',
                            'description' => 'Create a new task',
                            'parameters' => [
                                'type' => 'object',
                                'properties' => [
                                    'title' => ['type' => 'string'],
                                    'priority' => ['type' => 'string', 'enum' => ['normal', 'urgent', 'critical']],
                                    'category' => ['type' => 'string', 'enum' => ['admin_personal', 'employee_assignment']],
                                ],
                                'required' => ['title']
                            ]
                        ]
                    ],
                    [
                        'type' => 'function',
                        'function' => [
                            'name' => 'add_grocery',
                            'description' => 'Add item to grocery list',
                            'parameters' => [
                                'type' => 'object',
                                'properties' => [
                                    'item_name' => ['type' => 'string'],
                                    'type' => ['type' => 'string', 'enum' => ['vegetables', 'blinkit', 'supermart', 'today']],
                                    'qty' => ['type' => 'string']
                                ],
                                'required' => ['item_name']
                            ]
                        ]
                    ]
                ],
                'tool_choice' => 'auto',
            ]);

            $message = $response->choices[0]->message;

            if ($message->toolCalls) {
                $results = [];
                foreach ($message->toolCalls as $toolCall) {
                    $functionName = $toolCall->function->name;
                    $args = json_decode($toolCall->function->arguments, true) ?? [];

                    if ($functionName === 'create_task') {
                        $category = in_array($args['category'] ?? null, ['admin_personal', 'employee_assignment'])
                            ? $args['category']
                            : 'admin_personal';
                        Task::create([
                            'title' => $args['title'],
                            'priority' => $args['priority'] ?? 'normal',
                            'category' => $category,
                            'created_by' => $user->id,
                            'status' => 'pending',
                            'is_active' => true
                        ]);
                        $results[] = "Task '{$args['title']}' created successfully.";
                    }

                    if ($functionName === 'add_grocery') {
                        $type = $args['type'] ?? 'today';
                        $date = ($type == 'today') ? Carbon::today() : null;
                        GroceryListItem::create([
                            'item_name' => $args['item_name'],
                            'type' => $type,
                            'qty' => $args['qty'] ?? '1',
                            'status' => 'pending',
                            'is_active' => true,
                            'date' => $date
                        ]);
                        $results[] = "Added {$args['item_name']} to grocery list.";
                    }
                }
                if (! empty($results)) {
                    return ApiJson::ok(['results' => $results], implode(' ', $results));
                }
            }

            return ApiJson::ok([], "I heard you, but I wasn't sure what action to take.");
        } catch (\Exception $e) {
            Log::error('AI Error: ' . $e->getMessage());

            // Handle Rate Limit specifically
            if (str_contains(strtolower($e->getMessage()), 'rate limit') || str_contains(strtolower($e->getMessage()), 'quota')) {
                return response()->json(['message' => 'AI Limit Exceeded. Please check your OpenAI billing/credits.'], 429);
            }

            return response()->json(['message' => 'Server Error: '.$e->getMessage()], 500);
        }
    }
}