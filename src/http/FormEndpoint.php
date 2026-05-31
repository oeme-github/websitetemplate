<?php
declare(strict_types=1);

function respond(int $status, array $payload): never
{
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
    }

    http_response_code($status);

    echo json_encode(
        $payload,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
    );
    exit;
}

function formBootstrap(): void
{
    set_error_handler(function (int $severity, string $message, string $file, int $line): never {
        throw new ErrorException($message, 0, $severity, $file, $line);
    });

    set_exception_handler(function (Throwable $e): void {
        $payload = [
            'ok'      => false,
            'code'    => 'SERVER',
            'message' => 'Interner Serverfehler.',
        ];

        if (\App\Helpers\Helpers::is_dev()) {
            $payload['debug'] = $e->getMessage();
        }

        respond(500, $payload);
    });
}

function guardMethod(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        respond(405, [
            'ok'      => false,
            'code'    => 'METHOD',
            'message' => 'Methode nicht erlaubt.',
        ]);
    }
}

function guardCsrf(): void
{
    if (!csrf_verify($_POST['_csrf'] ?? null)) {
        respond(403, [
            'ok'      => false,
            'code'    => 'CSRF',
            'message' => 'Ungültige Anfrage.',
        ]);
    }
}

function guardHoneypot(): void
{
    if (!empty($_POST['website'] ?? '')) {
        respond(200, ['ok' => true, 'message' => 'Danke.']);
    }
}

function requireEnvKeys(array $keys): void
{
    foreach ($keys as $key) {
        if (empty($_ENV[$key] ?? null)) {
            throw new RuntimeException("Missing env variable: $key");
        }
    }
}
