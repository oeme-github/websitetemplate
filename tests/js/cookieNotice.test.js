'use strict';

const MAIN_JS = '../../public/assets/js/main.js';

beforeEach(() => {
  jest.resetModules();
  localStorage.clear();
  document.body.innerHTML = `
    <aside class="cookie-notice">
      <button data-cookie-dismiss>Verstanden</button>
    </aside>
  `;
});

function loadMain() {
  require(MAIN_JS);
}

describe('Cookie notice – dismiss', () => {
  test('removes notice immediately when already dismissed in localStorage', () => {
    localStorage.setItem('cookieNoticeDismissed', '1');
    loadMain();
    expect(document.querySelector('.cookie-notice')).toBeNull();
  });

  test('keeps notice visible when not yet dismissed', () => {
    loadMain();
    expect(document.querySelector('.cookie-notice')).not.toBeNull();
  });

  test('adds is-hidden class and sets localStorage on button click', () => {
    loadMain();

    document.querySelector('[data-cookie-dismiss]').click();

    expect(document.querySelector('.cookie-notice').classList.contains('is-hidden')).toBe(true);
    expect(localStorage.getItem('cookieNoticeDismissed')).toBe('1');
  });

  test('removes notice from DOM after transitionend', () => {
    loadMain();

    document.querySelector('[data-cookie-dismiss]').click();

    const notice = document.querySelector('.cookie-notice');
    notice.dispatchEvent(new Event('transitionend'));

    expect(document.querySelector('.cookie-notice')).toBeNull();
  });

  test('returns early without throwing when elements are absent', () => {
    document.body.innerHTML = '';
    expect(() => loadMain()).not.toThrow();
  });
});
