<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        ValidationException::class => 'info',
        AuthenticationException::class => 'warning',
        NotFoundHttpException::class => 'warning',
        TooManyRequestsHttpException::class => 'notice',
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        AuthenticationException::class,
        ValidationException::class,
        NotFoundHttpException::class,
        ModelNotFoundException::class,
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
        'new_password',
        'api_key',
        'secret',
        'token',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            $this->logError($e);
        });

        // Handle authentication exceptions
        $this->renderable(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Authentication required',
                    'error' => 'unauthenticated',
                    'status_code' => 401
                ], 401);
            }
        });

        // Handle rate limiting exceptions
        $this->renderable(function (TooManyRequestsHttpException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Too many requests. Please try again later.',
                    'error' => 'rate_limit_exceeded',
                    'status_code' => 429,
                    'retry_after' => $e->getHeaders()['Retry-After'] ?? 60
                ], 429, $e->getHeaders());
            }
        });

        // Handle validation exceptions
        $this->renderable(function (ValidationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Validation failed',
                    'error' => 'validation_error',
                    'status_code' => 422,
                    'errors' => $e->errors()
                ], 422);
            }
        });

        // Handle not found exceptions
        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Resource not found',
                    'error' => 'not_found',
                    'status_code' => 404
                ], 404);
            }
        });

        // Handle model not found exceptions
        $this->renderable(function (ModelNotFoundException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                $model = class_basename($e->getModel());
                return response()->json([
                    'message' => "{$model} not found",
                    'error' => 'model_not_found',
                    'status_code' => 404,
                    'model' => $model
                ], 404);
            }
        });

        // Handle generic HTTP exceptions
        $this->renderable(function (HttpException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage() ?: 'An error occurred',
                    'error' => 'http_error',
                    'status_code' => $e->getStatusCode()
                ], $e->getStatusCode());
            }
        });

        // Handle all other exceptions
        $this->renderable(function (Throwable $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
                
                $response = [
                    'message' => app()->environment('production') ? 'Internal server error' : $e->getMessage(),
                    'error' => 'internal_error',
                    'status_code' => $statusCode
                ];

                // Add debug information in non-production environments
                if (!app()->environment('production')) {
                    $response['debug'] = [
                        'exception' => get_class($e),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => collect($e->getTrace())->take(5)->toArray() // Limit trace for readability
                    ];
                }

                return response()->json($response, $statusCode);
            }
        });
    }

    /**
     * Enhanced error logging with context
     */
    protected function logError(Throwable $exception): void
    {
        $request = request();
        $user = $request?->user();

        $context = [
            'exception' => [
                'class' => get_class($exception),
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ],
            'request' => [
                'method' => $request?->method(),
                'url' => $request?->fullUrl(),
                'ip' => $request?->ip(),
                'user_agent' => $request?->userAgent(),
                'headers' => $this->sanitizeHeaders($request?->headers->all() ?? []),
            ],
            'user' => $user ? [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
            ] : null,
            'timestamp' => now()->toISOString(),
            'environment' => app()->environment(),
        ];

        // Add request data for certain types of errors
        if ($exception instanceof ValidationException) {
            $context['validation_errors'] = $exception->errors();
        }

        // Log based on severity
        $level = $this->getLogLevel($exception);
        Log::log($level, $exception->getMessage(), $context);

        // Send to monitoring service in production
        if (app()->environment('production')) {
            $this->sendToMonitoringService($exception, $context);
        }
    }

    /**
     * Determine log level based on exception type
     */
    protected function getLogLevel(Throwable $exception): string
    {
        if ($exception instanceof ValidationException) {
            return 'info';
        }

        if ($exception instanceof AuthenticationException || 
            $exception instanceof NotFoundHttpException ||
            $exception instanceof ModelNotFoundException) {
            return 'warning';
        }

        if ($exception instanceof TooManyRequestsHttpException) {
            return 'notice';
        }

        if ($exception instanceof HttpException && $exception->getStatusCode() < 500) {
            return 'warning';
        }

        return 'error';
    }

    /**
     * Sanitize headers to remove sensitive information
     */
    protected function sanitizeHeaders(array $headers): array
    {
        $sensitiveHeaders = [
            'authorization',
            'cookie',
            'x-api-key',
            'x-auth-token',
        ];

        foreach ($sensitiveHeaders as $header) {
            if (isset($headers[$header])) {
                $headers[$header] = ['[REDACTED]'];
            }
        }

        return $headers;
    }

    /**
     * Send error to monitoring service
     */
    protected function sendToMonitoringService(Throwable $exception, array $context): void
    {
        try {
            // Log to monitoring channel for external service integration
            Log::channel('monitoring')->error('Exception occurred', [
                'exception' => $exception,
                'context' => $context,
                'fingerprint' => $this->generateFingerprint($exception),
            ]);
        } catch (Throwable $e) {
            // Don't let monitoring failures break the application
            Log::error('Failed to send error to monitoring service', [
                'monitoring_error' => $e->getMessage(),
                'original_exception' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * Generate a unique fingerprint for the error
     */
    protected function generateFingerprint(Throwable $exception): string
    {
        return md5(
            get_class($exception) . 
            $exception->getFile() . 
            $exception->getLine() . 
            $exception->getMessage()
        );
    }

    /**
     * Convert an authentication exception into a response.
     */
    protected function unauthenticated($request, AuthenticationException $exception): Response
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Authentication required',
                'error' => 'unauthenticated',
                'status_code' => 401
            ], 401);
        }

        return redirect()->guest(route('login'));
    }

    /**
     * Get the default context variables for logging.
     */
    protected function context(): array
    {
        try {
            return array_filter([
                'userId' => auth()->id(),
                'email' => auth()->user()?->email,
            ]);
        } catch (Throwable) {
            return [];
        }
    }
}
