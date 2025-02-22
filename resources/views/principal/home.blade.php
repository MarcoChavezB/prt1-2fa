<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Código 2FA</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Inicio de sesión correcto</h2>
        <!-- Formulario para cerrar sesión -->
        <form action="{{ route('user.logout') }}" method="POST">
            @csrf <!-- Token CSRF necesario para solicitudes POST -->
            <button type="submit" class="btn btn-danger">Cerrar sesión</button>
        </form>
    </div>
</body>
</html>
