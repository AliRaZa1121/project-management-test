<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    /**
     * Format a successful response.
     *
     * @param string $message
     * @param mixed $data
     * @param int $statusCode
     * @return JsonResponse
     */
    public static function success(string $message, $data = null, int $statusCode = 200): JsonResponse
    {
        $response = [
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ];

        return response()->json($response, $statusCode);
    }

    /**
     * Format an error response.
     *
     * @param string $message
     * @param int $statusCode
     * @param array $errors
     * @return JsonResponse
     */
    public static function error(string $message, int $statusCode = 400, array $errors = []): JsonResponse
    {
        $response = [
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
        ];

        return response()->json($response, $statusCode);
    }
}
