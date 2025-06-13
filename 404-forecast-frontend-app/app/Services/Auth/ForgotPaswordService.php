<?php

declare(strict_types=1);

namespace App\Services\Auth;

use Illuminate\Support\Facades\Password;

class ForgotPasswordService
{
    public function sendResetLink(array $credentials): array
    {
        $status = Password::sendResetLink($credentials);

        return [
            'success' => $status === Password::RESET_LINK_SENT,
            'message' => __($status),
        ];
    }
}
