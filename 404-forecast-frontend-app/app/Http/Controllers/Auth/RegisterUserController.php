<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\RegisterUserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RegisterUserController extends Controller
{
    public function __construct(
        private readonly RegisterUserService $registerUserService
    ) {}

    /**
     * Handle an incoming registration request.
     */
    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $payload = $request->only(['name', 'email', 'password', 'password_confirmation']);

        $result = $this->registerUserService->register($payload);

        if (! $result['success']) {
            return redirect()->back()->withErrors(['message' => $result['message']])->withInput();
        }

        $token = $result['access_token'];
        session(['auth_token' => $token]);

        return redirect()->route('dashboard.dashboard')
            ->with('success', 'Registration successful! Welcome to the dashboard.');
    }
}
