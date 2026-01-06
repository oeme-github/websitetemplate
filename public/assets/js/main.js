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
  D) Kontaktformular – AJAX (neu & minimal)
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

    submitBtn?.setAttribute('disabled', 'disabled');
    form.setAttribute('aria-busy', 'true');

    try {
      const response = await fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: { 'Accept': 'application/json' },
        credentials: 'same-origin',
      });

      const contentType = response.headers.get('content-type') || '';
      const data = contentType.includes('application/json')
        ? await response.json()
        : null;

      if (response.ok && data?.ok) {
        // es scheint alles gut gegangen zu sein, jetzt noch sauber beenden
        setMessage(data.message || 'Nachricht gesendet.', true);
        form.reset();

        // CSRF-Token erneuern (AJAX-Flow)
        if (data.csrf) {
          const csrfInput = form.querySelector('input[name="_csrf"]');
          if (csrfInput) csrfInput.value = data.csrf;
        }
      } else {
        // Meldung aus dem Formular -> hier gab es ein Problem
        if (data?.errors && Array.isArray(data.errors)) {
          setMessage(data.errors.join(' · '), false);
        } else {
          setMessage(
            data?.message || 'Bitte Eingaben prüfen. xxx',
            false
          );
        }
      }
    } catch (err) {
      setMessage('Netzwerkfehler. Bitte später erneut versuchen.', false);
    } finally {
      submitBtn?.removeAttribute('disabled');
      form.removeAttribute('aria-busy');
    }
  });
})();
