<?php

namespace Tests\Unit;

use App\Http\Controllers\AuthActionController;
use App\Http\Controllers\VerifyCodeController;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class VerificationCodeTest extends TestCase
{
    /**
     * Prueba que verifica si el código es válido.
     */
    public function test_verifyCode_returns_true_for_valid_code()
    {
        // Crear un usuario con un código de verificación hasheado
        $user = new User([
            'verification_code' => Hash::make('123456'), // Código hasheado
            'verification_code_expires_at' => Carbon::now()->addMinutes(10), // Código válido por 10 minutos
        ]);

        // Instanciar el servicio
        $service = new VerifyCodeController();

        // Verificar que el código es válido
        $this->assertTrue($service->verifyCode('123456', $user));
    }

    /**
     * Prueba que verifica si el código ha expirado.
     */
    public function test_verifyCode_returns_false_for_expired_code()
    {
        // Crear un usuario con un código de verificación expirado
        $user = new User([
            'verification_code' => Hash::make('123456'), // Código hasheado
            'verification_code_expires_at' => Carbon::now()->subMinutes(10), // Código expirado
        ]);

        // Instanciar el servicio
        $service = new VerifyCodeController();

        // Verificar que el código ha expirado
        $this->assertFalse($service->verifyCode('123456', $user));
    }

    /**
     * Prueba que verifica si el código es incorrecto.
     */
    public function test_verifyCode_returns_false_for_invalid_code()
    {
        // Crear un usuario con un código de verificación hasheado
        $user = new User([
            'verification_code' => Hash::make('123456'), // Código hasheado
            'verification_code_expires_at' => Carbon::now()->addMinutes(10), // Código válido por 10 minutos
        ]);

        // Instanciar el servicio
        $service = new VerifyCodeController();

        // Verificar que el código es incorrecto
        $this->assertFalse($service->verifyCode('654321', $user)); // Código incorrecto
    }
}
