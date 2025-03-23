
<!--
    Vista: login.blade.php

    Propósito:
    Esta vista proporciona un formulario de inicio de sesión para que los usuarios ingresen sus credenciales
    (correo electrónico y contraseña) para acceder a su cuenta en la aplicación.

    Variables esperadas:
    - No se reciben variables explícitas en esta vista, pero se pueden mostrar mensajes de sesión.

    Estructura:
    La vista contiene un formulario donde los usuarios pueden ingresar su correo electrónico y contraseña.
    También incluye un enlace hacia la vista de registro para aquellos que no tienen una cuenta.

    Lógica:
    - Si hay mensajes de éxito o error almacenados en la sesión (como el resultado de intentos de inicio de sesión fallidos o exitosos),
      se muestran en la parte superior del formulario en forma de alertas.
    - El formulario envía una solicitud POST a la ruta 'login.perform', donde se procesan las credenciales del usuario.
    - También se incluye un enlace para redirigir a los usuarios a la vista de registro si aún no tienen cuenta.

    Estilo:
    - La vista utiliza Bootstrap para los estilos básicos y un estilo personalizado para el diseño del formulario.
    - El formulario y los botones tienen un diseño centrado en la página, con un fondo claro y bordes redondeados.

    Directivas utilizadas:
    - **csrf**: Protección contra ataques CSRF (Cross-Site Request Forgery).
    - **if**: Para mostrar los mensajes de éxito o error si están presentes en la sesión.
-->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Laravel</title>
    <link rel="stylesheet" href="../../css/auth/login.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="login-container">

            <!-- Mostrar mensajes de éxito -->
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Mostrar mensajes de error -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Mostrar mensajes de error -->
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            <!-- Título de la página -->
            <h2 class="text-center">Iniciar Sesión debug</h2>

            <!-- Formulario de inicio de sesión -->

            <form action="{{ route('login.perform') }}" method="POST" class="mt-4" id="loginForm">
                @csrf
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input
                           value="{{old('email')}}"
                           type="email" id="email" name="email" class="form-control" placeholder="Ingrese su correo" required
                           oninvalid="this.setCustomValidity('Por favor ingrese un correo electrónico válido')"
                           oninput="this.setCustomValidity('')">
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Ingrese su contraseña" required
                           oninvalid="this.setCustomValidity('Este campo es obligatorio')"
                           oninput="this.setCustomValidity('')">
                </div>
                <div class="form-group text-center">
                    <button type="submit" id="submitButton" class="btn btn-primary btn-block">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <span class="button-text">Iniciar sesión</span>
                    </button>
                </div>
                <div class="g-recaptcha" data-sitekey="{{ env('NOCAPTCHA_SITEKEY') }}"></div>
                <div id="recaptcha-error" class="alert alert-danger d-none">
                    Por favor, completa el captcha para continuar.
                </div>
            </form>


            <!-- Enlace a la vista de registro -->
            <div class="text-center mt-3">
                <p>¿No tienes cuenta? <a href="{{ route('register.view') }}" class="text-primary">Regístrate aquí</a></p>
            </div>
        </div>
    </div>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <script>
       const form = document.getElementById('loginForm');
        const submitButton = document.getElementById('submitButton');
        const spinner = submitButton.querySelector('.spinner-border');
        const buttonText = submitButton.querySelector('.button-text');

        form.addEventListener('submit', function (event) {
            const recaptchaResponse = grecaptcha.getResponse();

            if (recaptchaResponse.length === 0) {
                // Si el captcha no es válido, mostramos el error y restauramos el botón
                document.getElementById('recaptcha-error').classList.remove('d-none');
                event.preventDefault();
                resetButtonState();
            } else {
                document.getElementById('recaptcha-error').classList.add('d-none');

                // Deshabilita el botón y muestra el spinner
                submitButton.disabled = true;
                spinner.classList.remove('d-none');
                buttonText.textContent = "Cargando...";
            }
        });

        function resetButtonState() {
            // Restaura el estado del botón si ocurre un error
            submitButton.disabled = false;
            spinner.classList.add('d-none');
            buttonText.textContent = "Iniciar sesión";
        }

        // Si hay mensajes de error de Laravel después de que la página se recargue,
        // restauramos el estado del botón en caso de que algo fallara.
        document.addEventListener('DOMContentLoaded', function () {
            if (document.querySelector('.alert-danger')) {
                resetButtonState();
            }
        });
    </script>

</body>
</html>

