<?php
/**
 * Kontaktformular
 *
 * Erwartete Variablen:
 * @var string $action
 * @var string $variant
 */

$action  = $action  ?? '/send_kontakt.php';
$variant = $variant ?? 'default';
?>

<form
    id="kontaktForm"
    method="post"
    action="<?= e($action) ?>"
    class="contact-form contact-form--<?= e($variant) ?>"
    novalidate
>
    <!-- CSRF -->
    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">

    <!-- Honeypot -->
    <div class="website" aria-hidden="true">
        <label for="website">Website</label>
        <input id="website" type="text" name="website" tabindex="-1" autocomplete="off">
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

    <!-- Nachricht -->
    <label for="nachricht">Nachricht (optional)</label>
    <textarea id="nachricht" name="nachricht" rows="4"></textarea>

    <!-- DSGVO -->
    <div class="consent">
        <label class="consent-row">
            <span class="consent-label">DSGVO-Einverständnis*</span>
            <input
                id="consentCheckbox"
                name="consent"
                type="checkbox"
                required
                aria-required="true"
            >
        </label>
    </div>

    <p class="consent-text">
        Ich willige ein, dass diese Website meine übermittelten Informationen speichert,
        sodass meine Anfrage beantwortet werden kann.
    </p>

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

