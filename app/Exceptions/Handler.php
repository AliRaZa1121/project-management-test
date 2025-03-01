<?php

namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
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
        $this->reportable(function (Throwable $e) {});
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request  $request
     * @param \Throwable  $exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request, Throwable $exception): JsonResponse
    {
        // Get the dynamic status code based on the exception type
        $statusCode = $this->getStatusCode($exception);

        // Handle validation exceptions
        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            return ResponseHelper::error('Validation failed', $statusCode, $exception->errors());
        }

        // Handle model not found exceptions
        if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return ResponseHelper::error('Resource not found', $statusCode);
        }

        // Handle authentication exceptions
        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            return ResponseHelper::error('Unauthenticated', $statusCode);
        }

        // Handle authorization exceptions
        if ($exception instanceof \Illuminate\Auth\Access\AuthorizationException) {
            return ResponseHelper::error('Forbidden', $statusCode);
        }

        // Handle other general exceptions
        if ($exception instanceof \Exception) {
            return ResponseHelper::error('An error occurred', $statusCode, [$exception->getMessage()]);
        }

        // Return default response for unhandled exceptions
        return parent::render($request, $exception);
    }

    /**
     * Get the dynamic status code based on the exception type.
     *
     * @param \Throwable $exception
     * @return int
     */
    private function getStatusCode(Throwable $exception): int
    {
        // Handle validation exceptions
        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            return 422; // Unprocessable Entity
        }

        // Handle model not found exceptions (e.g., for APIs when the resource does not exist)
        if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return 404; // Not Found
        }

        // Handle authentication exceptions (e.g., user is not authenticated)
        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            return 401; // Unauthorized
        }

        // Handle authorization exceptions (e.g., user is authenticated but does not have permission)
        if ($exception instanceof \Illuminate\Auth\Access\AuthorizationException) {
            return 403; // Forbidden
        }

        // Handle SQL query exceptions (e.g., malformed query)
        if ($exception instanceof \Illuminate\Database\QueryException) {
            return 400; // Bad Request (query issues, incorrect SQL syntax)
        }

        // Handle HTTP exceptions (e.g., route not found)
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
            return $exception->getStatusCode(); // Use the status code from the HttpException itself
        }

        // Handle 404 errors for resource not found
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            return 404; // Not Found
        }

        // Handle method not allowed exceptions (e.g., route method mismatch)
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
            return 405; // Method Not Allowed
        }

        // Handle too many requests (e.g., rate limiting exceeded)
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException) {
            return 429; // Too Many Requests
        }

        // Handle internal server errors (catch-all for general exceptions)
        if ($exception instanceof \Exception) {
            return 500; // Internal Server Error
        }

        // Default status code for unhandled exceptions
        return 500; // Internal Server Error
    }
}
