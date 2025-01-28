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
    <!-- Enlace a la hoja de estilo de Bootstrap 4 -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
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
        <form action="{{ route('two-factor.verify') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="code">Ingrese el código que recibió por correo electrónico</label>
                <input type="text" id="code" name="code" class="form-control" placeholder="Código de verificación" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Verificar Código</button>
        </form>


        <form action="{{ route('two-factor.resend') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-primary">Reenviar código</button>
        </form>
    </div>
</body>
</html>
