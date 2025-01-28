<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TwoFactorCode extends Mailable
{
    use Queueable, SerializesModels;

    public $code; // Variable para almacenar el código de verificación 2FA

    /**
     * Crear una nueva instancia del mensaje.
     *
     * Este constructor asigna el código de verificación 2FA
     * que se enviará al usuario a través del correo electrónico.
     *
     * @param string $code El código 2FA generado.
     */
    public function __construct($code)
    {
        $this->code = $code; // Asignar el código de verificación 2FA
    }

    /**
     * Obtener el sobre (Envelope) del mensaje.
     *
     * Define el asunto del correo electrónico, el cual es "Confirmación de inicio de sesión".
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Confirmación de inicio de sesión', // Asunto del correo
        );
    }

    /**
     * Construir el mensaje.
     *
     * Define la vista que se utilizará para el contenido del correo.
     * La vista es 'email.two_factor_code_email' y se establece el asunto del correo.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.two_factor_code_email') // Vista que contiene el cuerpo del correo
                    ->subject('Confirmación de inicio de sesión'); // Asunto del correo
    }
}
