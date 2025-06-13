<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\Auth\LoginService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;

class LoginController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param LoginService $loginService
     */
    public function __construct(private LoginService $loginService)
    {
    }

    /**
     * Handle the incoming login request.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $data = $this->loginService->authenticate(
                $request->email,
                $request->password
            );

            session([
                'auth_token' => $data['access_token'],
                'user' => $data['user'],
            ]);

            return response()->json([
                'redirect_url' => route('dashboard.dashboard'),
            ]);

        } catch (RequestException $e) {
            $resp = $e->response;
            $message = $resp->json('message', 'Login failed');
            $status = $resp->status() ?? 422;

            return response()->json(['message' => $message], $status);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            $this->loginService->logout();

            session()->forget(['auth_token', 'user']);

            return response()->json(['message' => 'Logged out successfully.']);
        } catch (RequestException $e) {
            $resp = $e->response;
            $message = $resp->json('message', 'Logout failed');
            $status = $resp->status() ?? 422;

            return response()->json(['message' => $message], $status);
        }
    }
}
