'use strict';

/* =====================================================
  API / Backend Endpoints
===================================================== */
const BASE_PATH = document.body.dataset.basePath || '';
const API = {
  csrf: `${BASE_PATH}/PHP/csrf.php`,
  send: `${BASE_PATH}/PHP/send_kontakt.php`,
};

/* =====================================================
  THEME (Dark / Light)
===================================================== */
(function () {
  const STORAGE_KEY = 'theme';
  const root = document.documentElement;
  const toggle = document.getElementById('themeToggle');

  function getInitialTheme() {
    const stored = localStorage.getItem(STORAGE_KEY);
    if (stored) return stored;
    return window.matchMedia('(prefers-color-scheme: dark)').matches
      ? 'dark'
      : 'light';
  }

  function setTheme(theme) {
    root.setAttribute('data-theme', theme);
    localStorage.setItem(STORAGE_KEY, theme);
    toggle?.setAttribute('aria-pressed', theme === 'dark');
  }

  setTheme(getInitialTheme());

  toggle?.addEventListener('click', () => {
    const current = root.getAttribute('data-theme');
    setTheme(current === 'dark' ? 'light' : 'dark');
  });
})();

/* =====================================================
  MOBILE MENU (Slide + Fade)
===================================================== */
const menuToggle = document.getElementById('menuToggle');
const mobileMenu = document.getElementById('mobileMenu');
const backdrop = document.getElementById('menuBackdrop');
let lastFocusedElement = null;

// Menü öffnen
function openMenu() {
  lastFocusedElement = document.activeElement;

  mobileMenu.classList.add('is-open');

  // aria-hidden vollständig entfernen
  mobileMenu.removeAttribute('aria-hidden');

  mobileMenu.querySelectorAll('a').forEach(link => {
    link.removeAttribute('tabindex');
  });

  backdrop?.classList.add('is-active');
  menuToggle.setAttribute('aria-expanded', 'true');

  const firstLink = mobileMenu.querySelector('a');
  firstLink?.focus();

  trapFocus(mobileMenu);
}

// Menü schließen
function closeMenu() {
  mobileMenu.classList.remove('is-open');
  mobileMenu.setAttribute('aria-hidden', 'true');
 mobileMenu.querySelectorAll('a').forEach(link => {
    link.setAttribute('tabindex', '-1');
  });
  backdrop?.classList.remove('is-active');
  menuToggle.setAttribute('aria-expanded', 'false');

  // Fokus zurück
  lastFocusedElement?.focus();
}

// Klick auf Toggle öffnet/schließt
menuToggle.addEventListener('click', () => {
  const isOpen = mobileMenu.classList.toggle('is-open');

  if (isOpen) {
    // Menü geöffnet
    mobileMenu.removeAttribute('aria-hidden');

    mobileMenu.querySelectorAll('a').forEach(link => {
      link.removeAttribute('tabindex');
    });

    menuToggle.setAttribute('aria-expanded', 'true');
  } else {
    // Menü geschlossen
    mobileMenu.setAttribute('aria-hidden', 'true');

    mobileMenu.querySelectorAll('a').forEach(link => {
      link.setAttribute('tabindex', '-1');
    });

    menuToggle.setAttribute('aria-expanded', 'false');
  }
});

// Klick auf Backdrop schließt
backdrop?.addEventListener('click', closeMenu);

// Klick auf Link schließt
mobileMenu?.addEventListener('click', (e) => {
  if (e.target.tagName === 'A') closeMenu();
});

// ESC schließt
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') closeMenu();
});

// Fokus-Trap
function trapFocus(container) {
  const focusable = container.querySelectorAll(
    'a, button, input, textarea, select, [tabindex]:not([tabindex="-1"])'
  );

  if (!focusable.length) return;

  const first = focusable[0];
  const last = focusable[focusable.length - 1];

  container.addEventListener('keydown', (e) => {
    if (e.key !== 'Tab') return;

    if (e.shiftKey && document.activeElement === first) {
      e.preventDefault();
      last.focus();
    } else if (!e.shiftKey && document.activeElement === last) {
      e.preventDefault();
      first.focus();
    }
  });
}

/* =====================================================
  COUNT-UP ANIMATION
===================================================== */
const counters = document.querySelectorAll('.count');
let countersStarted = false;

function startCounters() {
  if (countersStarted) return;

  counters.forEach(counter => {
    const target = Number(counter.dataset.target);
    let current = 0;
    const increment = target / 80;

    function update() {
      current += increment;
      if (current < target) {
        counter.textContent = Math.ceil(current);
        requestAnimationFrame(update);
      } else {
        counter.textContent = target + '+';
      }
    }

    update();
  });

  countersStarted = true;
}

