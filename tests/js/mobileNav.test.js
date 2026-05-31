'use strict';

const MAIN_JS = '../../public/assets/js/main.js';

beforeEach(() => {
  jest.resetModules();
  Object.defineProperty(window, 'scrollY', {
    writable: true,
    configurable: true,
    value: 0,
  });
  document.body.innerHTML = `
    <button data-nav-toggle aria-expanded="false"></button>
    <nav data-nav>
      <a href="#section">Section</a>
      <a href="/external">External</a>
    </nav>
  `;
});

function loadMain() {
  require(MAIN_JS);
}

describe('C) Mobile nav – toggle', () => {
  test('opens nav on click', () => {
    loadMain();
    const toggle = document.querySelector('[data-nav-toggle]');
    const nav = document.querySelector('[data-nav]');

    toggle.click();

    expect(nav.classList.contains('is-open')).toBe(true);
    expect(toggle.getAttribute('aria-expanded')).toBe('true');
  });

  test('closes open nav on second click', () => {
    loadMain();
    const toggle = document.querySelector('[data-nav-toggle]');
    const nav = document.querySelector('[data-nav]');
    nav.classList.add('is-open');
    toggle.setAttribute('aria-expanded', 'true');

    toggle.click();

    expect(nav.classList.contains('is-open')).toBe(false);
    expect(toggle.getAttribute('aria-expanded')).toBe('false');
  });

  test('returns early without throwing when elements are absent', () => {
    document.body.innerHTML = '';
    expect(() => loadMain()).not.toThrow();
  });
});

describe('C1) Mobile nav – close on link click', () => {
  test('closes nav when an anchor inside nav is clicked', () => {
    loadMain();
    const toggle = document.querySelector('[data-nav-toggle]');
    const nav = document.querySelector('[data-nav]');
    nav.classList.add('is-open');
    toggle.setAttribute('aria-expanded', 'true');

    nav.querySelector('a').click();

    expect(nav.classList.contains('is-open')).toBe(false);
    expect(toggle.getAttribute('aria-expanded')).toBe('false');
  });
});

describe('C2) Mobile nav – close on scroll down', () => {
  test('closes open nav when scrolling down more than 5 px', () => {
    window.scrollY = 0;
    loadMain();
    const toggle = document.querySelector('[data-nav-toggle]');
    const nav = document.querySelector('[data-nav]');
    nav.classList.add('is-open');

    window.scrollY = 10;
    window.dispatchEvent(new Event('scroll'));

    expect(nav.classList.contains('is-open')).toBe(false);
    expect(toggle.getAttribute('aria-expanded')).toBe('false');
  });

  test('does not close nav when scrolling up', () => {
    window.scrollY = 100;
    loadMain();
    const nav = document.querySelector('[data-nav]');
    nav.classList.add('is-open');

    window.scrollY = 80;
    window.dispatchEvent(new Event('scroll'));

    expect(nav.classList.contains('is-open')).toBe(true);
  });

  test('does not close nav when menu is already closed during scroll', () => {
    window.scrollY = 0;
    loadMain();
    const nav = document.querySelector('[data-nav]');

    window.scrollY = 100;
    window.dispatchEvent(new Event('scroll'));

    expect(nav.classList.contains('is-open')).toBe(false);
  });
});

describe('C3) Mobile nav – close on hashchange', () => {
  test('closes open nav on hashchange event', () => {
    loadMain();
    const toggle = document.querySelector('[data-nav-toggle]');
    const nav = document.querySelector('[data-nav]');
    nav.classList.add('is-open');

    window.dispatchEvent(new Event('hashchange'));

    expect(nav.classList.contains('is-open')).toBe(false);
    expect(toggle.getAttribute('aria-expanded')).toBe('false');
  });

  test('no-op on hashchange when nav is already closed', () => {
    loadMain();
    const nav = document.querySelector('[data-nav]');

    window.dispatchEvent(new Event('hashchange'));

    expect(nav.classList.contains('is-open')).toBe(false);
  });
});

describe('C4) Mobile nav – close on internal anchor click', () => {
  test('closes open nav when clicking a #anchor link', () => {
    loadMain();
    const nav = document.querySelector('[data-nav]');
    nav.classList.add('is-open');

    document.querySelector('a[href="#section"]').click();

    expect(nav.classList.contains('is-open')).toBe(false);
  });

  test('closes open nav when clicking a /#anchor link', () => {
    document.body.innerHTML += '<a href="/#home">Home</a>';
    loadMain();
    const nav = document.querySelector('[data-nav]');
    nav.classList.add('is-open');

    document.querySelector('a[href="/#home"]').click();

    expect(nav.classList.contains('is-open')).toBe(false);
  });

  test('does not close nav when clicking an external link outside nav', () => {
    document.body.innerHTML = `
      <button data-nav-toggle aria-expanded="false"></button>
      <nav data-nav><a href="#section">Section</a></nav>
      <a href="/external">External</a>
    `;
    loadMain();
    const nav = document.querySelector('[data-nav]');
    nav.classList.add('is-open');

    document.querySelector('a[href="/external"]').click();

    expect(nav.classList.contains('is-open')).toBe(true);
  });
});
