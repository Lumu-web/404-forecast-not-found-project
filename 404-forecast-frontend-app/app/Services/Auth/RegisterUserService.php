<?php

declare(strict_types=1);

namespace App\Services\Auth;

use Illuminate\Support\Facades\Http;

class RegisterUserService
{
    public function register(array $validated): array
    {
        $response = Http::post(config('services.api.url') . '/register', $validated);

        if (! $response->successful()) {
            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Registration failed.',
            ];
        }

        $data = $response->json();

        return [
            'success' => true,
            'token' => $data['token'] ?? null,
            'user' => $data['user'] ?? null,
        ];
    }
}
