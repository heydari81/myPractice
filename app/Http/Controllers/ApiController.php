<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    protected function successResponse(int $statusCode, $data, string $message): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'success' => true,
        ], $statusCode);
    }

    protected function errorResponse(int $statusCode, $errors): JsonResponse
    {
        return response()->json([
            'errors' => $errors,
            'message' => 'Validation failed',
            'success' => false,
        ], $statusCode);
    }
}
