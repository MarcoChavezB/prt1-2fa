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
        <h2 class="text-2xl font-bold text-center mb-6">Register</h2>

        <!-- Formulario de Registro -->
        <form action="{{ route('register.perform') }}" method="POST">
            @csrf

            <!-- Nombre -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input
                value="marco"
                type="text" name="name" id="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                @error('name')
                    <!-- Mensaje de error para el campo de nombre -->
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <!-- Correo Electrónico -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input
                value="marco1102004@gmail.com"
                type="email" name="email" id="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                @error('email')
                    <!-- Mensaje de error para el campo de correo electrónico -->
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <!-- Contraseña -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input
                value="10 enero"
                type="password" name="password" id="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                @error('password')
                    <!-- Mensaje de error para el campo de contraseña -->
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <!-- Confirmación de Contraseña -->
            <div class="mb-4">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input
                value="10 enero"
                type="password" name="password_confirmation" id="password_confirmation" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
            </div>

            <!-- Botón de Envío -->
            <div class="mb-4">
                <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">Register</button>

                <div class="g-recaptcha" data-sitekey="6LfZccUqAAAAAKTRQZprhs0YzPKD1739c-7d6Gzs"></div>
                <div id="recaptcha-error" class="bg-red-200 text-red" style="display: none;">
                   Por favor, completa el captcha para continuar.
                </div>
                <script src="https://www.google.com/recaptcha/api.js" async defer></script>
            </div>

            <!-- Enlace para iniciar sesión si el usuario ya tiene cuenta -->
            <div class="text-center">
                <a href="{{ route('login.view') }}" class="text-blue-500 hover:underline">¿Ya tienes una cuenta? Inicia sesión</a>
            </div>
        </form>
    </div>
</body>

    <script>
        document.querySelector('form').addEventListener('submit', function (event) {
            // Verifica si el captcha ha sido completado
            const response = grecaptcha.getResponse();
            if (response.length === 0) {
                // Si no se ha completado, muestra el mensaje de error y evita el envío del formulario
                document.getElementById('recaptcha-error').style.display = 'block';
                event.preventDefault(); // Detiene el envío del formulario
            } else {
                // Si está completado, oculta el mensaje de error (por si ya se mostró antes)
                document.getElementById('recaptcha-error').style.display = 'none';
            }
        });
    </script>

</html>
