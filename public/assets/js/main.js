'use strict';

/* =====================================================
  A) Header – Scroll Verhalten
===================================================== */
(function () {
  const header = document.querySelector('.site-header');
  if (!header) return;

  let lastY = window.scrollY;
  const threshold = 10;

  window.addEventListener(
    'scroll',
    () => {
      const currentY = window.scrollY;
      const delta = currentY - lastY;

      if (Math.abs(delta) < threshold) return;

      if (delta > 0 && currentY > 100) {
        header.classList.add('is-hidden');
      } else {
        header.classList.remove('is-hidden');
      }

      lastY = currentY;
    },
    { passive: true }
  );
})();

/* =====================================================
  B) Theme (Dark / Light)
===================================================== */
(function () {
  const STORAGE_KEY = 'theme';
  const root = document.documentElement;
  const toggle = document.querySelector('[data-theme-toggle]');

  if (!toggle) return;

  const storedTheme = localStorage.getItem(STORAGE_KEY);
  if (storedTheme) {
    root.setAttribute('data-theme', storedTheme);
  }

  toggle.addEventListener('click', () => {
    const current = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    root.setAttribute('data-theme', current);
    localStorage.setItem(STORAGE_KEY, current);
  });
})();

/* =====================================================
  B2) Color Scheme Selector
===================================================== */
(function () {
  const STORAGE_KEY = 'colorScheme';
  const VALID_SCHEMES = ['default', 'warm', 'nature'];
  const root = document.documentElement;
  const selector = document.querySelector('[data-color-scheme-select]');

  if (!selector) return;

  const stored = localStorage.getItem(STORAGE_KEY);
  const initial = VALID_SCHEMES.includes(stored) ? stored : 'default';
  root.setAttribute('data-color-scheme', initial);
  selector.value = initial;

  selector.addEventListener('change', (e) => {
    const scheme = e.target.value;
    if (!VALID_SCHEMES.includes(scheme)) return;
    root.setAttribute('data-color-scheme', scheme);
    localStorage.setItem(STORAGE_KEY, scheme);
  });
})();

/* =====================================================
  C) Mobile Navigation
===================================================== */
(function () {
  const toggle = document.querySelector('[data-nav-toggle]');
  const nav = document.querySelector('[data-nav]');

  if (!toggle || !nav) return;

  toggle.addEventListener('click', () => {
    const expanded = toggle.getAttribute('aria-expanded') === 'true';
    toggle.setAttribute('aria-expanded', String(!expanded));
    nav.classList.toggle('is-open');
  });
})();

/* =====================================================
  C1) Close on Link Click
===================================================== */
(function () {
  const toggle = document.querySelector('[data-nav-toggle]');
  const nav = document.querySelector('[data-nav]');

  if (!toggle || !nav) return;

  nav.addEventListener('click', (e) => {
    const link = e.target.closest('a');
    if (!link) return;

    nav.classList.remove('is-open');
    toggle.setAttribute('aria-expanded', 'false');
  });
})();

/* =====================================================
  C2) Mobile Navigation – Close on Scroll Down
===================================================== */
(function () {
  const toggle = document.querySelector('[data-nav-toggle]');
  const nav = document.querySelector('[data-nav]');
  if (!toggle || !nav) return;

  let lastY = window.scrollY;

  window.addEventListener(
    'scroll',
    () => {
      const currentY = window.scrollY;

      if (!nav.classList.contains('is-open')) {
        lastY = currentY;
        return;
      }

      if (currentY > lastY + 5) {
        nav.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
      }

      lastY = currentY;
    },
    { passive: true }
  );
})();

/* =====================================================
  C3) Mobile Navigation – Close on Hash Navigation
===================================================== */
(function () {
  const toggle = document.querySelector('[data-nav-toggle]');
  const nav = document.querySelector('[data-nav]');
  if (!toggle || !nav) return;

  window.addEventListener('hashchange', () => {
    if (!nav.classList.contains('is-open')) return;

    nav.classList.remove('is-open');
    toggle.setAttribute('aria-expanded', 'false');
  });
})();

