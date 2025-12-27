'use strict';

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
const backdrop   = document.getElementById('menuBackdrop');
let lastFocusedElement = null;

// Menü öffnen
function openMenu() {
  lastFocusedElement = document.activeElement;

  mobileMenu.classList.add('is-open');
  mobileMenu.setAttribute('aria-hidden', 'false');

  backdrop?.classList.add('is-active');
  menuToggle.setAttribute('aria-expanded', 'true');

  // Fokus ins Menü
  const firstLink = mobileMenu.querySelector('a');
  firstLink?.focus();

  trapFocus(mobileMenu);
}

// Menü schließen
function closeMenu() {
  mobileMenu.classList.remove('is-open');
  mobileMenu.setAttribute('aria-hidden', 'true');

  backdrop?.classList.remove('is-active');
  menuToggle.setAttribute('aria-expanded', 'false');

  // Fokus zurück
  lastFocusedElement?.focus();
}

// Klick auf Toggle öffnet/schließt
menuToggle?.addEventListener('click', () => {
  const isOpen = mobileMenu.classList.contains('is-open');
  isOpen ? closeMenu() : openMenu();
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

function resetButton(button, text = 'SEPA-Mandat absenden') {
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

  box.innerHTML = '';
  box.className = 'form-message error';

  const list = document.createElement('ul');
  list.style.margin = '0';
  list.style.paddingLeft = '1.2rem';

  Object.entries(errors).forEach(([field, message]) => {
    const li = document.createElement('li');
    li.textContent = message;
    list.appendChild(li);

    const input = document.querySelector(`[name="${field}"]`);
    input?.classList.add('field-error');
  });

  box.appendChild(list);
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
  fetch('PHP/csrf.php', { credentials: 'same-origin' })
    .then(res => res.json())
    .then(data => {
      document.getElementById('csrf_token').value = data.csrf_token;
      button.disabled = false;
    })
    .catch(() => console.error('CSRF-Token Fehler'));

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    clearFormMessage();
    setButtonLoading(button);

    try {
      const result = await submitForm('PHP/send.php', new FormData(form));

      if (!result || result.spam) {
        resetButton(button);
        return;
      }

      if (result.errors) {
        showValidationErrors(result.errors);
        focusFirstError(result.errors);
        resetButton(button);
        return;
      }

      if (result.success) {
        showFormMessage('Erfolgreich gesendet!', 'success');
        window.location.href = 'danke.html';
      }
    } catch {
      showFormMessage('Technischer Fehler. Bitte später erneut versuchen.');
    } finally {
      resetButton(button);
    }
  });

  form.addEventListener('input', (e) => {
    e.target.classList.remove('field-error');
  });
}
