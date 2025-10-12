<?php
namespace App\Exceptions;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Database\QueryException;
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
        $this->renderable(function (Throwable $e, $request) {
            if ($request->expectsJson()) {
                // :bricks: Validation exception
                if ($e instanceof ValidationException) {
                    return response()->json([
                        'status' => 422,
                        'message' => collect($e->errors())->flatten()->first(),
                        'data' => [],
                    ], 422);
                }
                // :closed_lock_with_key: Authentication error
                if ($e instanceof AuthenticationException) {
                    return response()->json([
                        'status' => 401,
                        'message' => 'Unauthenticated',
                        'data' => []
                    ], 401);
                }
                // :no_entry_sign: Too many requests
                if ($e instanceof ThrottleRequestsException) {
                    return response()->json([
                        'status' => 429,
                        'message' => 'Too many requests. Please try again later.',
                        'data' => []
                    ], 429);
                }
                // :x: Model not found (e.g. model binding failure)
                if ($e instanceof ModelNotFoundException) {
                    return response()->json([
                        'status' => 404,
                        'message' => 'Record not found.',
                        'data' => []
                    ], 404);
                }
                // :x: Route not found
                if ($e instanceof NotFoundHttpException) {
                    return response()->json([
                        'status' => 404,
                        'message' => 'Resource not found.',
                        'data' => []
                    ], 404);
                }
                // :gear: Database errors
                if ($e instanceof QueryException) {
                    return response()->json([
                        'status' => 500,
                        'message' => config('app.debug')
                            ? $e->getMessage()
                            : 'A database error occurred. Please contact support.',
                    ], 500);
                }
                // :boom: Fallback for any unhandled exception
                return response()->json([
                    'status' => 500,
                    'message' => config('app.debug')
                        ? $e->getMessage()
                        : 'An unexpected error occurred. Please try again later.',
                    'trace' => config('app.debug') ? $e->getTrace() : null,
                    'data' => []
                ], 500);
            }
        });
    }
}
