<?php
namespace App\Helpers;

use TCPDF;

class SepaPdf
{
    public static function create(array $data): string
    {
        $pdf = new TCPDF();
        $pdf->SetCreator($data['creditor_name']);
        $pdf->SetAuthor($data['creditor_name']);
        $pdf->SetTitle('SEPA-Lastschriftmandat');
        $pdf->AddPage();

        $pdf->SetFont('dejavusans', '', 10);

        $check = '☑';
        $uncheck = '☐';

        $consentSepa = !empty($data['consentsepa']) ? $check : $uncheck;
        $consentDs   = !empty($data['consentds'])   ? $check : $uncheck;

        $html = "
        <h1>SEPA-Lastschriftmandat</h1>
        <p>
            Ort: {$data['place']} Datum: {$data['date']}<br>
        </p>
        <p>
            Gläubiger: {$data['creditor_name']}<br>
            Anschrift: {$data['creditor_adress']}<br>
            Gläubiger-ID: {$data['creditor_id']}<br>
        </p>
        <p>
            Mandats-ID: {$data['mandate_id']}
        </p>
        <p>
            Name: {$data['name']}<br>
            Adresse: {$data['strasse']}, {$data['plz']}, {$data['ort']}
        </p>
        <p>
            IBAN: {$data['iban']}<br>
            Bank: {$data['bank']}
        </p>
        <p>
            Betrag: {$data['fee']}<br>
            Zahlungsrhytmus: {$data['frequ']}<br>
        </p>
        <p>
            Antrag Mitgliedschaft: {$data['memship']}<br>
            Nachricht an uns: {$data['mes']}
        </p>
        <p>
            Ich ermächtige den <strong>{$data['creditor_name']}</strong>,<br>
            Zahlungen von meinem Konto mittels Lastschrift einzuziehen.<br>
            Zugleich weise ich mein Kreditinstitut an,<br>
            die vom {$data['creditor_name']}<br>
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

        $path = sys_get_temp_dir() . '/sepa_' . uniqid() . '.pdf';
        $pdf->Output($path, 'F');

        return $path;
    }
}
