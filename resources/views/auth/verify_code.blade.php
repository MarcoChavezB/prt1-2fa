<!--
    Vista: verify-code.blade.php

    Propósito:
    Esta vista es utilizada para permitir a los usuarios ingresar un código de verificación para completar procesos de verificación, como el inicio de sesión o la confirmación de una acción. Se muestra un formulario donde el usuario debe introducir el código recibido.

    Variables esperadas:
    - `session('error')`: Si hay un error personalizado en el proceso, este mensaje se mostrará en una alerta de tipo "danger".
    - `session('success')`: Si el código fue verificado correctamente, se muestra un mensaje de éxito.
    - `errors`: Se utiliza para mostrar errores de validación si el código ingresado no es válido.

    Errores:
    - Los errores de validación relacionados con el campo de código (por ejemplo, si el código no se ingresa correctamente) se muestran debajo del campo con la clase `is-invalid`.

    Funcionalidad:
    - El formulario solicita al usuario que ingrese un código de verificación. Al enviar el formulario, se ejecutará una acción en el backend para verificar si el código ingresado es válido.

    Estilo:
    - Se utiliza **Bootstrap 4** para crear una interfaz atractiva y responsiva.
    - La vista está estructurada con una **card** (tarjeta) que contiene un formulario y mensajes de error o éxito, dependiendo del estado de la validación.

    Lógica:
    - **csrf**: Protege el formulario contra ataques CSRF.
    - **error('code')**: Se utiliza para mostrar los errores de validación específicos del campo de código. Si hay algún error, se marca el campo con la clase `is-invalid` y se muestra el mensaje de error correspondiente.

-->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Código</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Verificar Código</div>
                    <div class="card-body">
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

                        <!-- Mostrar errores personalizados -->
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <!-- Mostrar mensajes de éxito -->
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

    <!-- Formulario para ingresar el código -->
    <form id="verifyForm" action="{{ route('code.perform') }}" method="POST">
        @csrf

        <!-- Campo para el código de verificación -->
        <div class="form-group">
            <label for="code">Código de verificación</label>
            <input type="text" id="code" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}">
            @error('code')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Campo oculto para el token de reCAPTCHA -->
        <input type="hidden" id="g-recaptcha-response-verify" name="g-recaptcha-response">

        <!-- Botón de envío -->
        <button id="verifyButton" type="submit" class="btn btn-primary" disabled>
            <span id="verifySpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            <span id="verifyButtonText">Verificar</span>
        </button>
    </form>

    <!-- Formulario para reenviar código -->
    <form id="resendForm" action="{{ route('resend.verification') }}" method="POST">
        @csrf

        <!-- Campo oculto para el token de reCAPTCHA -->
        <input type="hidden" id="g-recaptcha-response-resend" name="g-recaptcha-response">

        <!-- Botón para reenviar código -->
        <button id="resendButton" type="submit" class="btn btn-secondary" disabled>
            <span id="resendSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            <span id="resendButtonText">Reenviar código</span>
        </button>
    </form>

    <!-- Captcha único compartido -->
    <div class="g-recaptcha mt-3"
         data-sitekey="{{ env('NOCAPTCHA_SITEKEY') }}"
         data-callback="onCaptchaSuccess"
         data-expired-callback="onCaptchaExpired"></div>

    <!-- Mensaje de error para el captcha -->
    <div id="recaptcha-error" class="alert alert-danger d-none mt-2">
        Por favor, completa el captcha para continuar.
    </div>

    <!-- Script para manejar el reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        let isCaptchaValid = false;

        /**
         * Función que se ejecuta cuando el usuario completa el reCAPTCHA.
         * Guarda el token en los formularios y habilita los botones.
         */
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

        /**
         * Función que se ejecuta cuando el reCAPTCHA expira.
         * Deshabilita los botones y borra el token.
         */
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
    </script>
</body>
</html>