// Trigger bei Scroll
const counterSection = document.getElementById('zahlen');
if (counterSection) {
  window.addEventListener('scroll', () => {
    if (counterSection.getBoundingClientRect().top < window.innerHeight * 0.8) {
      startCounters();
    }
  }, { passive: true });
}

/* =====================================================
  FORM HELPERS
===================================================== */
function setButtonLoading(button, text = 'Sende...') {
  button.disabled = true;
  button.textContent = text;
}

function resetButton(button, text = 'Absenden') {
  button.disabled = false;
  button.textContent = text;
}

function clearFormMessage() {
  const box = document.getElementById('formMessage');
  if (box) {
    box.innerHTML = '';
    box.className = 'form-message';
  }
}

function showFormMessage(message, type = 'error') {
  const box = document.getElementById('formMessage');
  if (!box) return;
  box.textContent = message;
  box.className = `form-message ${type}`;
}

function showValidationErrors(errors) {
  const box = document.getElementById('formMessage');
  if (!box) return;

  // Rolle für Fehler
  box.setAttribute('role', 'alert');
  box.setAttribute('aria-live', 'assertive');

  box.innerHTML =
    '<strong>Bitte überprüfen Sie Ihre Eingaben:</strong><ul>' +
    Object.values(errors).map(msg => `<li>${msg}</li>`).join('') +
    '</ul>';

  box.className = 'form-message error';
  box.focus();

  // Felder markieren (ARIA + visuell)
  Object.keys(errors).forEach((field) => {
    const input = document.querySelector(`[name="${field}"]`);
    if (input) {
      input.classList.add('field-error');
      input.setAttribute('aria-invalid', 'true');
    }
  });
}

function focusFirstError(errors) {
  const field = document.querySelector(
    `[name="${Object.keys(errors)[0]}"]`
  );
  field?.focus();
}

/* =====================================================
  FORM SUBMIT
===================================================== */
async function submitForm(url, formData) {
  const response = await fetch(url, {
    method: 'POST',
    body: formData,
  });

  const contentType = response.headers.get('content-type') || '';

  if (response.status === 204) return null;

  if (response.status === 422 && contentType.includes('application/json')) {
    return await response.json();
  }

  if (!contentType.includes('application/json')) {
    throw new Error(await response.text());
  }

  if (!response.ok) {
    throw new Error(await response.text());
  }

  return await response.json();
}

/* =====================================================
  FORM INIT
===================================================== */
const form = document.getElementById('kontaktForm');
if (form) {
  const button = document.getElementById('submitBtn');
  button.disabled = true;

  // CSRF Token
  fetch(API.csrf, { credentials: 'same-origin' })
    .then(res => res.json())
    .then(data => {
      document.getElementById('csrf_token').value = data.csrf_token;
      button.disabled = false;
    })
    .catch(() => console.error('CSRF-Token Fehler'));

  form.addEventListener('input', (e) => {
    e.target.classList.remove('field-error');
    e.target.removeAttribute('aria-invalid');
  });

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    clearFormMessage();
    setButtonLoading(button);

    try {
      const result = await submitForm(API.send, new FormData(form));
      form.setAttribute('aria-busy', 'true');

      if (!result || result.spam) {
        resetButton(button);
        form.removeAttribute('aria-busy');
        return;
      }

      if (result.errors) {
        showValidationErrors(result.errors);
        focusFirstError(result.errors);
        resetButton(button);
        form.removeAttribute('aria-busy');
        return;
      }

      if (result.success) {
        form.reset();
        formMessage.setAttribute('role', 'status');
        formMessage.setAttribute('aria-live', 'polite');
        formMessage.textContent = 'Vielen Dank! Ihre Nachricht wurde erfolgreich gesendet.';
        formMessage.className = 'form-message success';
        formMessage.classList.add('success');
      }
    }
    catch {
      formMessage.setAttribute('role', 'alert');
      formMessage.setAttribute('aria-live', 'assertive');
      formMessage.textContent = 'Es ist ein technischer Fehler aufgetreten. Bitte versuchen Sie es später erneut.';
      formMessage.className = 'form-message error';
      formMessage.focus();
      setTimeout(() => { formMessage.setAttribute('role', 'status'); }, 100);
    }
    finally {
      resetButton(button);
      form.removeAttribute('aria-busy');
    }
  });

  form.addEventListener('input', (e) => {
    e.target.classList.remove('field-error');
  });
}
