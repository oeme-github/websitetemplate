<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('integration')]
class SepaFormTest extends TestCase
{
    private const BASE_URL    = 'http://websitetemplate.local';
    private const MAILPIT_API = 'http://localhost:8025/api/v1';
    private const COOKIE_FILE = '/tmp/phpunit_sepa_session.txt';

    protected function setUp(): void
    {
        if (!$this->isReachable(self::BASE_URL)) {
            $this->markTestSkipped('websitetemplate.local nicht erreichbar — Apache läuft?');
        }

        if (!$this->isReachable(self::MAILPIT_API . '/messages')) {
            $this->markTestSkipped('Mailpit nicht erreichbar — mailpit gestartet?');
        }

        $this->mailpitDeleteAll();
    }

    public function testSepaFormularSendetMailMitPdfAnhang(): void
    {
        [$csrf] = $this->fetchCsrf();
        $this->assertNotEmpty($csrf, 'Kein CSRF-Token auf der Seite gefunden.');

        $data = $this->postForm($csrf, $this->validFields());

        $this->assertTrue(
            $data['ok'] ?? false,
            'Endpoint lieferte kein ok:true. Debug: ' . ($data['debug'] ?? $data['message'] ?? '')
        );
        $this->assertSame('SENT', $data['code'] ?? '');

        $messages = $this->mailpitMessages();
        $this->assertCount(1, $messages, 'Erwartet genau 1 Mail in Mailpit.');

        $msg = $messages[0];
        // mitgliedschaft=Nein → Spende
        $this->assertSame('Neue Spende (SEPA)', $msg['Subject']);
        $this->assertSame('dev@websitetemplate.local', $msg['From']['Address']);
        $this->assertSame('inbox@websitetemplate.local', $msg['To'][0]['Address']);
        $this->assertSame(1, $msg['Attachments'], 'Erwartet genau 1 Anhang in der SEPA-Mail.');

        $detail = $this->mailpitMessage($msg['ID']);
        $attachment = $detail['Attachments'][0] ?? null;
        $this->assertNotNull($attachment, 'Kein Anhang im Nachrichtendetail.');
        $this->assertMatchesRegularExpression('/^SEPA-\d{8}-[0-9a-f]{8}\.pdf$/', $attachment['FileName']);
        $this->assertSame('application/pdf', $attachment['ContentType']);
    }

    public function testValidierungsfehlerSendetKeineMail(): void
    {
        [$csrf] = $this->fetchCsrf();

        $data = $this->postForm($csrf, [
            'vorname'  => '',
            'nachname' => '',
            'email'    => 'kein-email',
            'consent'  => '',
        ]);

        $this->assertFalse($data['ok'] ?? true);
        $this->assertSame('VALIDATION', $data['code'] ?? '');
        $this->assertNotEmpty($data['errors'] ?? []);
        $this->assertCount(0, $this->mailpitMessages(), 'Bei Validierungsfehler darf keine Mail gesendet werden.');
    }

    public function testUngueltigeIbanLiefertFeldFehler(): void
    {
        [$csrf] = $this->fetchCsrf();

        $fields = $this->validFields();
        $fields['iban'] = 'DE00000000000000000000';

        $data = $this->postForm($csrf, $fields);

        $this->assertFalse($data['ok'] ?? true);
        $this->assertSame('VALIDATION', $data['code'] ?? '');
        $this->assertArrayHasKey('iban', $data['errors'] ?? [], 'Feldfehler für "iban" erwartet.');
        $this->assertCount(0, $this->mailpitMessages());
    }

    public function testCsrfTokenWirdNachErfolgRotiert(): void
    {
        [$csrf] = $this->fetchCsrf();

        $data = $this->postForm($csrf, $this->validFields());

        $this->assertTrue($data['ok'] ?? false);
        $this->assertArrayHasKey('csrf', $data, 'Kein neuer CSRF-Token im Erfolgs-Response.');
        $this->assertNotSame($csrf, $data['csrf'], 'CSRF-Token wurde nicht rotiert.');
    }

    // -- Helpers --

    private function validFields(): array
    {
        return [
            'vorname'              => 'Max',
            'nachname'             => 'Mustermann',
            'email'                => 'max@example.com',
            'geburtsdatum'         => '1990-01-15',
            'strasse'              => 'Musterstraße 1',
            'plz'                  => '12345',
            'ort'                  => 'Musterstadt',
            'iban'                 => 'DE89370400440532013000',
            'bank'                 => 'Commerzbank',
            'betrag'               => '25 €',
            'zahlungsrhythmus'     => 'Jahr',
            'mitgliedschaft'       => 'Nein',
            'nachricht'            => 'Integration-Test',
            'consent'              => '1',
            'consent-datenschutz'  => '1',
            'consent-sepa'         => '1',
        ];
    }

    private function fetchCsrf(): array
    {
        @unlink(self::COOKIE_FILE);

        $ch = curl_init(self::BASE_URL . '/');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_COOKIEJAR      => self::COOKIE_FILE,
            CURLOPT_COOKIEFILE     => self::COOKIE_FILE,
        ]);
        $html = curl_exec($ch);
        curl_close($ch);

        preg_match('/name="_csrf"\s+value="([^"]+)"/', (string) $html, $m);

        return [$m[1] ?? ''];
    }

    private function postForm(string $csrf, array $fields): array
    {
        $ch = curl_init(self::BASE_URL . '/send_sepa.php');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_COOKIEJAR      => self::COOKIE_FILE,
            CURLOPT_COOKIEFILE     => self::COOKIE_FILE,
            CURLOPT_POSTFIELDS     => array_merge(['_csrf' => $csrf], $fields),
            CURLOPT_HTTPHEADER     => ['Accept: application/json'],
        ]);
        $body = curl_exec($ch);
        curl_close($ch);

        return json_decode((string) $body, true) ?? [];
    }

    private function mailpitMessages(): array
    {
        $body = file_get_contents(self::MAILPIT_API . '/messages');
        $data = json_decode((string) $body, true);
        return $data['messages'] ?? [];
    }

    private function mailpitMessage(string $id): array
    {
        $body = file_get_contents(self::MAILPIT_API . '/message/' . $id);
        return json_decode((string) $body, true) ?? [];
    }

    private function mailpitDeleteAll(): void
    {
        $ch = curl_init(self::MAILPIT_API . '/messages');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => 'DELETE',
        ]);
        curl_exec($ch);
        curl_close($ch);
    }

    private function isReachable(string $url): bool
    {
        $ctx = stream_context_create(['http' => ['timeout' => 2, 'ignore_errors' => true]]);
        return @file_get_contents($url, false, $ctx) !== false;
    }
}
