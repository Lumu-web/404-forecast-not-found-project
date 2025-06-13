<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function success(array $data, int $status = 200): JsonResponse
    {
        return response()->json($data, $status);
    }

    protected function error(string $message, int $status, array $errors = []): JsonResponse
    {
        $payload = ['message' => $message];
        if ($errors) {
            $payload['errors'] = $errors;
        }
        return response()->json($payload, $status);
    }
}

