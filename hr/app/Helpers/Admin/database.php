<?php
namespace App\Helpers\Admin;

use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;

if (!function_exists('handleDatabaseError')) {
    function handleDatabaseError(Throwable $exception)
    {
        if ($exception instanceof UniqueConstraintViolationException || 
            ($exception instanceof QueryException && $exception->errorInfo[1] === 1062)) {
            
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage(),
                'message1' => 'Duplicate entry detected. Please provide a unique value.',


            ], 409);
        }

        return response()->json([
            'error' => true,
            'message' => $exception->getMessage(),
        ], 500);
    }
}
