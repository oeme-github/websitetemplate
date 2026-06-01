'use strict';

const MAIN_JS = '../../public/assets/js/main.js';

beforeEach(() => {
  jest.resetModules();
  jest.useFakeTimers();
  document.body.innerHTML = `
    <form id="kontaktForm">
      <input id="iban" name="iban" type="text" value="">
      <span id="iban-status" class="iban-status" aria-live="polite"></span>
      <input id="bank" name="bank" type="text" value="">
    </form>
  `;
  global.fetch = jest.fn();
});

afterEach(() => {
  jest.useRealTimers();
});

function loadMain() {
  require(MAIN_JS);
}

function typeInIban(value) {
  const input = document.getElementById('iban');
  input.value = value;
  input.dispatchEvent(new Event('input', { bubbles: true }));
}

const VALID_IBAN = 'DE89370400440532013000';

describe('IBAN Live-Validierung', () => {
  test('returns early without throwing when #iban is absent', () => {
    document.body.innerHTML = '';
    expect(() => loadMain()).not.toThrow();
  });

  test('empty input clears status', () => {
    loadMain();
    typeInIban('');
    expect(document.getElementById('iban-status').textContent).toBe('');
    expect(document.getElementById('iban-status').className).toBe('iban-status');
  });

  test('invalid format shows error immediately without fetch', () => {
    loadMain();
    typeInIban('INVALID');
    expect(document.getElementById('iban-status').textContent).toBe('IBAN-Format ungültig');
    expect(document.getElementById('iban-status').classList.contains('is-invalid')).toBe(true);
    expect(fetch).not.toHaveBeenCalled();
  });

  test('valid IBAN does not call fetch before debounce fires', () => {
    global.fetch.mockResolvedValue({ json: async () => ({ ok: true, bank: 'Sparkasse' }) });
    loadMain();
    typeInIban(VALID_IBAN);
    expect(fetch).not.toHaveBeenCalled();
  });

  test('valid IBAN fires fetch after 600ms debounce', () => {
    global.fetch.mockResolvedValue({ json: async () => ({ ok: false, bank: null }) });
    loadMain();
    typeInIban(VALID_IBAN);
    jest.advanceTimersByTime(600);
    expect(fetch).toHaveBeenCalledWith(
      expect.stringContaining('iban_lookup.php'),
      expect.any(Object)
    );
  });

  test('shows loading state when debounce fires', () => {
    global.fetch.mockResolvedValue({ json: async () => ({ ok: false, bank: null }) });
    loadMain();
    typeInIban(VALID_IBAN);
    jest.advanceTimersByTime(600);
    expect(document.getElementById('iban-status').textContent).toBe('Prüfe…');
    expect(document.getElementById('iban-status').classList.contains('is-loading')).toBe(true);
  });

  test('fills bank field and shows bank name on successful lookup', async () => {
    global.fetch.mockResolvedValue({ json: async () => ({ ok: true, bank: 'Commerzbank' }) });
    loadMain();
    typeInIban(VALID_IBAN);
    jest.advanceTimersByTime(600);
    await Promise.resolve(); await Promise.resolve(); await Promise.resolve();
    expect(document.getElementById('bank').value).toBe('Commerzbank');
    expect(document.getElementById('iban-status').textContent).toBe('Commerzbank');
    expect(document.getElementById('iban-status').classList.contains('is-valid')).toBe(true);
  });

  test('shows fallback message when bank not found in response', async () => {
    global.fetch.mockResolvedValue({ json: async () => ({ ok: false, bank: null }) });
    loadMain();
    typeInIban(VALID_IBAN);
    jest.advanceTimersByTime(600);
    await Promise.resolve(); await Promise.resolve(); await Promise.resolve();
    expect(document.getElementById('iban-status').textContent).toBe('IBAN gültig, Bank nicht gefunden');
    expect(document.getElementById('iban-status').classList.contains('is-valid')).toBe(true);
  });

  test('shows error status when fetch fails', async () => {
    global.fetch.mockRejectedValue(new Error('Network error'));
    loadMain();
    typeInIban(VALID_IBAN);
    jest.advanceTimersByTime(600);
    await Promise.resolve(); await Promise.resolve(); await Promise.resolve();
    expect(document.getElementById('iban-status').textContent).toBe('Prüfung fehlgeschlagen');
    expect(document.getElementById('iban-status').classList.contains('is-invalid')).toBe(true);
  });

  test('debounce: rapid inputs result in only one fetch call', () => {
    global.fetch.mockResolvedValue({ json: async () => ({ ok: false, bank: null }) });
    loadMain();
    typeInIban('DE89');
    typeInIban('DE8937');
    typeInIban(VALID_IBAN);
    jest.advanceTimersByTime(600);
    expect(fetch).toHaveBeenCalledTimes(1);
  });

  test('normalizes IBAN: strips spaces and uppercases before fetch', () => {
    global.fetch.mockResolvedValue({ json: async () => ({ ok: false, bank: null }) });
    loadMain();
    typeInIban('de89 3704 0044 0532 0130 00');
    jest.advanceTimersByTime(600);
    expect(fetch).toHaveBeenCalledWith(
      expect.stringContaining('DE89370400440532013000'),
      expect.any(Object)
    );
  });

  test('does not fill bank field when lookup returns no bank', async () => {
    document.getElementById('bank').value = 'Manuell eingegeben';
    global.fetch.mockResolvedValue({ json: async () => ({ ok: false, bank: null }) });
    loadMain();
    typeInIban(VALID_IBAN);
    jest.advanceTimersByTime(600);
    await Promise.resolve(); await Promise.resolve(); await Promise.resolve();
    expect(document.getElementById('bank').value).toBe('Manuell eingegeben');
  });
});
