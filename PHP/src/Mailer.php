<?php
namespace App;

use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    public static function create(array $config): PHPMailer
    {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = $config['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['user'];
        $mail->Password   = $config['pass'];
        $mail->SMTPSecure = $config['secure'];
        $mail->Port       = $config['port'];
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom($config['from'], $config['from_name']);
        return $mail;
    }
}