/* =====================================================
  C4) Mobile Navigation – Close on Internal Anchor Click (robust)
===================================================== */
(function () {
  const toggle = document.querySelector('[data-nav-toggle]');
  const nav = document.querySelector('[data-nav]');
  if (!toggle || !nav) return;

  function closeMenu() {
    if (!nav.classList.contains('is-open')) return;
    nav.classList.remove('is-open');
    toggle.setAttribute('aria-expanded', 'false');
  }

  document.addEventListener('click', (e) => {
    const a = e.target.closest('a');
    if (!a) return;

    const href = a.getAttribute('href') || '';
    const isInternalAnchor = href.startsWith('#') || href.startsWith('/#');

    if (!isInternalAnchor) return;

    closeMenu();
  });
})();

/* =====================================================
  E) Cookie Notice – Dismiss
===================================================== */
(function () {
  var STORAGE_KEY = 'cookieNoticeDismissed';
  var notice = document.querySelector('.cookie-notice');
  var btn = document.querySelector('[data-cookie-dismiss]');

  if (!notice || !btn) return;

  if (localStorage.getItem(STORAGE_KEY)) {
    notice.remove();
    return;
  }

  btn.addEventListener('click', function () {
    notice.classList.add('is-hidden');
    localStorage.setItem(STORAGE_KEY, '1');
    notice.addEventListener('transitionend', function () {
      notice.remove();
    }, { once: true });
  });
})();

/* =====================================================
  F) Stats Counter – Count-Up Animation
===================================================== */
(function () {
  var elements = document.querySelectorAll('[data-count-target]');
  if (!elements.length) return;

  var duration = 2000;
  var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function showFinalValue(el) {
    el.textContent = el.getAttribute('data-count-target');
  }

  function animateCount(el) {
    var target = parseInt(el.getAttribute('data-count-target'), 10);
    var start = performance.now();

    function tick(now) {
      var t = Math.min((now - start) / duration, 1);
      var eased = 1 - Math.pow(1 - t, 3);
      el.textContent = Math.round(eased * target);
      if (t < 1) {
        requestAnimationFrame(tick);
      }
    }

    requestAnimationFrame(tick);
  }

  if (reducedMotion || !('IntersectionObserver' in window)) {
    elements.forEach(showFinalValue);
    return;
  }

  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (!entry.isIntersecting) return;
      animateCount(entry.target);
      observer.unobserve(entry.target);
    });
  }, { threshold: 0.25 });

  elements.forEach(function (el) {
    observer.observe(el);
  });
})();

/* =====================================================
  D) Kontaktformular – AJAX
===================================================== */
(function () {
  const form = document.getElementById('kontaktForm');
  if (!form || !window.fetch) return;

  const messageBox = document.getElementById('formMessage');
  const submitBtn = form.querySelector('button[type="submit"]');

  let fadeTimer = null;

  if (messageBox) {
    messageBox.addEventListener('transitionend', () => {
      if (messageBox.classList.contains('is-fading')) {
        messageBox.textContent = '';
        messageBox.className = 'form-message';
      }
    });
  }

  function setMessage(text, isOk) {
    if (!messageBox) return;
    clearTimeout(fadeTimer);
    messageBox.textContent = text;
    messageBox.className = 'form-message ' + (isOk ? 'is-ok' : 'is-error');
    messageBox.focus();

    if (isOk) {
      fadeTimer = setTimeout(() => {
        messageBox.classList.add('is-fading');
      }, 5000);
    }
  }

  function clearFieldErrors() {
    form.querySelectorAll('.field-error-msg').forEach((el) => el.remove());
    form.querySelectorAll('.field-error').forEach((el) => {
      el.classList.remove('field-error');
      el.removeAttribute('aria-invalid');
      el.removeAttribute('aria-describedby');
    });
  }

  function showFieldErrors(errors) {
    let firstErrorEl = null;

    Object.entries(errors).forEach(([field, message]) => {
      const input = form.querySelector('[name="' + field + '"]');
      if (!input) return;

      const errorId = field.replace(/[^a-zA-Z0-9]/g, '-') + '-error';
      const insertAfter = input.closest('label') || input;

      input.classList.add('field-error');
      input.setAttribute('aria-invalid', 'true');
      input.setAttribute('aria-describedby', errorId);

      const errorEl = document.createElement('span');
      errorEl.id = errorId;
      errorEl.className = 'field-error-msg';
      errorEl.textContent = message;
      insertAfter.insertAdjacentElement('afterend', errorEl);

      if (!firstErrorEl) firstErrorEl = input;
    });

    return firstErrorEl;
  }

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    e.stopPropagation();

    clearFieldErrors();

    submitBtn?.setAttribute('disabled', 'disabled');
    submitBtn.textContent = 'Sende...';
    form.setAttribute('aria-busy', 'true');

    try {
      const response = await fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: { 'Accept': 'application/json' },
        credentials: 'same-origin',
      });

      const data = await response.json();

      if (response.ok && data.ok) {
        setMessage(data.message || 'Nachricht gesendet.', true);
        form.reset();

        if (data.csrf) {
          const csrfInput = form.querySelector('input[name="_csrf"]');
          if (csrfInput) csrfInput.value = data.csrf;
        }
        return;
      }

      if (data.errors && typeof data.errors === 'object' && !Array.isArray(data.errors)) {
        setMessage(data.message || 'Bitte Eingaben prüfen.', false);
        const firstErrorEl = showFieldErrors(data.errors);
        if (firstErrorEl) {
          if (firstErrorEl.scrollIntoView) {
            firstErrorEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }
          firstErrorEl.focus();
        }
        return;
      }

      setMessage(data.message || 'Fehler.', false);

    } catch (err) {
      console.error('Fetch failed:', err);
      setMessage(
        'Netzwerkfehler. Bitte Internetverbindung prüfen.',
        false
      );
    } finally {
      submitBtn?.removeAttribute('disabled');
      submitBtn.textContent = 'Absenden';
      form.removeAttribute('aria-busy');
    }
  });
})();

