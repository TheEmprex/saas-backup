<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\LoggingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response;

/**
 * Base API Controller with consistent response formatting
 */
class BaseController extends Controller
{
    protected LoggingService $logger;

    public function __construct(LoggingService $logger)
    {
        $this->logger = $logger;
        
        // Log API requests
        $this->middleware(function ($request, $next) {
            $startTime = microtime(true);
            
            $response = $next($request);
            
            $this->logger->logApiRequest(
                $request, 
                $response,
                [
                    'execution_time' => microtime(true) - $startTime,
                    'memory_usage' => memory_get_usage(true),
                    'controller' => static::class
                ]
            );
            
            return $response;
        });
    }

    /**
     * Return successful response with data
     */
    protected function successResponse(
        $data = null, 
        string $message = 'Success', 
        int $status = Response::HTTP_OK
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $status);
    }

    /**
     * Return error response
     */
    protected function errorResponse(
        string $message = 'Error occurred',
        int $status = Response::HTTP_INTERNAL_SERVER_ERROR,
        array $errors = [],
        array $metadata = []
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        if (!empty($metadata)) {
            $response['metadata'] = $metadata;
        }

        return response()->json($response, $status);
    }

    /**
     * Return validation error response
     */
    protected function validationErrorResponse(array $errors): JsonResponse
    {
        return $this->errorResponse(
            'Validation failed',
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $errors
        );
    }

    /**
     * Return not found response
     */
    protected function notFoundResponse(string $resource = 'Resource'): JsonResponse
    {
        return $this->errorResponse(
            "{$resource} not found",
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * Return unauthorized response
     */
    protected function unauthorizedResponse(string $message = 'Unauthorized access'): JsonResponse
    {
        return $this->errorResponse(
            $message,
            Response::HTTP_UNAUTHORIZED
        );
    }

    /**
     * Return forbidden response
     */
    protected function forbiddenResponse(string $message = 'Access forbidden'): JsonResponse
    {
        return $this->errorResponse(
            $message,
            Response::HTTP_FORBIDDEN
        );
    }

    /**
     * Return paginated response
     */
    protected function paginatedResponse(
        LengthAwarePaginator $paginator, 
        string $message = 'Data retrieved successfully'
    ): JsonResponse {
        return $this->successResponse([
            'items' => $paginator->items(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'has_more_pages' => $paginator->hasMorePages(),
                'links' => [
                    'first' => $paginator->url(1),
                    'last' => $paginator->url($paginator->lastPage()),
                    'prev' => $paginator->previousPageUrl(),
                    'next' => $paginator->nextPageUrl(),
                ]
            ]
        ], $message);
    }

    /**
     * Return resource response
     */
    protected function resourceResponse(
        JsonResource $resource,
        string $message = 'Data retrieved successfully',
        int $status = Response::HTTP_OK
    ): JsonResponse {
        return $this->successResponse(
            $resource->toArray(request()),
            $message,
            $status
        );
    }

    /**
     * Return resource collection response
     */
    protected function resourceCollectionResponse(
        ResourceCollection $collection,
        string $message = 'Data retrieved successfully'
    ): JsonResponse {
        return $this->successResponse(
            $collection->toArray(request()),
            $message
        );
    }

    /**
     * Return created response
     */
    protected function createdResponse(
        $data = null,
        string $message = 'Resource created successfully'
    ): JsonResponse {
        return $this->successResponse($data, $message, Response::HTTP_CREATED);
    }

    /**
     * Return updated response
     */
    protected function updatedResponse(
        $data = null,
        string $message = 'Resource updated successfully'
    ): JsonResponse {
        return $this->successResponse($data, $message);
    }

    /**
     * Return deleted response
     */
    protected function deletedResponse(string $message = 'Resource deleted successfully'): JsonResponse
    {
        return $this->successResponse(null, $message);
    }

    /**
     * Return no content response
     */
    protected function noContentResponse(): JsonResponse
    {
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Return accepted response (for async operations)
     */
    protected function acceptedResponse(
        $data = null,
        string $message = 'Request accepted for processing'
    ): JsonResponse {
        return $this->successResponse($data, $message, Response::HTTP_ACCEPTED);
    }

    /**
     * Handle exceptions consistently
     */
    protected function handleException(\Exception $exception, string $context = ''): JsonResponse
    {
        // Log the error using our logging service
        $this->logger->logError($exception, [
            'controller' => static::class,
            'context' => $context,
            'action' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2]['function'] ?? 'unknown'
        ]);

        // Return appropriate response based on exception type
        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            // Log validation errors for analysis
            $this->logger->logSecurity('validation_failed', [
                'errors' => $exception->errors(),
                'input' => request()->except(['password', 'password_confirmation', 'token'])
            ], 'warning');
            
            return $this->validationErrorResponse($exception->errors());
        }

        if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return $this->notFoundResponse();
        }

        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            return $this->unauthorizedResponse();
        }

        if ($exception instanceof \Illuminate\Auth\Access\AuthorizationException) {
            return $this->forbiddenResponse($exception->getMessage());
        }

        // Generic server error for other exceptions
        $message = config('app.debug') ? $exception->getMessage() : 'An unexpected error occurred';
        
        return $this->errorResponse(
            $message,
            Response::HTTP_INTERNAL_SERVER_ERROR,
            [],
            config('app.debug') ? [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ] : []
        );
    }

    /**
     * Log user activity
     */
    protected function logActivity(string $action, array $context = []): void
    {
        $this->logger->logUserActivity($action, array_merge([
            'controller' => static::class,
            'route' => request()->route()?->getName()
        ], $context));
    }

    /**
     * Log performance metrics
     */
    protected function logPerformance(string $operation, array $metrics = []): void
    {
        $this->logger->logPerformance($operation, array_merge([
            'controller' => static::class,
            'route' => request()->route()?->getName()
        ], $metrics));
    }

    /**
     * Log audit trail
     */
    protected function logAudit(string $action, $model = null, array $changes = [], array $context = []): void
    {
        $this->logger->logAudit($action, $model, $changes, array_merge([
            'controller' => static::class,
            'route' => request()->route()?->getName()
        ], $context));
    }
}
