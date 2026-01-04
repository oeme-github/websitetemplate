<!-- Hero Section -->
<section id="hero" class="section hero">
    <h2>Hero Section</h2>
    <p>Kurze Beschreibung oder Call-to-Action</p>
</section>
<!-- Features Section -->
<section id="features" class="section">
    <h2>Features</h2>
    <p>Inhalt…</p>
    <p>Inhalt…</p>
</section>
<!-- About Section -->
<section id="about" class="section alt">
    <h2>Über uns</h2>
    <p>Inhalt…</p>
    <p>Inhalt…</p>
</section>
<!-- Contact Section -->
<section id="contact" class="section">
    <h2>Kontakt</h2>
    <form id="kontaktForm">
        <!-- CSRF -->
        <input type="hidden" name="_csrf" value="<?= isset($_SESSION['csrf_token']) ? e($_SESSION['csrf_token']) : '' ?>">

        <!-- Vorname -->
        <label for="vorname">Vorname*</label>
        <input id="vorname" type="text" name="vorname" required aria-required="true" autocomplete="given-name">
        <!-- Nachname -->
        <label for="nachname">Nachname*</label>
        <input id="nachname" type="text" name="nachname" required aria-required="true" autocomplete="family-name">
        <!-- E-Mail -->
        <label for="email">E-Mail-Adresse*</label>
        <input id="email" type="email" name="email" required aria-required="true" autocomplete="off">
        <!-- Website -->
        <div class="website" aria-hidden="true">
            <label for="website">Website</label>
            <input id="website" type="text" name="website" tabindex="-1" autocomplete="off">
        </div>
        <!-- Nachricht -->
        <label for="nachricht">Nachricht (optional)</label>
        <textarea id="nachricht" name="Nachricht" rows="4"></textarea>
        <!-- DSGVO-Einverständnis -->
        <div class="consent">
            <label class="consent-row">
                <span class="consent-label">DSGVO-Einverständnis*</span>
                <input id="consentCheckbox" name="consent" type="checkbox" required aria-required="true">
            </label>
        </div>
        <!-- DSGVO-Einverständnistext -->
        <p class="consent-text">
            Ich willige ein, dass diese Website meine übermittelten Informationen speichert,
            sodass meine Anfrage beantwortet werden kann.
        </p>
        <!-- Absenden -->
        <button type="submit" class="btn-primary" id="submitBtn">Absenden</button>
        <!-- Formularmeldung -->
        <div id="formMessage" class="form-message" role="alert" aria-live="assertive" aria-atomic="true" tabindex="-1">
        </div>
    </form>
</section>