/* =====================================================
  G) Image Gallery – Lightbox
===================================================== */
(function () {
  var grid     = document.getElementById('galleryGrid');
  var lightbox = document.getElementById('galleryLightbox');
  if (!grid || !lightbox) return;

  var img     = document.getElementById('lightboxImg');
  var caption = document.getElementById('lightboxCaption');
  var btnPrev = lightbox.querySelector('[data-lightbox-prev]');
  var btnNext = lightbox.querySelector('[data-lightbox-next]');

  var items = Array.prototype.slice.call(
    grid.querySelectorAll('[data-gallery-index]')
  ).sort(function (a, b) {
    return +a.getAttribute('data-gallery-index') - +b.getAttribute('data-gallery-index');
  });

  var currentIndex = 0;
  var triggerEl    = null;

  function showImage(index) {
    var item = items[index];
    if (!item) return;
    img.src = item.getAttribute('data-gallery-src');
    img.alt = item.getAttribute('data-gallery-alt');
    caption.textContent = item.getAttribute('data-gallery-alt');
    btnPrev.disabled = (index === 0);
    btnNext.disabled = (index === items.length - 1);
  }

  function openLightbox(index) {
    currentIndex = index;
    showImage(currentIndex);
    lightbox.classList.add('is-open');
    lightbox.setAttribute('aria-hidden', 'false');
    grid.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = 'hidden';
    lightbox.querySelector('[data-lightbox-close]').focus();
  }

  function closeLightbox() {
    lightbox.classList.remove('is-open');
    lightbox.setAttribute('aria-hidden', 'true');
    grid.removeAttribute('aria-hidden');
    document.body.style.overflow = '';
    if (triggerEl) triggerEl.focus();
    triggerEl = null;
  }

  grid.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-gallery-index]');
    if (!btn) return;
    triggerEl = btn;
    openLightbox(+btn.getAttribute('data-gallery-index'));
  });

  lightbox.addEventListener('click', function (e) {
    if (e.target.closest('[data-lightbox-close]')) { closeLightbox(); return; }
    if (e.target.closest('[data-lightbox-prev]'))  { if (currentIndex > 0) showImage(--currentIndex); return; }
    if (e.target.closest('[data-lightbox-next]'))  { if (currentIndex < items.length - 1) showImage(++currentIndex); return; }
  });

  document.addEventListener('keydown', function (e) {
    if (!lightbox.classList.contains('is-open')) return;
    if (e.key === 'Escape')     { closeLightbox(); return; }
    if (e.key === 'ArrowLeft')  { if (currentIndex > 0) showImage(--currentIndex); return; }
    if (e.key === 'ArrowRight') { if (currentIndex < items.length - 1) showImage(++currentIndex); return; }
    if (e.key === 'Tab') {
      var focusable = Array.prototype.slice.call(
        lightbox.querySelectorAll('button:not([disabled])')
      );
      var first = focusable[0];
      var last  = focusable[focusable.length - 1];
      if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
      else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    }
  });
})();

