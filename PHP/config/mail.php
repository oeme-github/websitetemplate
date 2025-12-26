<?php
use App\Env;

return [
    'host'      => Env::get('SMTP_HOST'),
    'port'      => Env::get('SMTP_PORT'),
    'secure'    => PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS,
    'user'      => Env::get('SMTP_USER'),
    'pass'      => Env::get('SMTP_PASS'),
    'from'      => Env::get('SMTP_FROM'),
    'from_name' => Env::get('SMTP_FROM_NAME'),
];
