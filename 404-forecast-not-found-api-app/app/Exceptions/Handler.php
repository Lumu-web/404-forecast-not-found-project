<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use App\Traits\ApiResponse;

class Handler extends ExceptionHandler
{
    use ApiResponse;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        //
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // 1) Validation errors → let Laravel handle and return 422
        if ($e instanceof ValidationException) {
            // This will automatically return:
            // 422 + { message: "The given data was invalid.", errors: { … } }
            return parent::render($request, $e);
        }

        // 2) Our custom AuthenticationException → return 401 + JSON
        if ($e instanceof AuthenticationException) {
            return $this->error(
                $e->getMessage(),
                $e->getCode() === 0 ? 401 : $e->getCode()
            );
        }

        // 3) Fallback for any other exception → default handling (500, etc.)
        return parent::render($request, $e);
    }
}
