<!--
    Vista: verify-email.blade.php

    Propósito:
    Esta vista se utiliza para mostrar un mensaje al usuario indicando que su cuenta no está verificada y solicitando que verifique su correo electrónico. Además, incluye un botón para re-enviar el correo de verificación en caso de que el usuario no lo haya recibido o lo haya perdido.

    Variables esperadas:
    - `session('error')`: Si hay un error en el proceso (por ejemplo, si el intento de reenvío falla), se muestra un mensaje de error.
    - `session('success')`: Si el correo electrónico de verificación se ha enviado correctamente, se puede mostrar un mensaje de éxito (no se incluye explícitamente en la vista, pero podría implementarse).

    Lógica:
    - El formulario se encarga de reenviar el correo de verificación al usuario. Al hacer clic en el botón, se ejecutará la acción que corresponde a `resend.verification`, encargada de enviar el correo de verificación.
    - Se protege el formulario contra ataques CSRF con la directiva `@csrf`.

    Estilo:
    - Se utiliza **Bootstrap 4** para una interfaz adaptativa y sencilla.
    - La vista está estructurada con una **card** que contiene el mensaje de verificación y el formulario para reenviar el correo.
    - Los mensajes de error, en caso de que se presenten, se muestran dentro de alertas con la clase `alert-danger`.

    Flujo:
    - **Primera vista**: Al acceder a esta página, el usuario verá un mensaje indicando que su cuenta no está verificada.
    - **Formulario**: Si el usuario no ha recibido el correo de verificación o desea reenviarlo, puede hacer clic en el botón para enviar nuevamente el correo.
    - **Mensaje de error**: Si ocurre algún error durante el proceso (por ejemplo, si no se puede reenviar el correo), se muestra un mensaje de error en una alerta.

-->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Correo Electrónico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mt-5">
                    <div class="card-header text-center">
                        <h4>{{ __('Verificación de Correo Electrónico') }}</h4>
                    </div>

                    <div class="card-body">
                        <h5 class="text-center">Tu cuenta aún no está verificada</h5>
                        <p class="text-center">Por favor verifica tu correo electrónico para activar tu cuenta.</p>

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form onsubmit='disableButton()' id="resendForm" method="POST" action="{{ route('resend.verification') }}">
                            @csrf
                            <div class="form-group text-center">
                                <button id="submit_button" type="submit" class="btn btn-primary">
                                    <span id="spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    <span id="buttonText">Enviar nuevamente el correo de verificación</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const submitButton = document.getElementById('submit_button');
        function disableButton(){
            submitButton.disabled = true;
            submitButton.innerText = 'Cargando...';
            submitButton.style.cursor = 'not-allowed';
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
