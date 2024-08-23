<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Throwable;
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
     * Report or log an exception.
     *
     * @param \Throwable $exception
     * @return void
     *
     * @throws \Exception
     * @throws Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param Throwable $exception
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     * @throws Throwable
     */

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($exception, $request);
        }
        if ($exception instanceof AuthenticationException) {
            return $this->error("Debe iniciar sesión", 400);
        }
        if ($exception instanceof ModelNotFoundException) {
            return $this->error("El dato con el id especificado no existe", 404);
        }
        if ($exception instanceof NotFoundHttpException) {
            return $this->error('La ruta no existe', 404);
        }
        if ($exception instanceof MethodNotAllowedHttpException) {
            return $this->error('El metodo no existe', 404);
        }
        if ($exception instanceof JWTException) {
            return $this->error('Authorization Token not found', 401);
        }
        if ($exception instanceof UnauthorizedException) {
            return $this->error('User does not have the right roles.', 403);
        }
        return parent::render($request, $exception);
    }

    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        $errors = $e->validator->errors()->getMessages();
        return $this->error($errors, 422);
    }
}
