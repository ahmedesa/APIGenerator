<?php

namespace essa\APIGenerator\Exceptions;

use Throwable;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use essa\APIGenerator\Http\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class JsonHandler extends ExceptionHandler
{
    use ApiResponse;

    /**
     * Render an exception into an HTTP response.
     *
     * @param                          $request
     * @param                          $exception
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     * @throws Throwable
     */
    public function render($request, $exception)
    {
        if ($request->expectsJson()) {

            if ($exception instanceof ValidationException) {
                return $this->ResponseValidationError($exception);
            }

            if ($exception instanceof QueryException || $exception instanceof ModelNotFoundException) {
                return $this->responseNotFound($exception->getMessage(), Str::title(Str::snake(class_basename($exception), ' ')));
            }

            if ($exception instanceof AuthorizationException) {
                return $this->responseUnAuthorized();
            }

            if ($exception instanceof UnprocessableEntityHttpException) {
                return $this->responseUnprocessable($exception->getMessage(), Str::title(Str::snake(class_basename($exception), ' ')));
            }

            if ($exception instanceof AuthenticationException) {
                return $this->responseUnAuthenticated($exception->getMessage());
            }

            if ($exception instanceof BadRequestHttpException) {
                return $this->responseBadRequest($exception->getMessage(), Str::title(Str::snake(class_basename($exception), ' ')));
            }

            return $this->responseWithCustomError(Str::title(Str::snake(class_basename($exception), ' ')), $exception->getMessage(), $exception->getCode());
        }

        return parent::render($request, $exception);
    }
}
