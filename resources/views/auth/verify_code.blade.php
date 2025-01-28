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
                        <form action="{{ route('code.perform') }}" method="POST">
                            @csrf

                            <!-- Campo para el código -->
                            <div class="form-group">
                                <label for="code">Código de verificación</label>
                                <input type="text" id="code" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}">
                                @error('code')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Botón de envío -->
                            <button type="submit" class="btn btn-primary">Verificar</button>
                        </form>
                        <form action="{{ route('resend.verification') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-primary">Reenviar código</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
