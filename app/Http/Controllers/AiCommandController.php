<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\AiController as ApiAiController;
use Illuminate\Http\Request;

/**
 * Web wrapper around the API AI command handler (session-auth friendly).
 */
class AiCommandController extends Controller
{
    public function __invoke(Request $request, ApiAiController $ai)
    {
        abort_unless($request->user()->hasRole('super-admin'), 403);

        $request->validate(['command' => 'required|string|max:2000']);

        return $ai->handleCommand($request);
    }
}
