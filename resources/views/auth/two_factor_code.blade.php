    <!--
    Vista: two_factor_verify.blade.php

    Propósito:
    Esta vista permite que el usuario ingrese un código de verificación de dos factores (2FA) que ha recibido por correo electrónico para completar el proceso de autenticación.

    Variables esperadas:
    - `code`: El código de verificación que el usuario recibe por correo electrónico. Este campo se debe completar para confirmar la identidad del usuario.

    Errores:
    - La vista maneja mensajes de error tanto generales como específicos para el formulario de verificación.
    - Si la validación del formulario falla, se mostrarán los errores correspondientes debajo de los campos.

    Mensajes personalizados:
    - La vista muestra mensajes personalizados de éxito o error utilizando la sesión para informar al usuario si el proceso de verificación fue exitoso o si hubo algún problema.
    - Los mensajes de éxito y error se muestran usando las alertas de Bootstrap.

    Estilo:
    - La vista usa **Bootstrap** 4 para los estilos de la interfaz de usuario.
    - El formulario tiene un diseño simple y centrado, adecuado para la verificación rápida de 2FA.

    Lógica:
    - **csrf**: Protección contra ataques CSRF (Cross-Site Request Forgery).
    - **error**: La directiva `error` se utiliza para mostrar los mensajes de error en caso de que el código proporcionado no sea válido.
    - **if**: La directiva `if` se utiliza para mostrar diferentes alertas según el estado de la sesión (éxito o error).

-->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Código 2FA</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="verification-container">
            <h2 class="text-center mb-4">Verificación de Código</h2>

            <!-- Mostrar mensajes de error personalizados -->
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Mostrar errores de validación -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Mostrar mensajes de éxito -->
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Formulario de verificación de 2FA -->
            <form id="verifyForm" action="{{ route('two-factor.verify') }}" method="POST" class="mt-4">
                @csrf
                <div class="form-group">
                    <label for="code">Ingrese el código que recibió por correo electrónico</label>
                    <input type="text" id="code" name="code" class="form-control" placeholder="Código de verificación" required>
                </div>

                <!-- Campo oculto para reCAPTCHA -->
                <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response-verify">

                <button disabled id="verifyButton" type="submit" class="btn btn-primary btn-block">
                    <span id="verifySpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    <span id="verifyButtonText">Verificar Código</span>
                </button>
            </form>

            <!-- Botón para reenviar código -->
            <form id="resendForm" action="{{ route('two-factor.resend') }}" method="POST" class="text-center mt-3">
                @csrf
                <!-- Campo oculto para reCAPTCHA -->
                <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response-resend">

                <button disabled id="resendButton" type="submit" class="btn btn-secondary">
                    <span id="resendSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    <span id="resendButtonText">Reenviar código</span>
                </button>
            </form>

            <!-- reCAPTCHA -->
            <div class="g-recaptcha mt-3"
                 data-sitekey="{{ env('NOCAPTCHA_SITEKEY') }}"
                 data-callback="onCaptchaSuccess"
                 data-expired-callback="onCaptchaExpired"></div>

            <!-- Mensaje de error para el captcha -->
            <div id="recaptcha-error" class="alert alert-danger d-none mt-2">
                Por favor, completa el captcha para continuar.
            </div>
        </div>
    </div>

    <!-- Script de reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        let isCaptchaValid = false;

        function onCaptchaSuccess(token) {
            isCaptchaValid = true;

            // Guardar el token en los formularios
            document.getElementById('g-recaptcha-response-verify').value = token;
            document.getElementById('g-recaptcha-response-resend').value = token;

            // Habilitar botones
            document.getElementById('verifyButton').disabled = false;
            document.getElementById('resendButton').disabled = false;

            // Ocultar mensaje de error
            document.getElementById('recaptcha-error').classList.add('d-none');
        }

        function onCaptchaExpired() {
            isCaptchaValid = false;

            // Borrar el token
            document.getElementById('g-recaptcha-response-verify').value = "";
            document.getElementById('g-recaptcha-response-resend').value = "";

            // Deshabilitar botones
            document.getElementById('verifyButton').disabled = true;
            document.getElementById('resendButton').disabled = true;

            // Mostrar mensaje de error
            document.getElementById('recaptcha-error').classList.remove('d-none');
        }

        // Manejo del formulario de verificación
        const verifyForm = document.getElementById('verifyForm');
        const verifyButton = document.getElementById('verifyButton');
        const verifySpinner = document.getElementById('verifySpinner');
        const verifyButtonText = document.getElementById('verifyButtonText');

        verifyForm.addEventListener('submit', function (event) {
            if (!isCaptchaValid) {
                event.preventDefault();
                document.getElementById('recaptcha-error').classList.remove('d-none');
                return;
            }
            verifyButton.disabled = true;
            verifySpinner.classList.remove('d-none');
            verifyButtonText.textContent = "Cargando...";
        });

        // Manejo del formulario para reenviar código
        const resendForm = document.getElementById('resendForm');
        const resendButton = document.getElementById('resendButton');
        const resendSpinner = document.getElementById('resendSpinner');
        const resendButtonText = document.getElementById('resendButtonText');

        resendForm.addEventListener('submit', function (event) {
            if (!isCaptchaValid) {
                event.preventDefault();
                document.getElementById('recaptcha-error').classList.remove('d-none');
                return;
            }
            resendButton.disabled = true;
            resendSpinner.classList.remove('d-none');
            resendButtonText.textContent = "Cargando...";
        });
    </script>
</body>
</html>
