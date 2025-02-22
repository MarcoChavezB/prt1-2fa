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
                <button id="verifyButton" type="submit" class="btn btn-primary btn-block">
                    <span id="verifySpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    <span id="verifyButtonText">Verificar Código</span>
                </button>
            </form>

            <!-- Botón para reenviar código -->
            <form id="resendForm" action="{{ route('two-factor.resend') }}" method="POST" class="text-center mt-3">
                @csrf
                <button id="resendButton" type="submit" class="btn btn-secondary">
                    <span id="resendSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    <span id="resendButtonText">Reenviar código</span>
                </button>
            </form>
        </div>
    </div>

    <script>
        // Manejo del formulario de verificación
        const verifyForm = document.getElementById('verifyForm');
        const verifyButton = document.getElementById('verifyButton');
        const verifySpinner = document.getElementById('verifySpinner');
        const verifyButtonText = document.getElementById('verifyButtonText');

        verifyForm.addEventListener('submit', function () {
            verifyButton.disabled = true; // Deshabilitar el botón
            verifySpinner.classList.remove('d-none'); // Mostrar el spinner
            verifyButtonText.textContent = "Cargando..."; // Cambiar el texto
        });

        // Manejo del formulario para reenviar código
        const resendForm = document.getElementById('resendForm');
        const resendButton = document.getElementById('resendButton');
        const resendSpinner = document.getElementById('resendSpinner');
        const resendButtonText = document.getElementById('resendButtonText');

        resendForm.addEventListener('submit', function () {
            resendButton.disabled = true; // Deshabilitar el botón
            resendSpinner.classList.remove('d-none'); // Mostrar el spinner
            resendButtonText.textContent = "Cargando..."; // Cambiar el texto
        });
    </script>
</body>
</html>
