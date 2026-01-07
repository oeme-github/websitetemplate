'use strict';


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
