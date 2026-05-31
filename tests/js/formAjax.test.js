'use strict';

const MAIN_JS = '../../public/assets/js/main.js';

const flushPromises = () => new Promise((resolve) => setTimeout(resolve, 0));

beforeEach(() => {
  jest.resetModules();
  document.body.innerHTML = `
    <form id="kontaktForm" action="/send_kontakt.php">
      <input name="_csrf" value="token123">
      <input name="vorname" value="">
      <input name="email" value="">
      <button type="submit">Absenden</button>
    </form>
    <div id="formMessage" tabindex="-1"></div>
  `;
  global.fetch = jest.fn();
});

function loadMain() {
  require(MAIN_JS);
}

function submitForm() {
  document
    .getElementById('kontaktForm')
    .dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
}

describe('Form AJAX', () => {
  test('sends a POST request to the form action on submit', async () => {
    global.fetch.mockResolvedValue({
      ok: true,
      json: async () => ({ ok: true, message: 'Nachricht gesendet.' }),
    });

    loadMain();
    submitForm();
    await flushPromises();

    expect(fetch).toHaveBeenCalledWith(
      expect.stringContaining('send_kontakt.php'),
      expect.objectContaining({ method: 'POST' })
    );
  });

  test('shows success message and adds is-ok class on successful response', async () => {
    global.fetch.mockResolvedValue({
      ok: true,
      json: async () => ({ ok: true, message: 'Nachricht gesendet.' }),
    });

    loadMain();
    submitForm();
    await flushPromises();

    const msg = document.getElementById('formMessage');
    expect(msg.textContent).toBe('Nachricht gesendet.');
    expect(msg.classList.contains('is-ok')).toBe(true);
  });

  test('updates CSRF token when response includes a new token', async () => {
    global.fetch.mockResolvedValue({
      ok: true,
      json: async () => ({ ok: true, message: 'OK', csrf: 'newtoken456' }),
    });

    loadMain();
    submitForm();
    await flushPromises();

    expect(
      document.querySelector('input[name="_csrf"]').value
    ).toBe('newtoken456');
  });

  test('shows field-level errors and summary message on validation failure', async () => {
    global.fetch.mockResolvedValue({
      ok: false,
      json: async () => ({
        ok: false,
        code: 'VALIDATION',
        message: 'Bitte Eingaben prüfen.',
        errors: { vorname: 'Vorname fehlt', email: 'E-Mail ungültig' },
      }),
    });

    loadMain();
    submitForm();
    await flushPromises();

    const vornameInput = document.querySelector('[name="vorname"]');
    expect(vornameInput.getAttribute('aria-invalid')).toBe('true');
    expect(vornameInput.getAttribute('aria-describedby')).toBe('vorname-error');
    expect(document.getElementById('vorname-error').textContent).toBe('Vorname fehlt');
    expect(vornameInput.classList.contains('field-error')).toBe(true);

    const emailInput = document.querySelector('[name="email"]');
    expect(emailInput.getAttribute('aria-invalid')).toBe('true');
    expect(document.getElementById('email-error').textContent).toBe('E-Mail ungültig');

    const msg = document.getElementById('formMessage');
    expect(msg.textContent).toBe('Bitte Eingaben prüfen.');
    expect(msg.classList.contains('is-error')).toBe(true);
  });

  test('focuses first errored field on validation failure', async () => {
    global.fetch.mockResolvedValue({
      ok: false,
      json: async () => ({
        ok: false,
        message: 'Bitte Eingaben prüfen.',
        errors: { vorname: 'Vorname fehlt' },
      }),
    });

    loadMain();
    submitForm();
    await flushPromises();

    expect(document.activeElement).toBe(document.querySelector('[name="vorname"]'));
  });

  test('clears field errors on subsequent submit', async () => {
    global.fetch
      .mockResolvedValueOnce({
        ok: false,
        json: async () => ({
          ok: false,
          message: 'Bitte Eingaben prüfen.',
          errors: { vorname: 'Vorname fehlt' },
        }),
      })
      .mockResolvedValueOnce({
        ok: true,
        json: async () => ({ ok: true, message: 'Gesendet.' }),
      });

    loadMain();
    submitForm();
    await flushPromises();

    expect(document.getElementById('vorname-error')).not.toBeNull();
    expect(document.querySelector('[name="vorname"]').getAttribute('aria-invalid')).toBe('true');

    submitForm();
    await flushPromises();

    expect(document.getElementById('vorname-error')).toBeNull();
    expect(document.querySelector('[name="vorname"]').getAttribute('aria-invalid')).toBeNull();
  });

  test('ignores unknown field names in errors object gracefully', async () => {
    global.fetch.mockResolvedValue({
      ok: false,
      json: async () => ({
        ok: false,
        message: 'Bitte Eingaben prüfen.',
        errors: { nonexistent: 'Fehler' },
      }),
    });

    loadMain();
    expect(() => submitForm()).not.toThrow();
    await flushPromises();

    expect(document.querySelectorAll('.field-error-msg').length).toBe(0);
  });

  test('shows generic error message from response message field', async () => {
    global.fetch.mockResolvedValue({
      ok: false,
      json: async () => ({ ok: false, message: 'Serverfehler.' }),
    });

    loadMain();
    submitForm();
    await flushPromises();

    const msg = document.getElementById('formMessage');
    expect(msg.textContent).toBe('Serverfehler.');
    expect(msg.classList.contains('is-error')).toBe(true);
  });

  test('shows network error message when fetch rejects', async () => {
    global.fetch.mockRejectedValue(new Error('Network failure'));

    loadMain();
    submitForm();
    await flushPromises();

    const msg = document.getElementById('formMessage');
    expect(msg.textContent).toContain('Netzwerkfehler');
    expect(msg.classList.contains('is-error')).toBe(true);
  });

  test('re-enables submit button and resets label after submission', async () => {
    global.fetch.mockResolvedValue({
      ok: true,
      json: async () => ({ ok: true, message: 'OK' }),
    });

    loadMain();
    submitForm();
    await flushPromises();

    const btn = document.querySelector('button[type="submit"]');
    expect(btn.hasAttribute('disabled')).toBe(false);
    expect(btn.textContent).toBe('Absenden');
  });

  test('returns early without throwing when form element is absent', () => {
    document.body.innerHTML = '';
    expect(() => loadMain()).not.toThrow();
  });
});

