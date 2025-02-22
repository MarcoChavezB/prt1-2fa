<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ErrorController extends Controller
{
    // Códigos de error personalizados
    private const DATABASE_ERROR_CODE = 'DB23';
    private const VALIDATION_ERROR_CODE = 'VAL01';
    private const GENERAL_ERROR_CODE = 'GEN01';
    private const HTTP_500_ERROR_CODE = 'HTTP500';

    /**
     * Manejo de errores de base de datos.
     */
    public function handleDatabaseError()
    {
        $code = self::DATABASE_ERROR_CODE;
        return redirect()->back()->with([
            'error' => "Hubo un problema con la base de datos (Código: {$code}). Por favor, inténtalo más tarde.",
            'code' => $code,
        ]);
    }

    /**
     * Manejo de errores de validación.
     */
    public function handleValidationError($validator)
    {
        $code = self::VALIDATION_ERROR_CODE;
        return redirect()->back()->withErrors($validator)->withInput()->with([
            'error' => "Hubo un error de validación (Código: {$code}). Verifica los campos ingresados.",
            'code' => $code,
        ]);
    }

    /**
     * Manejo de errores generales.
     */
    public function handleGeneralError(string $message = 'Ocurrió un error inesperado.')
    {
        $code = self::GENERAL_ERROR_CODE;
        return redirect()->back()->with([
            'error' => "{$message} (Código: {$code}). Por favor, inténtalo nuevamente.",
            'code' => $code,
        ]);
    }

    /**
     * Manejo de errores HTTP 500.
     */
    public function handleHttp500Error()
    {
        $code = self::HTTP_500_ERROR_CODE;
        return redirect()->back()->with([
            'error' => "Hubo un problema interno en el servidor (Código: {$code}). Por favor, inténtalo más tarde.",
            'code' => $code,
        ]);
    }
}
