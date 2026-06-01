<footer class="footer">
    <div class="footer-inner footer-grid">

        <div class="footer-col footer-copy">
            <?php $copyright = $gallery('legal/copyright'); ?>
            © <?= date('Y') ?> · <?= htmlspecialchars($copyright['owner'] ?? '', ENT_QUOTES, 'UTF-8') ?>
        </div>

        <nav class="footer-col footer-legal" aria-label="Rechtliches">
            <ul>
                <li><a href="impressum">Impressum</a></li>
                <li><a href="datenschutz">Datenschutz</a></li>
            </ul>
        </nav>

        <div class="footer-col footer-theme">
            <button type="button" class="theme-toggle" data-theme-toggle aria-label="Theme wechseln"
                aria-pressed="false">
                🌗 Theme
            </button>
            <label class="visually-hidden" for="colorSchemeSelect">Farbschema</label>
            <select id="colorSchemeSelect" class="color-scheme-select" data-color-scheme-select aria-label="Farbschema wählen">
                <option value="default">Default</option>
                <option value="warm">Warm</option>
                <option value="nature">Nature</option>
            </select>
        </div>
    </div>
</footer>