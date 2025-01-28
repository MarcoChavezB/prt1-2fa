<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendVerificationCode extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $code; // Variable para almacenar el código de verificación

    /**
     * Crear una nueva instancia del mensaje.
     *
     * Este constructor asigna el código de verificación que se enviará
     * al usuario a través del correo electrónico.
     *
     * @param string $code El código de verificación generado.
     */
    public function __construct($code)
    {
        $this->code = $code; // Asignar el código de verificación
    }

    /**
     * Obtener el sobre (Envelope) del mensaje.
     *
     * Define el asunto del correo electrónico, el cual es "Confirmación de Activación de Cuenta".
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(){
        return new Envelope(
            subject: 'Confirmación de Activación de Cuenta', // Asunto del correo
        );
    }

    /**
     * Construir el mensaje.
     *
     * Define la vista que se utilizará para el contenido del correo.
     * La vista es 'email.code_email' y se establece el asunto del correo.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.code_email') // Vista que contiene el cuerpo del correo
                    ->subject('Confirmación de Activación de Cuenta'); // Asunto del correo
    }
}
