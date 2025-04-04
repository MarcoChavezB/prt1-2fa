<?php

namespace App\Http\Controllers;

use App\Mail\TwoFactorCode;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Resend\Laravel\Facades\Resend;

class TwoFactorController extends Controller
{
    // Tiempo de expiración del código de 2FA (en minutos)
    public int $timeExpirationCode = 10;

    // Tiempo de expiracion de la session (en minutos)
    public int $sessionExpiresMinutes = 10;

    // Clave de la session
    public string $sessionTwoFactorEmail = 'two_factor_code_email';

    /*
    |--------------------------------------------------------------------------
    | Actualizar Código de 2FA
    |--------------------------------------------------------------------------
    |
    | Este método actualiza el código de autenticación de dos factores (2FA)
    | para un usuario específico. El código es cifrado y se establece una
    | fecha de expiración para el código, que se calcula sumando un tiempo
    | determinado a la fecha actual.
    |
    | @param User $user El usuario al que se le actualizará el código.
    | @param string $code El código de autenticación de dos factores.
    |
    */
    public function updateCode(User $user, $code)
    {
        // Asignar el código 2FA cifrado y la fecha de expiración
        $user->two_factor_code = Hash::make($code);
        $user->two_factor_code_expires_at = now()->addMinutes($this->timeExpirationCode);
        $user->two_factor_expires_at = now()->addMinutes($this->sessionExpiresMinutes);
        $user->save();
    }

    /*
    |--------------------------------------------------------------------------
    | Completar Verificación de 2FA
    |--------------------------------------------------------------------------
    |
    | Este método marca la verificación de dos factores como completada para
    | un usuario. El código de 2FA y su fecha de expiración se eliminan
    | y se marca el campo `two_factor_verified` como `true` en la base de datos.
    |
    | @param User $user El usuario cuya verificación de 2FA se completará.
    |
    */
    public function completeTwoFactorVerification($user)
    {
        // Eliminar el código de 2FA y su fecha de expiración
        $user->two_factor_code = null;
        $user->two_factor_code_expires_at = null;
        $user->two_factor_verified = true;
        $user->two_factor_expires_at = now()->addMinutes($this->sessionExpiresMinutes);
        $user->save();
    }

    /*
    |--------------------------------------------------------------------------
    | Enviar Código de 2FA
    |--------------------------------------------------------------------------
    |
    | Este método envía el código de autenticación de dos factores (2FA) por
    | correo electrónico al usuario. Utiliza la clase de correo `TwoFactorCode`
    | para enviar el código a la dirección de correo proporcionada.
    |
    | @param string $email La dirección de correo electrónico a la que se enviará el código.
    | @param string $code El código de 2FA que será enviado.
    |
    */
    public function sendCode(string $email, string $code)
    {
        // Enviar el código por correo electrónico
        Resend::emails()->send([
            'from' => 'infotrc@aviafly.mx',
            'to' => [$email],
            'subject' => 'Confirmacion de inicio de sesion',
            'html' => view('email.two_factor_code_email', ['code' => $code])->render(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Verificar Código de 2FA
    |--------------------------------------------------------------------------
    |
    | Este método verifica si el código de 2FA ingresado por el usuario es válido
    | y no ha expirado. Si el código es válido y no ha expirado, retorna `true`;
    | en caso contrario, retorna `false`.
    |
    | @param string $code El código de 2FA ingresado por el usuario.
    | @param User $user El usuario para el cual se verificará el código.
    | @return bool Retorna `true` si el código es válido y no ha expirado, `false` si no lo es.
    |
    */
    public function verifyCode(string $code, User $user) : bool
    {
        // Verificar si el código ha expirado
        if ($user->two_factor_code_expires_at && now()->gt($user->two_factor_code_expires_at)) {
            return false;
        }

        // Verificar si el código ingresado es válido
        if (Hash::check($code, $user->two_factor_code)) {
            return true;
        }

        return false;
    }


    /*
    |--------------------------------------------------------------------------
    | Eliminar Sesión de 2FA
    |--------------------------------------------------------------------------
    |
    | Este método desactiva la autenticación de dos factores (2FA) para el usuario
    | proporcionado, eliminando todos los datos relacionados con la verificación
    | de 2FA, como el código, su fecha de expiración y la marca de verificación.
    |
    | Los campos afectados son:
    | - `two_factor_verified`: Se establece como `false`, indicando que el usuario
    |   ya no ha verificado el 2FA.
    | - `two_factor_expires_at`: Se establece como `null`, eliminando la fecha de expiración
    |   del código 2FA.
    | - `two_factor_code`: Se establece como `null`, eliminando el código de 2FA.
    | - `two_factor_code_expires_at`: Se establece como `null`, eliminando la fecha de expiración
    |   del código de 2FA.
    |
    | Finalmente, se guardan los cambios realizados en la base de datos.
    |
    | @param User $user El usuario cuyo 2FA será desactivado.
    | @return void No retorna valor.
    |
    */
    public function deleteTwoFaSession(User $user){
        $user->two_factor_verified = false;
        $user->two_factor_expires_at = null;
        $user->two_factor_code = null;
        $user->two_factor_code_expires_at = null;

        $user->save();
    }
}
