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
    <!-- prüfen ob SEAP oder Kontaktformular -->
    <?php if (!empty($formPartial)): ?>
        <?php require __DIR__ . '/../' . $formPartial; ?>
    <?php endif; ?>
</section>