<?php
namespace App;

use TCPDF;

class SepaPdf
{
    public static function create(array $data): string
    {
        $pdf = new TCPDF();
        $pdf->SetCreator('Beatmungswohngemeinschaft in Ofterdingen');
        $pdf->SetAuthor('Förderverein');
        $pdf->SetTitle('SEPA-Lastschriftmandat');
        $pdf->AddPage();

        $html = "
        <h1>SEPA-Lastschriftmandat</h1>
        <p>
            Name: {$data['name']}<br>
            Adresse: {$data['address']}<br>
            IBAN: {$data['iban']}<br>
            Bank: {$data['bank']}<br>
            Betrag: {$data['fee']}<br>
            Zahlungsrhytmus: {$data['frequ']}<br>
            Antrag Mitgliedschaft: {$data['memship']}<br>
            Nachricht an uns: {$data['mes']}<br><br>

            Ich ermächtige den <strong>Förderverein der Tübinger Hawks e.V.</strong>,<br>
            Zahlungen von meinem Konto mittels Lastschrift einzuziehen.<br>
            Zugleich weise ich mein Kreditinstitut an,<br>
            die vom Förderverein der Tübinger Hawks e.V. <br>
            auf mein Konto gezogenen Lastschriften einzulösen.<br><br>
            Eine Unterschrift ist nicht nötig.<br>
        </p>
        ";

        $pdf->writeHTML($html);

        $path = sys_get_temp_dir() . '/sepa_' . uniqid() . '.pdf';
        $pdf->Output($path, 'F');

        return $path;
    }
}
