<?php

namespace App\Exceptions;

use App\Http\Controllers\ErrorController;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
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
     :*
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
    // app/Exceptions/Handler.php
    public function register()
    {
        $this->renderable(function (Throwable $exception, $request) {
            $errorController = new ErrorController();

            // Manejo de errores de base de datos
            if ($exception instanceof \Illuminate\Database\QueryException) {
                return $errorController->handleDatabaseError();
            }

            // Manejo de errores de validación
            if ($exception instanceof \Illuminate\Validation\ValidationException) {
                return $errorController->handleValidationError($exception->validator);
            }

            // Manejo de errores HTTP 500
            if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException && $exception->getStatusCode() === 500) {
                return $errorController->handleHttp500Error();
            }

            // Manejo de errores generales
            return $errorController->handleGeneralError($exception->getMessage());
        });
    }
}
