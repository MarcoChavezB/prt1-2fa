<?php

namespace App\Http\Controllers;

use App\Mail\SendVerificationCode;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Resend\Laravel\Facades\Resend;

class VerifyCodeController extends Controller
{
    // Tiempo de expiración del código de verificación (en minutos)
    public int $timeExpirationCode = 10;

    // Nombre de la sesión donde se guarda el correo del usuario
    public string $userEmailSessionName = 'user_email';

    /*
    |--------------------------------------------------------------------------
    | Generar Código de Verificación
    |--------------------------------------------------------------------------
    |
    | Este método genera un código de verificación aleatorio de 6 dígitos.
    | El código generado será siempre de 6 dígitos, incluso si el número es menor.
    |
    | @return string Código de verificación generado.
    |
    */
    public function generateCode() : string {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /*
    |--------------------------------------------------------------------------
    | Verificar Código de Verificación
    |--------------------------------------------------------------------------
    |
    | Este método verifica si el código de verificación proporcionado por el usuario
    | es válido, es decir, si coincide con el código almacenado en la base de datos
    | y si no ha expirado.
    |
    | @param string $code Código de verificación proporcionado por el usuario.
    | @param User $user El usuario al que se le verifica el código.
    | @return bool True si el código es válido, False si no lo es.
    |
    */
    public function verifyCode(string $code, User $user): bool
    {
        // Verificar si el código ha expirado
        if ($user->verification_code_expires_at && now()->gt($user->verification_code_expires_at)) {
            return false; // El código ha expirado
        }

        // Verificar si el código proporcionado coincide con el código hasheado
        if (Hash::check($code, $user->verification_code)) {
            return true; // El código es válido
        }

        return false; // El código no coincide
    }

    /*
    |--------------------------------------------------------------------------
    | Enviar Código de Verificación
    |--------------------------------------------------------------------------
    |
    | Este método envía un código de verificación al correo electrónico del usuario.
    | Utiliza la clase `SendVerificationCode` para enviar el código.
    |
    | @param string $email El correo electrónico al que se enviará el código.
    | @param string $verificationCode El código de verificación que se enviará.
    |
    */
    public function sendCode(string $email, string $verificationCode)
    {
        // Enviar el código de verificación por correo electrónico

        Resend::emails()->send([
            'from' => 'infotrc@aviafly.mx',
            'to' => [$email],
            'subject' => 'Confirmación de Activación de Cuenta',
            'html' => view('email.code_email', ['code' => $verificationCode])->render(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Verificar al Usuario
    |--------------------------------------------------------------------------
    |
    | Este método marca la cuenta del usuario como verificada, es decir, asigna la
    | fecha y hora actual al campo `email_verified_at` y elimina el código de
    | verificación y su fecha de expiración.
    |
    | @param User $user El usuario que será marcado como verificado.
    |
    */
    public function verifyUser(User $user)
    {
        // Marcar al usuario como verificado
        $user->email_verified_at = now();
        $user->verification_code = null;
        $user->verification_code_expires_at = null;
        $user->save();
    }

    /*
    |--------------------------------------------------------------------------
    | Actualizar Código de Verificación
    |--------------------------------------------------------------------------
    |
    | Este método actualiza el código de verificación para un usuario, generando
    | un nuevo código, cifrándolo y estableciendo una nueva fecha de expiración.
    | Si el código es actualizado, el campo `email_verified_at` es establecido
    | a `null`, ya que el usuario debe volver a verificar su correo.
    |
    | @param User $user El usuario para el cual se actualizará el código de verificación.
    | @param string $code El nuevo código de verificación.
    |
    */
    public function updateCode(User $user, string $code)
    {
        // Asignar el código de verificación cifrado y la fecha de expiración
        $user->verification_code = Hash::make($code);
        $user->verification_code_expires_at = now()->addMinutes($this->timeExpirationCode);
        $user->email_verified_at = null; // Eliminar la verificación anterior
        $user->save();
    }
}
