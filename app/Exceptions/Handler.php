<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
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
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        // Handle API requests
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->handleApiException($request, $e);
        }

        return parent::render($request, $e);
    }

    /**
     * Handle API exceptions and return JSON responses
     *
     * @param Request $request
     * @param Throwable $e
     * @return JsonResponse
     */
    protected function handleApiException(Request $request, Throwable $e): JsonResponse
    {
        // Authentication Exception
        if ($e instanceof AuthenticationException) {
            return response()->json([
                'error' => 'Unauthenticated',
                'message' => 'Please log in to access this resource.'
            ], 401);
        }

        // Authorization Exception
        if ($e instanceof AuthorizationException) {
            return response()->json([
                'error' => 'Forbidden',
                'message' => 'You do not have permission to access this resource.'
            ], 403);
        }

        // Validation Exception
        if ($e instanceof ValidationException) {
            return response()->json([
                'error' => 'Validation Failed',
                'message' => 'The given data was invalid.',
                'errors' => $e->errors()
            ], 422);
        }

        // Model Not Found Exception
        if ($e instanceof ModelNotFoundException) {
            return response()->json([
                'error' => 'Resource Not Found',
                'message' => 'The requested resource was not found.'
            ], 404);
        }

        // Not Found Exception
        if ($e instanceof NotFoundHttpException) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'The requested endpoint was not found.'
            ], 404);
        }

        // Method Not Allowed Exception
        if ($e instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'error' => 'Method Not Allowed',
                'message' => 'The HTTP method is not allowed for this endpoint.'
            ], 405);
        }

        // Too Many Requests Exception
        if ($e instanceof TooManyRequestsHttpException) {
            return response()->json([
                'error' => 'Too Many Requests',
                'message' => 'Rate limit exceeded. Please try again later.'
            ], 429);
        }

        // Database Connection Exception
        if ($e instanceof \PDOException || $e instanceof \Illuminate\Database\QueryException) {
            // Log the actual error for debugging
            \Log::error('Database error: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Database Error',
                'message' => 'A database error occurred. Please try again later.'
            ], 500);
        }

        // File Upload Exception
        if ($e instanceof \Symfony\Component\HttpFoundation\File\Exception\FileException) {
            return response()->json([
                'error' => 'File Upload Error',
                'message' => 'There was an error uploading the file. Please try again.'
            ], 400);
        }

        // Generic Server Error
        if (app()->environment('production')) {
            // In production, don't expose internal error details
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 500);
        } else {
            // In development, show detailed error information
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'error' => 'Unauthenticated',
                'message' => 'Please log in to access this resource.'
            ], 401);
        }

        return redirect()->guest(route('login'));
    }
}
