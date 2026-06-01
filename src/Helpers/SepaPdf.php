<?php
declare(strict_types=1);

namespace App\Helpers;

use TCPDF;

class SepaPdf
{
    private static function esc(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function create(array $data): string
    {
        $place        = self::esc($data['place']          ?? '');
        $date         = self::esc($data['date']           ?? '');
        $creditorName = self::esc($data['creditor_name']  ?? '');
        $creditorAddr = self::esc($data['creditor_adress'] ?? '');
        $creditorId   = self::esc($data['creditor_id']    ?? '');
        $mandateId    = self::esc($data['mandate_id']     ?? '');
        $vorname      = self::esc($data['vorname']        ?? '');
        $nachname     = self::esc($data['nachname']       ?? '');
        $geburtsdatum = self::esc($data['geburtsdatum']   ?? '');
        $email        = self::esc($data['email']          ?? '');
        $telefon      = self::esc($data['telefon']        ?? '');
        $strasse      = self::esc($data['strasse']        ?? '');
        $plz          = self::esc($data['plz']            ?? '');
        $ort          = self::esc($data['ort']            ?? '');
        $iban         = self::esc($data['iban']           ?? '');
        $bank         = self::esc($data['bank']           ?? '');
        $fee          = self::esc($data['fee']            ?? '');
        $frequ        = self::esc($data['frequ']          ?? '');
        $memship      = self::esc($data['memship']        ?? '');
        $herkunft     = self::esc($data['herkunft']       ?? '');
        $mes          = self::esc($data['mes']            ?? '');

        $isSpende = ($data['frequ']   ?? '') === 'Spende einmalig'
                 || ($data['memship'] ?? '') === 'Nein';
        $docTitle = $isSpende
            ? 'Spende mit SEPA-Lastschriftmandat'
            : 'Mitgliedsantrag mit SEPA-Lastschriftmandat';

        $pdf = new TCPDF();
        $pdf->SetCreator($data['creditor_name'] ?? '');
        $pdf->SetAuthor($data['creditor_name']  ?? '');
        $pdf->SetTitle($docTitle);
        $pdf->AddPage();
        $pdf->SetFont('dejavusans', '', 10);

        $check   = '☑';
        $uncheck = '☐';

        $consentSepa = !empty($data['consentsepa']) ? $check : $uncheck;
        $consentDs   = !empty($data['consentds'])   ? $check : $uncheck;

        $telefonLine  = $telefon  !== '' ? "Telefon: {$telefon}<br>"   : '';
        $herkunftLine = $herkunft !== '' ? "Herkunft: {$herkunft}<br>" : '';
        $bankLine     = $bank     !== '' ? "Bank: {$bank}<br>"         : '';
        $mesLine      = $mes      !== '' ? "Nachricht: {$mes}<br>"     : '';

        $html = "
        <h1>{$docTitle}</h1>
        <p>Ort: {$place} &nbsp;&nbsp; Datum: {$date}</p>

        <h2>Antragsteller</h2>
        <p>
            Vorname: {$vorname}<br>
            Nachname: {$nachname}<br>
            Geburtsdatum: {$geburtsdatum}<br>
            E-Mail: {$email}<br>
            {$telefonLine}
            Strasse: {$strasse}<br>
            PLZ: {$plz}<br>
            Ort: {$ort}
        </p>

        <h2>Mitgliedschaft</h2>
        <p>
            Betrag: {$fee}<br>
            Zahlungsrhythmus: {$frequ}<br>
            Mitgliedschaft: {$memship}<br>
            {$herkunftLine}
            {$mesLine}
        </p>

        <h2>Bankverbindung</h2>
        <p>
            IBAN: {$iban}<br>
            {$bankLine}
        </p>

        <h2>SEPA-Lastschriftmandat</h2>
        <p>
            Gläubiger: {$creditorName}<br>
            Anschrift: {$creditorAddr}<br>
            Gläubiger-ID: {$creditorId}<br>
            Mandatdatum: {$date}<br>
            Mandats-ID: {$mandateId}
        </p>
        <p>
            Ich ermächtige den <strong>{$creditorName}</strong>,<br>
            Zahlungen von meinem Konto mittels Lastschrift einzuziehen.<br>
            Zugleich weise ich mein Kreditinstitut an,<br>
            die vom {$creditorName}<br>
            auf mein Konto gezogenen Lastschriften einzulösen.
        </p>
        <p>
            <strong>Hinweis:</strong> Ich kann innerhalb von acht Wochen,<br>
            beginnend mit dem Belastungsdatum,<br>
            die Erstattung des belasteten Betrages verlangen.
        </p>
        <p>
            {$consentSepa} Ich erteile ein SEPA-Lastschriftmandat.
        </p>
        <p>
            {$consentDs} Ich habe die Datenschutzerklärung gelesen und stimme zu.
        </p>
        <p>
            Dieses SEPA-Mandat wurde elektronisch erteilt und ist ohne Unterschrift gültig.
        </p>
        ";

        $pdf->writeHTML($html);

        $path = sys_get_temp_dir() . '/sepa_' . uniqid('', true) . '.pdf';
        $pdf->Output($path, 'F');

        return $path;
    }
}
