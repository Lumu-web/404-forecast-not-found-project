<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\AuthenticationException;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * @throws AuthenticationException
     */
    public function login(array $credentials): array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw new AuthenticationException(
                "The credentials you provided are incorrect." . PHP_EOL . " - not gonna lie, not a good look"
            );
        }

        return [
            'access_token' => $user->createToken('forecast_app')->plainTextToken,
            'token_type'   => 'Bearer',
            'user'         => $user,
        ];
    }

    public function register(array $data): array
    {
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return [
            'access_token' => $user->createToken('forecast_app')->plainTextToken,
            'token_type'   => 'Bearer',
            'user'         => $user,
        ];
    }

    public function logout(): array
    {
        Auth::user()->tokens()->delete();

        return [
            'message' => 'Logged out successfully.',
        ];
    }
}
