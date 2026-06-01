<!-- Hero Section -->
<section id="hero" class="section hero">
    <?= $md('home/hero') ?>
</section>

<!-- Features Section -->
<section id="features" class="section" aria-label="Galerie">
    <?= $md('home/features') ?>

    <?php foreach ($gallery('home/videos') as $video):
        if (empty($video['enabled'])) continue;
        $videoFile  = htmlspecialchars($video['file']        ?? '', ENT_QUOTES, 'UTF-8');
        $videoTitle = htmlspecialchars($video['title']       ?? '', ENT_QUOTES, 'UTF-8');
        $videoDesc  = htmlspecialchars($video['description'] ?? '', ENT_QUOTES, 'UTF-8');
        $base = '/assets/videos/' . $videoFile;
    ?>
    <figure class="section-video">
        <video
            controls
            preload="metadata"
            aria-label="<?= $videoTitle ?>"
            title="<?= $videoTitle ?>">
            <source src="<?= $base ?>.webm" type="video/webm">
            <source src="<?= $base ?>.mp4"  type="video/mp4">
        </video>
        <?php if ($videoDesc !== ''): ?>
        <figcaption class="section-video-caption"><?= $videoDesc ?></figcaption>
        <?php endif; ?>
    </figure>
    <?php endforeach; ?>

    <ul class="gallery-grid" role="list" aria-label="Bildergalerie" id="galleryGrid" data-gallery-show-all="false">
        <?php foreach ($gallery('home/gallery') as $index => $item):
            $file = htmlspecialchars($item['file'],      ENT_QUOTES, 'UTF-8');
            $alt  = htmlspecialchars($item['alt'],       ENT_QUOTES, 'UTF-8');
            $cat  = htmlspecialchars($item['category'] ?? '', ENT_QUOTES, 'UTF-8');
            $base = '/assets/images/content/' . $file;
        ?>
        <li class="gallery-item" data-gallery-category="<?= $cat ?>">
            <button class="gallery-thumb-btn" type="button"
                aria-label="Bild vergrößern: <?= $alt ?>"
                data-gallery-src="<?= $base ?>.webp"
                data-gallery-alt="<?= $alt ?>"
                data-gallery-index="<?= $index ?>">
                <picture>
                    <source srcset="<?= $base ?>.webp" type="image/webp">
                    <img class="gallery-thumb"
                        src="<?= $base ?>.jpg"
                        alt="<?= $alt ?>"
                        loading="lazy" width="400" height="300">
                </picture>
            </button>
        </li>
        <?php endforeach; ?>
    </ul>
</section>

<!-- Lightbox Overlay -->
<div id="galleryLightbox" class="lightbox"
    role="dialog" aria-modal="true" aria-label="Bildvorschau"
    tabindex="-1">
    <div class="lightbox-backdrop" data-lightbox-close></div>
    <div class="lightbox-dialog">
        <button class="lightbox-btn lightbox-btn--close" type="button"
            aria-label="Schließen" data-lightbox-close>&#x2715;</button>
        <button class="lightbox-btn lightbox-btn--prev" type="button"
            aria-label="Vorheriges Bild" data-lightbox-prev>&#x2039;</button>
        <figure class="lightbox-figure">
            <img id="lightboxImg" class="lightbox-img" src="" alt="">
            <figcaption id="lightboxCaption" class="lightbox-caption"></figcaption>
        </figure>
        <button class="lightbox-btn lightbox-btn--next" type="button"
            aria-label="Nächstes Bild" data-lightbox-next>&#x203A;</button>
    </div>
</div>

<!-- Stats Section -->
<section id="stats" class="section alt" aria-label="Unsere Zahlen">
    <div class="stats-grid">
        <?php foreach ($gallery('home/stats') as $stat):
            $value = (int) ($stat['value'] ?? 0);
            $label = htmlspecialchars($stat['label'] ?? '', ENT_QUOTES, 'UTF-8');
        ?>
        <div class="stats-item">
            <span class="stats-number" data-count-target="<?= $value ?>" aria-hidden="true">0</span>
            <span class="visually-hidden"><?= $value ?></span>
            <span class="stats-label"><?= $label ?></span>
        </div>
        <?php endforeach; ?>
    </div>
    <?= $md('home/stats') ?>
</section>

<!-- About Section -->
<section id="about" class="section">
    <?= $md('home/about') ?>
    <?php $aboutCards = $gallery('home/about-cards'); if ($aboutCards): ?>
    <div class="about-cards">
        <?php foreach ($aboutCards as $card):
            $svg   = $card['svg']   ?? '';
            $title = htmlspecialchars($card['title'] ?? '', ENT_QUOTES, 'UTF-8');
            $text  = htmlspecialchars($card['text']  ?? '', ENT_QUOTES, 'UTF-8');
        ?>
        <div class="about-card">
            <div class="about-card-icon"><?= $svg ?></div>
            <h3><?= $title ?></h3>
            <p><?= $text ?></p>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>

<!-- Contact Section -->
<section id="contact" class="section">
    <?= $md('home/contact') ?>
    <?php if (!empty($formPartial)): ?>
        <?php require __DIR__ . '/../' . $formPartial; ?>
    <?php endif; ?>
</section>
