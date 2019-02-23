<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Http\JsonResponse;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param \Exception $exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $exception
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        // Handle built-in exceptions
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            $response['message'] = 'Not found';

            return response()->json($response, JsonResponse::HTTP_NOT_FOUND);
        }
        if ($exception instanceof \Illuminate\Auth\Access\AuthorizationException) {
            $response['message'] = 'Forbidden';

            return response()->json($response, JsonResponse::HTTP_FORBIDDEN);
        }
        if ($exception instanceof \Illuminate\Auth\AuthenticationException ||
            $exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
            $response['message'] = 'Unauthorized';
            if ($exception->getMessage()) {
                $response['message'] = $exception->getMessage();
            }

            return response()->json($response, JsonResponse::HTTP_UNAUTHORIZED);
        }
        if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            $response['message'] = 'Data not found';

            return response()->json($response, JsonResponse::HTTP_BAD_REQUEST);
        }
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\BadRequestHttpException) {
            $response['message'] = $exception->getMessage();

            return response()->json($response, JsonResponse::HTTP_BAD_REQUEST);
        }
        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            $response['message'] = 'Validation error';
            $response['errors'] = $exception->errors();

            return response()->json($response, JsonResponse::HTTP_BAD_REQUEST);
        }

        // Handle custom exception

        // Here is unhandled Exception.

        $response['message'] = $exception->getMessage();

        if (env('APP_DEBUG')) {
            $response['file'] = $exception->getFile();
            $response['line'] = $exception->getLine();
        }

        return response()->json(
            $response,
            JsonResponse::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
