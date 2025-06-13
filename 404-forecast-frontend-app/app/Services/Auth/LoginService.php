<?php

declare(strict_types=1);

namespace App\Services\Auth;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class LoginService
{
    /**
     * Authenticate user with the API.
     *
     * @param string $email
     * @param string $password
     * @return array
     * @throws RequestException
     */
    public function authenticate(string $email, string $password): array
    {
        $response = Http::post(config('services.api.url') . '/login', compact('email','password'))
            ->throw();

        return $response->json();
    }

    /**
     * Logout user from the API.
     *
     * @return array
     * @throws RequestException
     */
    public function logout(): array
    {
        $response = Http::post(config('services.api.url') . '/logout')
            ->throw();

        return $response->json();
    }
}
