<?php
$pageH1 = 'Impressum';
$navBase = 'index.php';
?>
<!DOCTYPE html>
<html lang="de">

<!-- This is a template for a one-page website - legal page-->

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="description" content="Das ist ein Template für eine Datenschutz-Page der One-Pager Website.">
  <!-- SEO -->
  <meta name="robots" content="noindex, follow">
  <!-- Title -->
  <title>Datenschutz – One-Pager Template</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="icon" href="/assets/icons/icon.svg" type="image/svg+xml">
  <link rel="icon" href="/assets/icons/favicon-32.png" sizes="32x32">
  <link rel="icon" href="/assets/icons/favicon-16.png" sizes="16x16">
  <link rel="icon" href="/assets/icons/favicon.ico">
  <link rel="apple-touch-icon" href="/assets/icons/apple-touch-icon.png">
  <link rel="manifest" href="assets/icons/site.webmanifest">
  <link rel="stylesheet" href="/assets/css/styles.css" />

  <!-- Styles -->
  <link rel="stylesheet" href="/assets/css/styles.css">
</head>

<body>
  <!-- Topbar -->
  <div class="topbar">
    <p>✨ Kostenloser Versand ab 50 €</p>
  </div>

  <!-- Header mit Logo, Desktop Navigation & Mobile Menu -->
  <header class="header">
    <div class="header-inner">
      <a href="index.html#hero" class="logo" aria-label="Startseite">
        <img class="logo-img" alt="Mein One-Pager" />
      </a>
      <h1>Datenschutzerklärung</h1>
      <!-- Desktop Navigation -->
      <nav class="nav-desktop" id="desktopMenu">
        <a href="index.html#hero">Hero</a>
        <a href="index.html#features">Features</a>
        <a href="index.html#about">Über uns</a>
        <a href="index.html#contact">Kontakt aufnehmen</a>
      </nav>

      <!-- Mobile Button -->
      <button id="menuToggle" class="menu-toggle" aria-label="Menü öffnen" aria-expanded="false">
        ☰
      </button>
    </div>

    <!-- Mobile Menü -->
    <nav id="mobileMenu" class="nav-mobile" aria-hidden="true">
      <a href="index.html#hero" tabindex="-1">Hero</a>
      <a href="index.html#features" tabindex="-1">Features</a>
      <a href="index.html#about" tabindex="-1">Über uns</a>
      <a href="index.html#contact" tabindex="-1">Kontakt</a>
    </nav>
  </header>
  <div id="menuBackdrop" class="menu-backdrop"></div>

  <!-- Main Content -->
  <main id="main" class="content">
    <section class="section-legal">
      <h2>1. Datenschutz auf einen Blick</h2>
      <p>
        Die folgenden Hinweise geben einen einfachen Überblick darüber,
        was mit Ihren personenbezogenen Daten passiert, wenn Sie diese Website besuchen.
      </p>
    </section>
    <!-- Weitere Abschnitte der Datenschutzerklärung -->
    <section class="section-legal">
      <h2>2. Verantwortliche Stelle</h2>
      <p>
        Max Mustermann<br>
        Musterstraße 1<br>
        12345 Musterstadt<br>
        E-Mail: <a href="mailto:info@example.de">info@example.de</a>
      </p>
    </section>
    <!-- Weitere Abschnitte der Datenschutzerklärung -->
    <section class="section-legal">
      <h2>3. Erhebung und Verarbeitung personenbezogener Daten</h2>
      <p>
        Personenbezogene Daten werden nur erhoben, wenn Sie uns diese freiwillig mitteilen,
        z. B. über das Kontaktformular.
      </p>
    </section>
    <!-- Weitere Abschnitte der Datenschutzerklärung -->
    <section class="section-legal">
      <h2>4. Kontaktformular</h2>
      <p>
        Wenn Sie uns per Kontaktformular Anfragen zukommen lassen, werden Ihre Angaben
        aus dem Formular inklusive der von Ihnen dort angegebenen Kontaktdaten zwecks
        Bearbeitung der Anfrage gespeichert.
      </p>
    </section>
    <!-- Weitere Abschnitte der Datenschutzerklärung -->
    <section class="section-legal">
      <h2>5. Ihre Rechte</h2>
      <ul>
        <li>Auskunft über Ihre gespeicherten Daten</li>
        <li>Berichtigung unrichtiger Daten</li>
        <li>Löschung Ihrer Daten</li>
        <li>Einschränkung der Verarbeitung</li>
        <li>Widerspruch gegen die Verarbeitung</li>
      </ul>
    </section>
  </main>
  <!-- Footer -->
  <!-- wird durch base.php eingebunden -->
  <!-- JavaScript -->
  <script src="./assets/js/main.js" defer></script>
</body>

</html>