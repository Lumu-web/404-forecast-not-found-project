<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\ResetPasswordService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    public function __construct(
        private ResetPasswordService $resetPasswordService
    ) {}

    public function showResetForm(Request $request, ?string $token = null): Factory
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $result = $this->resetPasswordService->reset($request);

        return $result['success']
            ? redirect()->route('login')->with('status', $result['message'])
            : back()->withErrors(['email' => [$result['message']]]);
    }
}
