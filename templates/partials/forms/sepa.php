<?php
/**
 * SEPA Kontaktformular
 */

if (empty($action)) {
    throw new RuntimeException('Form action not defined.');
}
?>
<form id="kontaktForm"
    method="post"
    action="<?= e($action) ?>"
    class="contact-form contact-form--<?= e($variant) ?>"
    novalidate
>
    <!-- CSRF -->
    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">

    <!-- Website -->
    <div class="website" aria-hidden="true">
        <label for="website">Website</label>
        <input
            id="website"
            type="text"
            name="website"
            tabindex="-1"
            autocomplete="off">
    </div>

    <!-- Vorname -->
    <label for="vorname">Vorname*</label>
    <input
        id="vorname"
        type="text"
        name="vorname"
        required
        aria-required="true"
        autocomplete="given-name"
    >

    <!-- Nachname -->
    <label for="nachname">Nachname*</label>
    <input
        id="nachname"
        type="text"
        name="nachname"
        required
        aria-required="true"
        autocomplete="family-name"
    >

    <!-- Adresse -->
    <div class="address-row">
        <label for="strasse">Straße & Hausnummer*</label>
        <input
            id="strasse"
            type="text"
            name="strasse"
            required
            aria-required="true"
            autocomplete="address-line1"
        >
        <label for="plz">PLZ*</label>
        <input
            id="plz"
            type="text"
            name="plz"
            required
            aria-required="true"
            autocomplete="postal-code"
        >
        <label for="ort">Ort*</label>
        <input
            id="ort"
            type="text"
            name="ort"
            required
            aria-required="true"
            autocomplete="address-level2"
        >
    </div>

    <!-- E-Mail -->
    <label for="email">E-Mail-Adresse*</label>
    <input
        id="email"
        type="email"
        name="email"
        required
        aria-required="true"
        autocomplete="email"
    >

    <!-- Beitrag -->
    <label for="beitrag">Mein Beitrag*
        <select
            name="betrag"
            required
            aria-required="true"
        >
            <option value="">Bitte auswählen</option>
            <option value="10 €">10 €</option>
            <option value="25 €">25 €</option>
            <option value="50 €">50 €</option>
            <option value="100 €">100 €</option>
            <option value="Nachricht">s. Nachricht</option>
        </select>
    </label>
    <label for="zahlungsrhytmus">Zahlungsrhythmus*
        <select
            name="zahlungsrhythmus"
            required
            aria-required="true"
        >
            <option value="">Bitte auswählen</option>
            <option value="Spende einmalig">Spende einmalig</option>
            <option value="Jahr">Jahr</option>
            <option value="Qurtal">Quartal</option>
            <option value="Monat">Monat</option>
            <option value="Nachricht">s. Nachricht</option>
        </select>
    </label>
    <label for="mitgliedschaft">Ich beantrage die Mitgliedschaft*
        <select
            name="mitgliedschaft"
            required
            aria-required="true"
        >
            <option value="">Bitte auswählen</option>
            <option value="Ja">Ja</option>
            <option value="Nein">Nein</option>
            <option value="Nachricht">s. Nachricht</option>
        </select>
    </label>

    <!-- Nachricht -->
    <label for="nachricht">Nachricht (optional)</label>
    <textarea
        id="nachricht"
        name="nachricht"
        rows="4">
    </textarea>

    <!-- DSGVO -->
    <div class="consent">
        <label class="consent-row">
            <span class="consent-label">DSGVO-Einverständnis*</span>
            <input
                id="consentCheckbox"
                name="consent"
                type="checkbox"
                required
                aria-required="true">
        </label>
    </div>

    <p class="consent-text">
        Ich willige ein, dass diese Website meine übermittelten Informationen speichert,
        sodass meine Anfrage bearbeitet werden kann.
    </p>

    <!-- SEPA-Lastschrift -->
    <p class="form-intro">
        Mit einem SEPA-Lastschriftmandat ermöglichen Sie uns,
        <?php echo $_ENV['SEPA_CREDITOR_NAME']; ?>, den für sie bequem Einzug von Ihrem Konto.
        Das Mandat kann jederzeit widerrufen werden (z.B. per Mail).
    </p>

    <h2>Bankverbindung</h2>

    <label for="iban">IBAN*</label>
    <input
        type="text"
        name="iban"
        required
        aria-required="true"
        autocomplete="iban"
    >
    
    <label for="bank">Name der Bank</label>
    <input
        type="text"
        name="Bank"
        aria-required="false"
        autocomplete="bank"
    >
    
    <h2>SEPA-Mandat</h2>

    <p class="form-intro">
        Ich ermächtige den <strong> <?php echo $_ENV['SEPA_CREDITOR_NAME']; ?></strong>,
        Zahlungen von meinem Konto mittels Lastschrift einzuziehen.<br>
        Unsere Gläubiger-ID: <?php echo $_ENV['SEPA_CREDITOR_ID']; ?><br>
        Zugleich weise ich mein Kreditinstitut an, die vom <?php echo $_ENV['SEPA_CREDITOR_NAME']; ?> auf mein Konto gezogenen Lastschriften einzulösen.
    </p>

    <p class="form-intro">
        <strong>Hinweis:</strong> Ich kann innerhalb von acht Wochen,
        beginnend mit dem Belastungsdatum, die Erstattung des belasteten Betrages verlangen.
    </p>

    <div class="cta-grid">
        <div class="consent-datenschutz">
            <label class="consent-row">
                <span>Ich habe die
                    <a class="form" href="/datenschutz">Datenschutzerklärung</a>
                    gelesen und stimme zu.
                </span>
                <input
                    id="consentDatenschutzCheckbox"
                    name="consent-datenschutz"
                    type="checkbox"
                    required
                    aria-required="true">
            </label>
        </div>

        <div class="consent-sepa">
            <label class="consent-row">
                <span>
                    Ich erteile ein SEPA-Lastschriftmandat.
                </span>
                <input
                    id="consentSepaCheckbox"
                    name="consent-sepa"
                    type="checkbox"
                    required
                    aria-required="true">
            </label>
        </div>
    </div>

    <button type="submit" class="btn-primary">Absenden</button>

    <div
        id="formMessage"
        class="form-message"
        role="alert"
        aria-live="assertive"
        aria-atomic="true"
        tabindex="-1"
    ></div>
</form>