/* =====================================================
  I) IBAN Live-Validierung
===================================================== */
(function () {
  var ibanInput = document.getElementById('iban');
  var bankInput = document.getElementById('bank');
  var statusEl  = document.getElementById('iban-status');

  if (!ibanInput || !statusEl) return;

  function normalize(raw) {
    return raw.replace(/\s+/g, '').toUpperCase();
  }

  function looksLikeIban(iban) {
    return /^[A-Z]{2}\d{2}[A-Z0-9]{11,30}$/.test(iban);
  }

  function setStatus(text, modifier) {
    statusEl.textContent = text;
    statusEl.className = 'iban-status' + (modifier ? ' ' + modifier : '');
  }

  var debounceTimer = null;

  ibanInput.addEventListener('input', function () {
    clearTimeout(debounceTimer);
    var iban = normalize(ibanInput.value);

    if (iban === '') {
      setStatus('', '');
      return;
    }

    if (!looksLikeIban(iban)) {
      setStatus('IBAN-Format ungültig', 'is-invalid');
      return;
    }

    debounceTimer = setTimeout(async function () {
      setStatus('Prüfe…', 'is-loading');

      try {
        var res  = await fetch('/iban_lookup.php?iban=' + encodeURIComponent(iban), {
          credentials: 'same-origin',
          headers: { 'Accept': 'application/json' },
        });
        var data = await res.json();

        if (data.ok && data.bank) {
          setStatus(data.bank, 'is-valid');
          if (bankInput) bankInput.value = data.bank;
        } else {
          setStatus('IBAN gültig, Bank nicht gefunden', 'is-valid');
        }
      } catch (_) {
        setStatus('Prüfung fehlgeschlagen', 'is-invalid');
      }
    }, 600);
  });
})();

/* =====================================================
  H) Gallery Tabs
===================================================== */
(function () {
  var grid = document.getElementById('galleryGrid');
  if (!grid) return;

  var items = Array.prototype.slice.call(grid.querySelectorAll('.gallery-item'));
  if (!items.length) return;

  var categories = [];
  items.forEach(function (item) {
    var cat = item.dataset.galleryCategory;
    if (cat && categories.indexOf(cat) === -1) categories.push(cat);
  });

  if (categories.length < 2) return;

  var showAll = grid.dataset.galleryShowAll !== 'false';

  var nav = document.createElement('div');
  nav.className = 'gallery-tabs';
  nav.setAttribute('role', 'group');
  nav.setAttribute('aria-label', 'Galerie filtern');

  var tabDefs = showAll
    ? [{ label: 'Alle', filter: '' }].concat(categories.map(function (cat) { return { label: cat, filter: cat }; }))
    : categories.map(function (cat) { return { label: cat, filter: cat }; });

  tabDefs.forEach(function (def, i) {
    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'gallery-tab';
    btn.textContent = def.label;
    btn.setAttribute('aria-pressed', showAll && i === 0 ? 'true' : 'false');
    btn.dataset.galleryFilter = def.filter;
    nav.appendChild(btn);
  });

  grid.before(nav);

  if (!showAll && tabDefs.length > 0) {
    nav.querySelector('.gallery-tab').setAttribute('aria-pressed', 'true');
    var firstFilter = tabDefs[0].filter;
    items.forEach(function (item) {
      item.hidden = item.dataset.galleryCategory !== firstFilter;
    });
  }

  nav.addEventListener('click', function (e) {
    var btn = e.target.closest('.gallery-tab');
    if (!btn) return;
    var filter = btn.dataset.galleryFilter;

    Array.prototype.slice.call(nav.querySelectorAll('.gallery-tab')).forEach(function (b) {
      b.setAttribute('aria-pressed', b === btn ? 'true' : 'false');
    });

    items.forEach(function (item) {
      item.hidden = filter ? item.dataset.galleryCategory !== filter : false;
    });
  });
})();
