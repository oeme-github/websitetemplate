'use strict';

const FOUC_JS = '../../public/assets/js/fouc-prevention.js';

beforeEach(() => {
  jest.resetModules();
  localStorage.clear();
  document.documentElement.removeAttribute('data-theme');
  document.documentElement.removeAttribute('data-color-scheme');
  document.documentElement.removeAttribute('data-cookie-dismissed');
});

function loadFouc() {
  require(FOUC_JS);
}

describe('FOUC prevention', () => {
  test('sets data-theme from localStorage', () => {
    localStorage.setItem('theme', 'dark');
    loadFouc();
    expect(document.documentElement.getAttribute('data-theme')).toBe('dark');
  });

  test('sets data-color-scheme from localStorage', () => {
    localStorage.setItem('colorScheme', 'warm');
    loadFouc();
    expect(document.documentElement.getAttribute('data-color-scheme')).toBe('warm');
  });

  test('sets data-cookie-dismissed when cookie was previously dismissed', () => {
    localStorage.setItem('cookieNoticeDismissed', '1');
    loadFouc();
    expect(document.documentElement.getAttribute('data-cookie-dismissed')).toBe('true');
  });

  test('sets no attributes when localStorage is empty', () => {
    loadFouc();
    expect(document.documentElement.getAttribute('data-theme')).toBeNull();
    expect(document.documentElement.getAttribute('data-color-scheme')).toBeNull();
    expect(document.documentElement.getAttribute('data-cookie-dismissed')).toBeNull();
  });
});
