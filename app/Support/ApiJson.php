<?php

namespace App\Support;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

/**
 * Standard JSON shapes for /api routes (MODULE 8).
 */
final class ApiJson
{
    public static function ok(mixed $data, string $message = 'Success', int $status = 200): JsonResponse
    {
        return response()->json(['data' => $data, 'message' => $message], $status);
    }

    public static function created(mixed $data, string $message = 'Created successfully'): JsonResponse
    {
        return response()->json(['data' => $data, 'message' => $message], 201);
    }

    /**
     * @param  callable(mixed): mixed|null  $map
     */
    public static function paginated(LengthAwarePaginator $paginator, ?callable $map = null): JsonResponse
    {
        $items = collect($paginator->items());
        $data = $map !== null
            ? $items->map($map)->values()->all()
            : $items->values()->all();

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public static function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return response()->json(['message' => $message], 403);
    }

    public static function noContent(): JsonResponse
    {
        return response()->json(null, 204);
    }
}
