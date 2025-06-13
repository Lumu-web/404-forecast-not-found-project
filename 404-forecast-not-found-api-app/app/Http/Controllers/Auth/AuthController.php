<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Exceptions\AuthenticationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly AuthService $authService
    )
    {
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $payload = $this->authService->login($request->validated());
            return $this->success($payload);
        } catch (AuthenticationException $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $payload = $this->authService->register($request->validated());
        return $this->success($payload, Response::HTTP_CREATED);
    }

    public function logout(): JsonResponse
    {
        $payload = $this->authService->logout();
        return $this->success($payload, Response::HTTP_OK);
    }
}
