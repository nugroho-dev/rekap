<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    protected function shouldReturnJson($request, Throwable $e)
    {
        if ($request->is('api/*')) {
            return true;
        }

        return parent::shouldReturnJson($request, $e);
    }

    public function render($request, Throwable $e)
    {
        $response = parent::render($request, $e);

        if (! $request->is('api/*') || ! $this->shouldNormalizeApiExceptionResponse($response)) {
            return $response;
        }

        return response()->json(
            $this->buildApiErrorPayload($e, $response->getStatusCode()),
            $response->getStatusCode(),
            $response->headers->all()
        );
    }

    private function shouldNormalizeApiExceptionResponse($response): bool
    {
        return $response instanceof JsonResponse && $response->getStatusCode() >= 400;
    }

    private function buildApiErrorPayload(Throwable $e, int $status): array
    {
        $message = $this->resolveApiErrorMessage($e, $status);

        return [
            'message' => $message,
            'code' => $this->resolveApiErrorCode($e, $status),
            'status' => $status,
            'errors' => $e instanceof ValidationException ? $e->errors() : null,
        ];
    }

    private function resolveApiErrorMessage(Throwable $e, int $status): string
    {
        if ($e instanceof ValidationException) {
            return $e->getMessage() ?: 'Data yang dikirim tidak valid.';
        }

        if ($e instanceof AuthenticationException) {
            return 'Unauthenticated.';
        }

        if ($e instanceof HttpExceptionInterface) {
            return match ($status) {
                401 => 'Unauthenticated.',
                403 => 'Akses ditolak.',
                404 => 'Resource API tidak ditemukan.',
                405 => 'Method tidak diizinkan untuk endpoint ini.',
                429 => 'Terlalu banyak request.',
                default => ! empty($e->getMessage()) && $status < 500
                    ? $e->getMessage()
                    : 'Terjadi kesalahan pada server.',
            };
        }

        if (! empty($e->getMessage()) && $status < 500) {
            return $e->getMessage();
        }

        return match ($status) {
            401 => 'Unauthenticated.',
            403 => 'Akses ditolak.',
            404 => 'Resource API tidak ditemukan.',
            405 => 'Method tidak diizinkan untuk endpoint ini.',
            422 => 'Data yang dikirim tidak valid.',
            429 => 'Terlalu banyak request.',
            default => 'Terjadi kesalahan pada server.',
        };
    }

    private function resolveApiErrorCode(Throwable $e, int $status): string
    {
        if ($e instanceof ValidationException) {
            return 'VALIDATION_ERROR';
        }

        if ($e instanceof AuthenticationException) {
            return 'UNAUTHENTICATED';
        }

        if ($e instanceof HttpExceptionInterface) {
            return match ($status) {
                403 => 'FORBIDDEN',
                404 => 'NOT_FOUND',
                405 => 'METHOD_NOT_ALLOWED',
                429 => 'TOO_MANY_REQUESTS',
                default => 'HTTP_ERROR',
            };
        }

        return 'SERVER_ERROR';
    }
}
