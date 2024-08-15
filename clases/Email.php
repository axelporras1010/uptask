<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email
{
    protected $email;
    protected $nombre;
    protected $token;

    public function __construct($email, $nombre, $token)
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    public function enviarConfirmacion()
    {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'f4c5f08153b512';
        $mail->Password = '711db2b187d0b4';

        $mail->setFrom('cuentas@uptask.com');
        $mail->addAddress('cuentas@uptask.com', 'uptask.com');
        $mail->Subject = 'confirma tu cuenta';

        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = '<html>';
        $contenido .= "<p><string> Hola "  . $this->nombre .  "</strong> Has creado tu cuenta en UpTask, solo debes confirmarla en el siguiente enlace</p>";
        $contenido .= "<p>Presiona aqui: <a href ='http://localhost:3000/confirmar?token=" . $this->token . "'>Confirmar Cuenta</a></p>";
        $contenido .= '</html>';

        $mail->Body = $contenido;

        $mail->send();
    }

    public function enviarRecuperacion()
    {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'f4c5f08153b512';
        $mail->Password = '711db2b187d0b4';

        $mail->setFrom('cuentas@uptask.com');
        $mail->addAddress('cuentas@uptask.com', 'uptask.com');
        $mail->Subject = 'Reestablece tu password';

        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = '<html>';
        $contenido .= "<p><string> Hola "  . $this->nombre .  "</strong> Para recuperar tu password has click en el siguiente enlace, de lo contrario ignora este correo</p>";
        $contenido .= "<p>Presiona aqui: <a href ='http://localhost:3000/reestablecer?token=" . $this->token . "'>Reestablecer password</a></p>";
        $contenido .= '</html>';

        $mail->Body = $contenido;

        $mail->send();
    }
}