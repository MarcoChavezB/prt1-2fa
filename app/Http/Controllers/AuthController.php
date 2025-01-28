<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    // Controlador de verificación de códigos
    private $verifyCodeController;

    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    |
    | El constructor recibe el controlador de verificación de códigos para
    | poder utilizarlo en los métodos que lo necesiten dentro de esta clase.
    |
    */
    public function __construct(VerifyCodeController $verifyCodeController)
    {
        $this->verifyCodeController = $verifyCodeController;
    }

    /*
    |--------------------------------------------------------------------------
    | Mostrar Vista de Login
    |--------------------------------------------------------------------------
    |
    | Este método muestra la vista de login donde el usuario puede ingresar
    | sus credenciales para iniciar sesión.
    |
    | @return \Illuminate\View\View Vista de login
    |
    */
    public function showLoginView()
    {
        return view('auth.login');
    }

    /*
    |--------------------------------------------------------------------------
    | Mostrar Vista de Registro
    |--------------------------------------------------------------------------
    |
    | Este método muestra la vista de registro donde el usuario puede crear
    | una nueva cuenta proporcionando su nombre, correo y contraseña.
    |
    | @return \Illuminate\View\View Vista de registro
    |
    */
    public function showRegisterView()
    {
        return view('auth.register');
    }

    /*
    |--------------------------------------------------------------------------
    | Mostrar Vista de Cuenta No Verificada
    |--------------------------------------------------------------------------
    |
    | Este método muestra la vista para cuando un usuario tiene una cuenta
    | inactiva o no verificada. En esta página, el usuario podrá recibir
    | instrucciones para verificar su cuenta.
    |
    | @return \Illuminate\View\View Vista de cuenta no verificada
    |
    */
    public function showUnVerifyCodeView()
    {
        return view('auth.unactive_account');
    }

    /*
    |--------------------------------------------------------------------------
    | Mostrar Vista de Verificación de Código
    |--------------------------------------------------------------------------
    |
    | Este método muestra la vista para que el usuario ingrese el código
    | de verificación que recibió en su correo. La vista se muestra sólo
    | si el correo del usuario está disponible en la sesión.
    |
    | @return \Illuminate\View\View Vista de verificación de código
    |
    */
    public function showVerifyCodeView()
    {
        // Obtener el correo desde la sesión
        $email = session($this->verifyCodeController->userEmailSessionName);

        // Si no se encontró el correo, redirigir a la vista de registro con un error
        if (!$email) {
            return redirect()->route('register.view')->with('error', 'No se encontró un correo para verificar');
        }

        // Devolver la vista de verificación con el correo como dato
        return view('auth.verify_code', compact('email'));
    }

    /*
    |--------------------------------------------------------------------------
    | Mostrar Vista de Código 2FA
    |--------------------------------------------------------------------------
    |
    | Este método muestra la vista para que el usuario ingrese el código
    | de autenticación de dos factores (2FA) durante el proceso de login.
    |
    | @return \Illuminate\View\View Vista de código 2FA
    |
    */
    public function showTwoFaCodeView()
    {
        return view('auth.two_factor_code');
    }

    /*
    |--------------------------------------------------------------------------
    | Mostrar Vista de Inicio (Home)
    |--------------------------------------------------------------------------
    |
    | Este método muestra la vista principal (home) para los usuarios que
    | han iniciado sesión correctamente y han pasado por el proceso de 2FA.
    |
    | @return \Illuminate\View\View Vista principal (home)
    |
    */
    public function showHomeView()
    {
        return view('principal.home');
    }
}
