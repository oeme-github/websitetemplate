'use strict';

/* =====================================================
  Header / Topbar – Scroll Verhalten
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
        // scroll down
        header.classList.add('is-hidden');
      } else {
        // scroll up
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

    // Menü schließen
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

      // nur reagieren, wenn Menü offen ist
      if (!nav.classList.contains('is-open')) {
        lastY = currentY;
        return;
      }

      // nur bei Scroll nach unten
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

  // Schließt bei Klick auf interne Anchor-Links überall auf der Seite
  document.addEventListener('click', (e) => {
    const a = e.target.closest('a');
    if (!a) return;

    const href = a.getAttribute('href') || '';
    // interne Anchors: "#..." oder "/#..."
    const isInternalAnchor =
      href.startsWith('#') || href.startsWith('/#');

    if (!isInternalAnchor) return;

    closeMenu();
  });
})();

/* =====================================================
  D) Kontaktformular – AJAX (final & schlank)
===================================================== */
(function () {
  const form = document.getElementById('kontaktForm');
  if (!form || !window.fetch) return;

  const messageBox = document.getElementById('formMessage');
  const submitBtn = form.querySelector('button[type="submit"]');

  function setMessage(text, isOk) {
    if (!messageBox) return;
    messageBox.textContent = text;
    messageBox.className = 'form-message ' + (isOk ? 'is-ok' : 'is-error');
    messageBox.focus();
  }

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    e.stopPropagation();

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

      const data = await response.json(); // jetzt garantiert

      if (response.ok && data.ok) {
        setMessage(data.message || 'Nachricht gesendet.', true);
        form.reset();

        if (data.csrf) {
          const csrfInput = form.querySelector('input[name="_csrf"]');
          if (csrfInput) csrfInput.value = data.csrf;
        }
        return;
      }

      if (Array.isArray(data.errors)) {
        setMessage(data.errors.join(' · '), false);
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
