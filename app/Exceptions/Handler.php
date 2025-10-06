<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     * This prevents sensitive data like passwords from being stored in the session.
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
     * Currently not needed for our simple 404 handling, but kept for future extensibility.
     */
    public function register(): void
    {
        // You can add custom exception reporting/logging here if needed
        // For example: $this->reportable(function (Throwable $e) { ... });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception): \Symfony\Component\HttpFoundation\Response
    {
        // Handle 404 errors with custom template
        if ($exception instanceof NotFoundHttpException || $exception instanceof ModelNotFoundException) {
            return $this->render404($request, $exception);
        }

        return parent::render($request, $exception);
    }

    /**
     * Render a 404 error using the custom 404.blade.php template
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    protected function render404(Request $request, Throwable $exception): Response
    {
        // Set the proper HTTP status code
        $response = response()->view('404', [], 404);
        
        // Add proper headers
        $response->header('Content-Type', 'text/html; charset=UTF-8');
        
        return $response;
    }
}
