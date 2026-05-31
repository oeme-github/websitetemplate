'use strict';

const MAIN_JS = '../../public/assets/js/main.js';

function setupDOM() {
  document.body.innerHTML = `
    <span data-count-target="15" aria-hidden="true">0</span>
    <span data-count-target="200" aria-hidden="true">0</span>
  `;
}

function mockMatchMedia(prefersReducedMotion) {
  Object.defineProperty(window, 'matchMedia', {
    writable: true,
    configurable: true,
    value: jest.fn().mockImplementation(() => ({
      matches: prefersReducedMotion,
    })),
  });
}

beforeEach(() => {
  jest.resetModules();
  setupDOM();
  mockMatchMedia(false);
  delete window.IntersectionObserver;
  global.requestAnimationFrame = jest.fn().mockImplementation((cb) => {
    cb(performance.now() + 2100);
    return 1;
  });
});

describe('Stats counter', () => {
  test('shows final values immediately when prefers-reduced-motion is set', () => {
    mockMatchMedia(true);
    require(MAIN_JS);

    expect(document.querySelector('[data-count-target="15"]').textContent).toBe('15');
    expect(document.querySelector('[data-count-target="200"]').textContent).toBe('200');
  });

  test('shows final values immediately when IntersectionObserver is not supported', () => {
    require(MAIN_JS);

    expect(document.querySelector('[data-count-target="15"]').textContent).toBe('15');
    expect(document.querySelector('[data-count-target="200"]').textContent).toBe('200');
  });

  test('creates an IntersectionObserver when supported and motion is allowed', () => {
    global.IntersectionObserver = jest.fn().mockImplementation(() => ({
      observe: jest.fn(),
      unobserve: jest.fn(),
    }));

    require(MAIN_JS);

    expect(IntersectionObserver).toHaveBeenCalled();
  });

  test('starts counting when element intersects', () => {
    let capturedCallback;
    const observedElements = [];

    global.IntersectionObserver = jest.fn().mockImplementation((callback) => {
      capturedCallback = callback;
      return {
        observe: (el) => observedElements.push(el),
        unobserve: jest.fn(),
      };
    });

    require(MAIN_JS);

    const el = document.querySelector('[data-count-target="15"]');
    capturedCallback([{ isIntersecting: true, target: el }]);

    expect(el.textContent).toBe('15');
  });

  test('does not animate non-intersecting entries', () => {
    let capturedCallback;

    global.IntersectionObserver = jest.fn().mockImplementation((callback) => {
      capturedCallback = callback;
      return {
        observe: jest.fn(),
        unobserve: jest.fn(),
      };
    });

    require(MAIN_JS);

    const el = document.querySelector('[data-count-target="15"]');
    capturedCallback([{ isIntersecting: false, target: el }]);

    expect(el.textContent).toBe('0');
  });

  test('returns early without throwing when no count elements are present', () => {
    document.body.innerHTML = '';
    expect(() => require(MAIN_JS)).not.toThrow();
  });
});
