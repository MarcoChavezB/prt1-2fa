<!--
    Vista: register.blade.php

    Propósito:
    Esta vista proporciona un formulario de registro para que los usuarios creen una cuenta en la aplicación.
    Los usuarios deben ingresar su nombre, correo electrónico, contraseña y confirmar la contraseña.

    Variables esperadas:
    - `name`: El nombre del usuario.
    - `email`: El correo electrónico del usuario.
    - `password`: La contraseña del usuario.
    - `password_confirmation`: Confirmación de la contraseña del usuario.

    Errores:
    - La vista también maneja errores de validación para cada campo (nombre, correo electrónico, contraseña).
    - Si un campo tiene un error, el mensaje de error se mostrará debajo del campo correspondiente.

    Estructura:
    - El formulario tiene campos para el nombre, correo electrónico, contraseña y confirmación de contraseña.
    - El formulario envía una solicitud POST a la ruta `register.perform`, donde se procesan los datos del usuario.
    - Un enlace para redirigir a los usuarios a la vista de inicio de sesión si ya tienen una cuenta.

    Estilo:
    - La vista utiliza **TailwindCSS** para los estilos de la interfaz de usuario.
    - El formulario tiene un diseño centrado en la pantalla, con un fondo blanco y sombras.
    - El botón de "Registrar" está estilizado con un color azul y cambia de tono al pasar el ratón sobre él.

    Lógica:
    - **csrf**: Protección contra ataques CSRF (Cross-Site Request Forgery).
    - **error**: La directiva `error` se utiliza para mostrar mensajes de error en caso de que haya validación fallida en alguno de los campos.
-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold text-center mb-6">Registro</h2>

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
                        <li class="text-sm text-red-600">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulario de Registro -->
        <form action="{{ route('register.perform') }}" method="POST" id="registerForm">
            @csrf
            <!-- Nombre -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input
                value="{{old('name')}}"
                type="text" name="name" id="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                @error('name')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <!-- Correo Electrónico -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Correo</label>
                <input
                value="{{old('email')}}"
                type="email" name="email" id="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                @error('email')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <!-- Contraseña -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Contrasena</label>
                <input
                type="password" name="password" id="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                @error('password')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
                <ul class="text-sm mt-2 text-gray-600">
                    <li id="length" class="invalid">🔴 Mínimo 10 caracteres</li>
                    <li id="number" class="invalid">🔴 Al menos un número (0-9)</li>
                    <li id="letter" class="invalid">🔴 Al menos una letra (a-z, A-Z)</li>
                    <li id="symbol" class="invalid">🔴 Al menos un símbolo (!@#$%^&*)</li>
                    <li id="case-diff" class="invalid">🔴 Al menos una mayúscula y una minúscula</li>
                </ul>
            </div>

            <!-- Confirmación de Contraseña -->
            <div class="mb-4">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar contrasena</label>
                <input
                type="password" name="password_confirmation" id="password_confirmation" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
            </div>

            <!-- Botón de Envío -->
            <div class="mb-4">
                <button id="submitButton" type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 flex items-center justify-center">
                    <span class="spinner-border hidden mr-2" id="spinner"></span>
                    <span id="buttonText">Registrar</span>
                </button>

                <div class="g-recaptcha" data-sitekey="{{ env('NOCAPTCHA_SITEKEY') }}"></div>
                <div id="recaptcha-error" class="bg-red-200 text-red-600 text-sm mt-2 hidden">
                    Por favor, completa el captcha para continuar.
                </div>
            </div>

            <!-- Enlace para iniciar sesión si el usuario ya tiene cuenta -->
            <div class="text-center">
                <a href="{{ route('login.view') }}" class="text-blue-500 hover:underline">¿Ya tienes una cuenta? Inicia sesión</a>
            </div>
        </form>
    </div>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        const form = document.getElementById('registerForm');
        const submitButton = document.getElementById('submitButton');
        const spinner = document.getElementById('spinner');
        const buttonText = document.getElementById('buttonText');
        const recaptchaError = document.getElementById('recaptcha-error');
        const passwordInput = document.getElementById("password");

        // Elementos de validación de contraseña
        const lengthCheck = document.getElementById("length");
        const numberCheck = document.getElementById("number");
        const letterCheck = document.getElementById("letter");
        const symbolCheck = document.getElementById("symbol");
        const caseDiffCheck = document.getElementById("case-diff");

        // Evento de validación al enviar el formulario
        form.addEventListener('submit', function (event) {
            const response = grecaptcha.getResponse();
            const passwordValid = isPasswordValid(passwordInput.value);

            let hasErrors = false;

            if (!passwordValid) {
                alert("La contraseña no cumple con los requisitos.");
                hasErrors = true;
            }

            if (response.length === 0) {
                recaptchaError.classList.remove('hidden');
                hasErrors = true;
            } else {
                recaptchaError.classList.add('hidden');
            }

            if (hasErrors) {
                event.preventDefault();
            }
        });

        // Verificar si la contraseña es válida
        function isPasswordValid(password) {
            const hasNumber = /\d/.test(password);
            const hasLetter = /[a-zA-Z]/.test(password);
            const hasSymbol = /[\W_]/.test(password);
            const hasUpper = /[A-Z]/.test(password);
            const hasLower = /[a-z]/.test(password);
            const isLongEnough = password.length >= 10;

            // Actualizar los estilos de validación
            updateValidation(lengthCheck, isLongEnough);
            updateValidation(numberCheck, hasNumber);
            updateValidation(letterCheck, hasLetter);
            updateValidation(symbolCheck, hasSymbol);
            updateValidation(caseDiffCheck, hasUpper && hasLower);

            return isLongEnough && hasNumber && hasLetter && hasSymbol && hasUpper && hasLower;
        }

        // Función para actualizar los estilos de validación
        function updateValidation(element, isValid) {
            if (isValid) {
                element.classList.remove("invalid");
                element.classList.add("valid");
                element.textContent = element.textContent.replace("🔴", "🟢");
            } else {
                element.classList.remove("valid");
                element.classList.add("invalid");
                element.textContent = element.textContent.replace("🟢", "🔴");
            }
        }

        // Evento de validación de la contraseña en tiempo real
        passwordInput.addEventListener("input", function () {
            isPasswordValid(passwordInput.value);
        });

        // Detectar cambios en el captcha
        document.addEventListener("DOMContentLoaded", function () {
            const recaptcha = document.querySelector(".g-recaptcha");
            if (recaptcha) {
                recaptcha.addEventListener("change", function () {
                    recaptchaError.classList.add('hidden');
                });
            }
        });

    </script>
</body>
</html>

<style>
    .valid {
        color: green;
    }

    .invalid {
        color: red;
    }
</style>
