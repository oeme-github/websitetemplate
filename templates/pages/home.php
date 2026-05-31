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

<!-- Stats Section – Count-Up Animation (data-count-target = Zielwert) -->
<section id="stats" class="section alt">
    <div class="section-inner">
        <h2>Zahlen &amp; Fakten</h2>
        <div class="stats-grid">
            <div class="stats-item">
                <span class="stats-number" data-count-target="120">0</span>
                <p class="stats-label">Mitglieder</p>
            </div>
            <div class="stats-item">
                <span class="stats-number" data-count-target="15">0</span>
                <p class="stats-label">Jahre aktiv</p>
            </div>
            <div class="stats-item">
                <span class="stats-number" data-count-target="42">0</span>
                <p class="stats-label">Veranstaltungen</p>
            </div>
            <div class="stats-item">
                <span class="stats-number" data-count-target="8">0</span>
                <p class="stats-label">Auszeichnungen</p>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="section">
    <h2>Über uns</h2>
    <p>Inhalt…</p>
    <p>Inhalt…</p>
</section>

<!-- Gallery Section – Lightbox & Tabs (data-gallery-category für Filter) -->
<section id="gallery" class="section alt">
    <div class="section-inner">
        <h2>Galerie</h2>
        <!-- galleryGrid: data-gallery-show-all="false" unterdrückt "Alle"-Tab -->
        <ul id="galleryGrid" class="gallery-grid">
            <li class="gallery-item" data-gallery-category="Kategorie A">
                <button class="gallery-thumb-btn"
                        data-gallery-index="0"
                        data-gallery-src="/assets/images/content/Frau_in_Bett.jpg"
                        data-gallery-alt="Beispielbild 1"
                        aria-label="Bild 1 vergrößern">
                    <img class="gallery-thumb"
                         src="/assets/images/content/Frau_in_Bett.jpg"
                         alt="Beispielbild 1"
                         loading="lazy">
                </button>
            </li>
            <li class="gallery-item" data-gallery-category="Kategorie A">
                <button class="gallery-thumb-btn"
                        data-gallery-index="1"
                        data-gallery-src="/assets/images/content/kueche.jpg"
                        data-gallery-alt="Beispielbild 2"
                        aria-label="Bild 2 vergrößern">
                    <img class="gallery-thumb"
                         src="/assets/images/content/kueche.jpg"
                         alt="Beispielbild 2"
                         loading="lazy">
                </button>
            </li>
            <li class="gallery-item" data-gallery-category="Kategorie B">
                <button class="gallery-thumb-btn"
                        data-gallery-index="2"
                        data-gallery-src="/assets/images/content/Terasse_in_BeantmungsWG.jpg"
                        data-gallery-alt="Beispielbild 3"
                        aria-label="Bild 3 vergrößern">
                    <img class="gallery-thumb"
                         src="/assets/images/content/Terasse_in_BeantmungsWG.jpg"
                         alt="Beispielbild 3"
                         loading="lazy">
                </button>
            </li>
            <li class="gallery-item" data-gallery-category="Kategorie B">
                <button class="gallery-thumb-btn"
                        data-gallery-index="3"
                        data-gallery-src="/assets/images/content/Wohnbereich_in_BeantmungsWG.jpg"
                        data-gallery-alt="Beispielbild 4"
                        aria-label="Bild 4 vergrößern">
                    <img class="gallery-thumb"
                         src="/assets/images/content/Wohnbereich_in_BeantmungsWG.jpg"
                         alt="Beispielbild 4"
                         loading="lazy">
                </button>
            </li>
        </ul>

        <!-- Lightbox -->
        <div id="galleryLightbox" class="lightbox" role="dialog" aria-modal="true"
             aria-label="Bildanzeige" aria-hidden="true">
            <div class="lightbox-backdrop" data-lightbox-close aria-hidden="true"></div>
            <div class="lightbox-dialog">
                <button class="lightbox-btn lightbox-btn--close" data-lightbox-close
                        aria-label="Schließen">✕</button>
                <button class="lightbox-btn" data-lightbox-prev aria-label="Vorheriges Bild">&#8592;</button>
                <figure class="lightbox-figure">
                    <img id="lightboxImg" class="lightbox-img" src="" alt="">
                    <figcaption id="lightboxCaption" class="lightbox-caption"></figcaption>
                </figure>
                <button class="lightbox-btn" data-lightbox-next aria-label="Nächstes Bild">&#8594;</button>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="section">
    <h2>Kontakt</h2>
    <!-- prüfen ob SEPA oder Kontaktformular -->
    <?php if (!empty($formPartial)): ?>
        <?php require __DIR__ . '/../' . $formPartial; ?>
    <?php endif; ?>
</section>
