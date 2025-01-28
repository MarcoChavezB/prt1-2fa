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


        <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    </head>
    <body>
        <div class="login-container">

            <!-- Mostrar mensajes de éxito -->
            @if (session('success'))
                <!-- Muestra un mensaje de éxito si existe una sesión con la clave 'success' -->
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Mostrar mensajes de error -->
            @if (session('error'))
                <!-- Muestra un mensaje de error si existe una sesión con la clave 'error' -->
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Título de la página -->
            <h2>Iniciar Sesión</h2>

            <!-- Formulario de inicio de sesión -->
            <form action="{{ route('login.perform') }}" method="POST">
                @csrf
                <div class="form-group">
                    <!-- Campo para ingresar el correo electrónico -->
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" placeholder="Ingrese su correo" required>
                </div>
                <div class="form-group">
                    <!-- Campo para ingresar la contraseña -->
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" placeholder="Ingrese su contraseña" required>
                </div>
                <div class="buttons">
                    <!-- Botón para enviar el formulario -->
                    <button type="submit" class="btn-submit">Iniciar sesión</button>
                    <div class="g-recaptcha" data-sitekey="6LfZccUqAAAAAKTRQZprhs0YzPKD1739c-7d6Gzs"></div>
                    <div id="recaptcha-error" class="alert alert-danger" style="display: none;">
                       Por favor, completa el captcha para continuar.
                    </div>
                    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                </div>

            </form>

            <!-- Enlace a la vista de registro -->
            <a href="{{ route('register.view') }}" class="btn-submit">Registrarse</a>
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

    <style>
        /* Estilos personalizados para la vista de inicio de sesión */
        body {
            font-family: 'Nunito', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f7fafc;
        }
        .login-container {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        label {
            display: block;
            font-weight: 600;
        }
        .bluttons{
            display: flex;
            gap: 10px;
            flex-direction: column;
            align-items: center;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        .btn-submit {
            width: 100%;
            padding: 1rem;
            background-color: black;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
        }
        .btn-submit:hover {
            background-color: #45a049;
        }
    </style>
</html>