describe('Success message fade-out', () => {
  beforeEach(() => {
    jest.useFakeTimers();
  });

  afterEach(() => {
    jest.useRealTimers();
  });

  test('adds is-fading class after 5 seconds on success', async () => {
    global.fetch.mockResolvedValue({
      ok: true,
      json: async () => ({ ok: true, message: 'Nachricht gesendet.' }),
    });

    loadMain();
    submitForm();
    await Promise.resolve();
    await Promise.resolve();
    await Promise.resolve();

    const msg = document.getElementById('formMessage');
    expect(msg.classList.contains('is-fading')).toBe(false);

    jest.advanceTimersByTime(5000);
    expect(msg.classList.contains('is-fading')).toBe(true);
  });

  test('does not add is-fading for error messages', async () => {
    global.fetch.mockResolvedValue({
      ok: false,
      json: async () => ({ ok: false, message: 'Fehler.' }),
    });

    loadMain();
    submitForm();
    await Promise.resolve();
    await Promise.resolve();
    await Promise.resolve();

    jest.advanceTimersByTime(5000);

    const msg = document.getElementById('formMessage');
    expect(msg.classList.contains('is-fading')).toBe(false);
  });

  test('transitionend on is-fading element clears text and resets class', () => {
    loadMain();
    const msg = document.getElementById('formMessage');
    msg.textContent = 'Nachricht gesendet.';
    msg.className = 'form-message is-ok is-fading';
    msg.dispatchEvent(new Event('transitionend'));

    expect(msg.textContent).toBe('');
    expect(msg.className).toBe('form-message');
  });

  test('transitionend without is-fading does not clear the message', () => {
    loadMain();
    const msg = document.getElementById('formMessage');
    msg.textContent = 'Fehler.';
    msg.className = 'form-message is-error';
    msg.dispatchEvent(new Event('transitionend'));

    expect(msg.textContent).toBe('Fehler.');
    expect(msg.className).toBe('form-message is-error');
  });
});
