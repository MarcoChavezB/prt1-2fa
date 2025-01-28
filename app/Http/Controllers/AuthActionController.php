<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthActionController extends Controller
{
    // Controladores de verificación de código y autenticación 2FA
    private $verifyCodeController;
    private $twoFactorController;

    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    |
    | El constructor recibe dos controladores: uno para la verificación de códigos
    | y otro para la autenticación de dos factores (2FA). Estos controladores
    | se usan en varios métodos dentro de esta clase.
    |
    */
    public function __construct(VerifyCodeController $verifyCodeController, TwoFactorController $twoFactorController)
    {
        $this->verifyCodeController = $verifyCodeController;
        $this->twoFactorController = $twoFactorController;
    }

    /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    |
    | Este método maneja el proceso de login del usuario. Valida las credenciales,
    | verifica la cuenta y gestiona el código de verificación 2FA.
    |
    | @param \Illuminate\Http\Request $request Datos de la solicitud de login
    | @return \Illuminate\Http\RedirectResponse Redirección a la vista de código 2FA
    |
    */
    public function login(Request $request)
    {
        // Validar correo y contraseña
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Si la validación falla, regresar con errores
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        // Buscar al usuario por correo
        $user = User::where('email', $request->email)->first();

        // Si el usuario no existe, redirigir a la página de registro
        if (!$user) {
            return redirect()->route('register.view')->with('error', 'Verifique que sus credenciales sean correctas.');
        }

        // Verificar que la cuenta esté activa (verificada por correo)
        if (!$user->email_verified_at) {
            session([$this->verifyCodeController->userEmailSessionName => $user->email]);
            return redirect()->route('code.inactive');
        }

        // Verificar que la contraseña es correcta
        if (!Hash::check($request->password, $user->password)) {
            return redirect()->back()->with('error', 'Las credenciales no son correctas.');
        }

        // Generar y almacenar el código 2FA
        $twoFactorCode = $this->verifyCodeController->generateCode(); // Genera el código
        $this->twoFactorController->updateCode($user, $twoFactorCode); // Almacena el código en el usuario
        $this->twoFactorController->sendCode($user->email, $twoFactorCode); // Envía el código por correo

        // Guardar el email del usuario en sesión para validación de 2FA
        session([$this->twoFactorController->sessionTwoFactorEmail => $user->email]);

        // Redirigir al usuario para ingresar el código 2FA
        return redirect()->route('code.two-factor')->with('success', 'Se envió un código de verificación a su correo.');
    }

    /*
    |--------------------------------------------------------------------------
    | Registro de Usuario
    |--------------------------------------------------------------------------
    |
    | Este método maneja el registro de nuevos usuarios. Valida los datos recibidos,
    | genera un código de verificación, lo envía por correo y crea el nuevo usuario.
    |
    | @param \Illuminate\Http\Request $request Datos de la solicitud de registro
    | @return \Illuminate\Http\RedirectResponse Redirección a la página de verificación
    |
    */
    public function register(Request $request)
    {
        // Validación de datos de registro
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Verificar si la validación falla
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Generar y enviar el código de verificación al correo
        $verificationCode = $this->verifyCodeController->generateCode();
        $this->verifyCodeController->sendCode($request->email, $verificationCode);

        // Crear el nuevo usuario con el código de verificación
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'verification_code' => Hash::make($verificationCode),
            'verification_code_expires_at' => now()->addMinutes($this->verifyCodeController->timeExpirationCode),
            'password' => Hash::make($request->password),
        ]);

        // Guardar el correo del usuario en sesión para la verificación
        session([$this->verifyCodeController->userEmailSessionName => $request->email]);

        // Redirigir a la página de verificación de código
        return redirect()->route('code.verify')->with('success', 'Se envió un código de verificación al correo proporcionado');
    }

    /*
    |--------------------------------------------------------------------------
    | Validación de Código
    |--------------------------------------------------------------------------
    |
    | Este método valida el código de verificación proporcionado por el usuario
    | durante el proceso de registro. Si es correcto, se verifica la cuenta.
    |
    | @param \Illuminate\Http\Request $request Solicitud con el código de verificación
    | @return \Illuminate\Http\RedirectResponse Redirección según el estado de la validación
    |
    */
    public function validateCode(Request $request)
    {
        // Validación del código
        $codeValidator = Validator::make($request->all(), [
            'code' => 'required|string',
        ]);

        if ($codeValidator->fails()) {
            return redirect()->back()->withErrors($codeValidator)->withInput();
        }

        $email = session($this->verifyCodeController->userEmailSessionName);

        if (!$email) {
            return redirect()->route('register.view')->with('error', 'Correo no encontrado');
        }

        // Buscar al usuario y verificar el código
        $user = User::where('email', $email)->first();

        if (!$user || !$this->verifyCodeController->verifyCode($request->code, $user)) {
            return redirect()->back()->with('error', 'El código no es válido');
        }

        // Verificar al usuario y limpiar la sesión
        $this->verifyCodeController->verifyUser($user);
        session()->forget($this->verifyCodeController->userEmailSessionName);

        return redirect()->route('login.view')->with('success', 'Cuenta verificada correctamente. Ya puedes iniciar sesión.');
    }

    /*
    |--------------------------------------------------------------------------
    | Reenvío de Código de Verificación
    |--------------------------------------------------------------------------
    |
    | Este método permite al usuario reenviar el código de verificación por correo
    | si no ha recibido el código original o si ha expirado.
    |
    | @return \Illuminate\Http\RedirectResponse Redirección con mensaje de éxito
    |
    */
    public function resendCode()
    {
        $email = session($this->verifyCodeController->userEmailSessionName);

        if (!$email) {
            return redirect()->back()->with('error', 'No se obtuvo el correo');
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'No se encontró un usuario');
        }

        // Generar y enviar un nuevo código de verificación
        $verificationCode = $this->verifyCodeController->generateCode();
        $this->verifyCodeController->sendCode($user->email, $verificationCode);
        $this->verifyCodeController->updateCode($user, $verificationCode);

        return redirect()->route('code.verify')->with('success', 'Se envió un código de verificación al correo proporcionado');
    }

    public function resendTwoFaCode(){
        $email = session($this->twoFactorController->sessionTwoFactorEmail);

        if(!$email){
            return redirect()->back()->with('error', 'sesion caducada inicie sesion de nuevo');
        }

        $user = User::where('email', $email)->first();

        if(!$user){
            return redirect()->back()->with('error', 'usuario no encontrado inicie sesion de nuevo');
        }

        $twoFactorCode = $this->verifyCodeController->generateCode();
        $this->twoFactorController->sendCode($user->email, $twoFactorCode);
        $this->twoFactorController->updateCode($user, $twoFactorCode);

        return redirect()->route('code.two-factor')->with('success', 'se envio un codigo nuevo');
    }

    /*
    |--------------------------------------------------------------------------
    | Validación del Código 2FA
    |--------------------------------------------------------------------------
    |
    | Este método valida el código de autenticación 2FA ingresado por el usuario
    | durante el proceso de inicio de sesión.
    |
    | @param \Illuminate\Http\Request $request Solicitud con el código 2FA
    | @return \Illuminate\Http\RedirectResponse Redirección al home si la verificación es exitosa
    |
    */
    public function validateTwoFaCode(Request $request)
    {
        // Validar el código ingresado
        $request->validate(['code' => 'required|string']);

        // Obtener el usuario asociado al 2FA desde la sesión
        $userEmail = session($this->twoFactorController->sessionTwoFactorEmail);

        if (!$userEmail) {
            return redirect()->route('login.view')->with('error', 'Sesión de autenticación expirada. Inicie sesión nuevamente.');
        }

        $user = User::where('email', $userEmail)->first();

        if (!$user || !$this->twoFactorController->verifyCode($request->code, $user)) {
            return redirect()->back()->with('error', 'El código proporcionado no es válido.');
        }

        // Completar la verificación 2FA y autenticar al usuario
        $this->twoFactorController->completeTwoFactorVerification($user);
        Auth::login($user);

        // Limpiar la sesión temporal y redirigir al dashboard
        session()->forget($this->twoFactorController->sessionTwoFactorEmail);
        return redirect()->route('home')->with('success', 'Inicio de sesión exitoso.');
    }
}
