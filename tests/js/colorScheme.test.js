'use strict';

const MAIN_JS = '../../public/assets/js/main.js';

beforeEach(() => {
  jest.resetModules();
  localStorage.clear();
  document.documentElement.removeAttribute('data-color-scheme');
  document.body.innerHTML = `
    <select data-color-scheme-select>
      <option value="default">Default</option>
      <option value="warm">Warm</option>
      <option value="nature">Nature</option>
    </select>
  `;
});

function loadMain() {
  require(MAIN_JS);
}

describe('Color scheme selector', () => {
  test('defaults to default when no scheme is stored', () => {
    loadMain();
    expect(document.documentElement.getAttribute('data-color-scheme')).toBe('default');
  });

  test('applies a valid stored scheme on load', () => {
    localStorage.setItem('colorScheme', 'warm');
    loadMain();
    expect(document.documentElement.getAttribute('data-color-scheme')).toBe('warm');
  });

  test('applies all three valid schemes from localStorage', () => {
    ['default', 'warm', 'nature'].forEach((scheme) => {
      jest.resetModules();
      localStorage.setItem('colorScheme', scheme);
      document.documentElement.removeAttribute('data-color-scheme');
      require(MAIN_JS);
      expect(document.documentElement.getAttribute('data-color-scheme')).toBe(scheme);
    });
  });

  test('falls back to default for an invalid stored scheme', () => {
    localStorage.setItem('colorScheme', 'invalid');
    loadMain();
    expect(document.documentElement.getAttribute('data-color-scheme')).toBe('default');
  });

  test('updates scheme and localStorage on select change', () => {
    loadMain();

    const sel = document.querySelector('[data-color-scheme-select]');
    sel.value = 'nature';
    sel.dispatchEvent(new Event('change'));

    expect(document.documentElement.getAttribute('data-color-scheme')).toBe('nature');
    expect(localStorage.getItem('colorScheme')).toBe('nature');
  });

  test('ignores change event with invalid value', () => {
    loadMain();

    const sel = document.querySelector('[data-color-scheme-select]');
    sel.value = 'invalid';
    sel.dispatchEvent(new Event('change'));

    expect(document.documentElement.getAttribute('data-color-scheme')).toBe('default');
  });

  test('returns early without throwing when selector element is absent', () => {
    document.body.innerHTML = '';
    expect(() => loadMain()).not.toThrow();
  });
});
