<?php

use App\Http\Controllers\AuthActionController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aquí puedes registrar rutas web para tu aplicación.
| Estas rutas se cargan a través del RouteServiceProvider dentro
| del grupo que contiene el middleware "web".
|
*/

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
|
| Estas rutas no requieren autenticación y permiten acceder a las vistas
| de login y registro, así como procesar las solicitudes de inicio de sesión
| y registro de nuevos usuarios.
|
*/


// Ruta para redirigir a login si no está autenticado
Route::get('/', function () {
    return redirect()->route('login.view');
})->middleware('guest');


Route::middleware('guest')->group(function () {

    // Ruta para mostrar la vista de login (inicio de sesión)
    Route::get('/login', [AuthController::class, 'showLoginView'])->name('login.view');

    // Ruta para mostrar la vista de registro (crear una nueva cuenta)
    Route::get('/register', [AuthController::class, 'showRegisterView'])->name('register.view');

    // Ruta para realizar el registro de un nuevo usuario (procesa la información del formulario de registro)
    Route::post('/register', [AuthActionController::class, 'register'])->name('register.perform');

    // Ruta para realizar el login (procesa la autenticación del usuario)
    Route::post('/login', [AuthActionController::class, 'login'])->name('login.perform');
/*
|--------------------------------------------------------------------------
| Rutas de Códigos de Verificación
|--------------------------------------------------------------------------
|
| Estas rutas están relacionadas con el proceso de autenticación de dos factores (2FA).
| Incluyen vistas para ingresar y verificar códigos, y rutas para manejar la verificación.
|
*/

    Route::middleware('code.verify')->group(function () {
        // Ruta para mostrar la vista donde el usuario ingresa el código de autenticación 2FA
        Route::get('/two-factor/code', [AuthController::class, 'showTwoFaCodeView'])->name('code.two-factor');

        // Ruta para mostrar la vista cuando el código de verificación está inactivo (no verificado)
        Route::get('/code/inactive', [AuthController::class, 'showUnVerifyCodeView'])->name('code.inactive');

        // Ruta para mostrar la vista para verificar el código ingresado
        Route::get('/code/verify', [AuthController::class, 'showVerifyCodeView'])->name('code.verify');

        /*
        |--------------------------------------------------------------------------
        | Rutas de Lógica de Códigos
        |--------------------------------------------------------------------------
        |
        | Estas rutas se encargan de la validación, reenvío y procesamiento de los códigos
        | de verificación, ya sea para la verificación de 2FA o otros códigos en el sistema.
        |
        */

        // Ruta para reenviar el código de verificación al usuario
        Route::post('/resend/verification/code', [AuthActionController::class, 'resendCode'])->name('resend.verification');

        // Ruta para verificar el código de autenticación de dos factores (2FA)
        Route::post('/two-factor/verify/code', [AuthActionController::class, 'validateTwoFaCode'])->name('two-factor.verify');

        // Ruta para verificar el código de autenticación de dos factores (2FA)
        Route::post('/resend/two-factor/verify/code', [AuthActionController::class, 'resendTwoFaCode'])->name('two-factor.resend');

        // Ruta para validar un código ingresado (posiblemente para otros tipos de verificación)
        Route::post('/code', [AuthActionController::class, 'validateCode'])->name('code.perform');

    });
});
/*
|--------------------------------------------------------------------------
| Rutas Protegidas
|--------------------------------------------------------------------------
|
| Estas rutas requieren que el usuario esté autenticado y haya pasado el proceso de
| verificación 2FA. Son accesibles solo después de que el usuario haya completado
| correctamente esos dos pasos de seguridad.
|
*/

// Grupo de rutas que requieren que el usuario esté autenticado y haya pasado el 2FA
Route::middleware(['2fa', 'auth'])->group(function () {
    // Ruta para mostrar la vista principal del usuario después de la autenticación
    Route::get('/home', [AuthController::class, 'showHomeView'])->name('home');
    Route::post('/logout', [AuthActionController::class, 'logout'])->name('user.logout');
});
