'use strict';

const MAIN_JS = '../../public/assets/js/main.js';

beforeEach(() => {
  jest.resetModules();
  document.body.innerHTML = '<header class="site-header"></header>';
  Object.defineProperty(window, 'scrollY', {
    writable: true,
    configurable: true,
    value: 0,
  });
});

function loadMain() {
  require(MAIN_JS);
}

describe('Header – scroll behaviour', () => {
  test('hides header when scrolling down past 100 px with delta > threshold', () => {
    window.scrollY = 0;
    loadMain();

    window.scrollY = 150;
    window.dispatchEvent(new Event('scroll'));

    expect(document.querySelector('.site-header').classList.contains('is-hidden')).toBe(true);
  });

  test('reveals header when scrolling up', () => {
    window.scrollY = 200;
    loadMain();

    const header = document.querySelector('.site-header');
    header.classList.add('is-hidden');

    window.scrollY = 150;
    window.dispatchEvent(new Event('scroll'));

    expect(header.classList.contains('is-hidden')).toBe(false);
  });

  test('ignores scroll delta below threshold (10 px)', () => {
    window.scrollY = 200;
    loadMain();

    window.scrollY = 205;
    window.dispatchEvent(new Event('scroll'));

    expect(document.querySelector('.site-header').classList.contains('is-hidden')).toBe(false);
  });

  test('does not hide header when scrollY is at or below 100', () => {
    window.scrollY = 0;
    loadMain();

    window.scrollY = 50;
    window.dispatchEvent(new Event('scroll'));

    expect(document.querySelector('.site-header').classList.contains('is-hidden')).toBe(false);
  });

  test('returns early without throwing when header element is absent', () => {
    document.body.innerHTML = '';
    expect(() => loadMain()).not.toThrow();
  });
});
