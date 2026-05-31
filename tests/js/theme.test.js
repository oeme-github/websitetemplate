'use strict';

const MAIN_JS = '../../public/assets/js/main.js';

beforeEach(() => {
  jest.resetModules();
  localStorage.clear();
  document.documentElement.removeAttribute('data-theme');
  document.body.innerHTML = '<button data-theme-toggle></button>';
});

function loadMain() {
  require(MAIN_JS);
}

describe('Theme toggle', () => {
  test('applies stored dark theme from localStorage on load', () => {
    localStorage.setItem('theme', 'dark');
    loadMain();
    expect(document.documentElement.getAttribute('data-theme')).toBe('dark');
  });

  test('applies stored light theme from localStorage on load', () => {
    localStorage.setItem('theme', 'light');
    loadMain();
    expect(document.documentElement.getAttribute('data-theme')).toBe('light');
  });

  test('does not set data-theme when localStorage is empty', () => {
    loadMain();
    expect(document.documentElement.getAttribute('data-theme')).toBeNull();
  });

  test('toggles from light to dark on click', () => {
    document.documentElement.setAttribute('data-theme', 'light');
    loadMain();

    document.querySelector('[data-theme-toggle]').click();

    expect(document.documentElement.getAttribute('data-theme')).toBe('dark');
  });

  test('toggles from dark to light on click', () => {
    document.documentElement.setAttribute('data-theme', 'dark');
    loadMain();

    document.querySelector('[data-theme-toggle]').click();

    expect(document.documentElement.getAttribute('data-theme')).toBe('light');
  });

  test('persists new theme to localStorage on click', () => {
    document.documentElement.setAttribute('data-theme', 'light');
    loadMain();

    document.querySelector('[data-theme-toggle]').click();

    expect(localStorage.getItem('theme')).toBe('dark');
  });

  test('returns early without throwing when toggle element is absent', () => {
    document.body.innerHTML = '';
    expect(() => loadMain()).not.toThrow();
  });
});
