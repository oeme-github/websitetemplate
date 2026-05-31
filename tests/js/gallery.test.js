'use strict';

const MAIN_JS = '../../public/assets/js/main.js';

function buildDOM() {
  document.body.innerHTML = `
    <ul id="galleryGrid">
      <li><button data-gallery-index="0" data-gallery-src="/img/a.jpg" data-gallery-alt="Alpha">A</button></li>
      <li><button data-gallery-index="1" data-gallery-src="/img/b.jpg" data-gallery-alt="Beta">B</button></li>
      <li><button data-gallery-index="2" data-gallery-src="/img/c.jpg" data-gallery-alt="Gamma">C</button></li>
    </ul>
    <div id="galleryLightbox" class="lightbox" aria-hidden="true">
      <div class="lightbox-backdrop" data-lightbox-close></div>
      <div class="lightbox-dialog">
        <button class="lightbox-btn lightbox-btn--close" data-lightbox-close>&#x2715;</button>
        <button class="lightbox-btn lightbox-btn--prev" data-lightbox-prev>&#x2039;</button>
        <figure>
          <img id="lightboxImg" src="" alt="">
          <figcaption id="lightboxCaption"></figcaption>
        </figure>
        <button class="lightbox-btn lightbox-btn--next" data-lightbox-next>&#x203A;</button>
      </div>
    </div>
  `;
}

beforeEach(() => {
  jest.resetModules();
  buildDOM();
});

function loadMain() {
  require(MAIN_JS);
}

function clickThumb(index) {
  document.querySelector(`[data-gallery-index="${index}"]`).click();
}

function getGrid()     { return document.getElementById('galleryGrid'); }
function getLightbox() { return document.getElementById('galleryLightbox'); }
function getImg()      { return document.getElementById('lightboxImg'); }
function getCaption()  { return document.getElementById('lightboxCaption'); }
function getPrev()     { return document.querySelector('[data-lightbox-prev]'); }
function getNext()     { return document.querySelector('[data-lightbox-next]'); }
function getCloseBtn() { return document.querySelector('.lightbox-btn--close'); }
function getBackdrop() { return document.querySelector('.lightbox-backdrop'); }

describe('G) Gallery – early return', () => {
  test('returns early without throwing when galleryGrid is absent', () => {
    document.getElementById('galleryGrid').remove();
    expect(() => loadMain()).not.toThrow();
  });

  test('returns early without throwing when galleryLightbox is absent', () => {
    document.getElementById('galleryLightbox').remove();
    expect(() => loadMain()).not.toThrow();
  });
});

describe('G) Gallery – thumbnail click', () => {
  test('opens lightbox on thumbnail click', () => {
    loadMain();
    clickThumb(0);
    expect(getLightbox().classList.contains('is-open')).toBe(true);
    expect(getLightbox().getAttribute('aria-hidden')).toBe('false');
  });

  test('sets correct src, alt and caption for clicked image', () => {
    loadMain();
    clickThumb(1);
    expect(getImg().src).toContain('/img/b.jpg');
    expect(getImg().alt).toBe('Beta');
    expect(getCaption().textContent).toBe('Beta');
  });

  test('sets aria-hidden on grid when lightbox opens', () => {
    loadMain();
    clickThumb(0);
    expect(getGrid().getAttribute('aria-hidden')).toBe('true');
  });
});

describe('G) Gallery – close', () => {
  test('close button closes lightbox', () => {
    loadMain();
    clickThumb(0);
    getCloseBtn().click();
    expect(getLightbox().classList.contains('is-open')).toBe(false);
    expect(getLightbox().getAttribute('aria-hidden')).toBe('true');
  });

  test('backdrop click closes lightbox', () => {
    loadMain();
    clickThumb(0);
    getBackdrop().click();
    expect(getLightbox().classList.contains('is-open')).toBe(false);
  });

  test('Escape key closes lightbox', () => {
    loadMain();
    clickThumb(0);
    document.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape' }));
    expect(getLightbox().classList.contains('is-open')).toBe(false);
  });

  test('Escape key does nothing when lightbox is already closed', () => {
    loadMain();
    expect(() => {
      document.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape' }));
    }).not.toThrow();
    expect(getLightbox().classList.contains('is-open')).toBe(false);
  });

  test('close removes aria-hidden from grid', () => {
    loadMain();
    clickThumb(0);
    getCloseBtn().click();
    expect(getGrid().hasAttribute('aria-hidden')).toBe(false);
  });

  test('focus returns to trigger element after close', () => {
    loadMain();
    const btn = document.querySelector('[data-gallery-index="1"]');
    btn.click();
    getCloseBtn().click();
    expect(document.activeElement).toBe(btn);
  });
});

describe('G) Gallery – navigation', () => {
  test('next button advances to next image', () => {
    loadMain();
    clickThumb(0);
    getNext().click();
    expect(getImg().src).toContain('/img/b.jpg');
    expect(getCaption().textContent).toBe('Beta');
  });

  test('prev button goes back to previous image', () => {
    loadMain();
    clickThumb(1);
    getPrev().click();
    expect(getImg().src).toContain('/img/a.jpg');
    expect(getCaption().textContent).toBe('Alpha');
  });

  test('ArrowRight key navigates to next image', () => {
    loadMain();
    clickThumb(0);
    document.dispatchEvent(new KeyboardEvent('keydown', { key: 'ArrowRight' }));
    expect(getImg().src).toContain('/img/b.jpg');
  });

  test('ArrowLeft key navigates to previous image', () => {
    loadMain();
    clickThumb(2);
    document.dispatchEvent(new KeyboardEvent('keydown', { key: 'ArrowLeft' }));
    expect(getImg().src).toContain('/img/b.jpg');
  });

  test('prev button is disabled on first image', () => {
    loadMain();
    clickThumb(0);
    expect(getPrev().disabled).toBe(true);
    expect(getNext().disabled).toBe(false);
  });

  test('next button is disabled on last image', () => {
    loadMain();
    clickThumb(2);
    expect(getNext().disabled).toBe(true);
    expect(getPrev().disabled).toBe(false);
  });

  test('ArrowLeft does nothing at first image', () => {
    loadMain();
    clickThumb(0);
    document.dispatchEvent(new KeyboardEvent('keydown', { key: 'ArrowLeft' }));
    expect(getImg().src).toContain('/img/a.jpg');
  });

  test('ArrowRight does nothing at last image', () => {
    loadMain();
    clickThumb(2);
    document.dispatchEvent(new KeyboardEvent('keydown', { key: 'ArrowRight' }));
    expect(getImg().src).toContain('/img/c.jpg');
  });
});

describe('G) Gallery – index ordering', () => {
  test('items are sorted by data-gallery-index regardless of DOM order', () => {
    document.body.innerHTML = `
      <ul id="galleryGrid">
        <li><button data-gallery-index="2" data-gallery-src="/img/c.jpg" data-gallery-alt="Gamma">C</button></li>
        <li><button data-gallery-index="0" data-gallery-src="/img/a.jpg" data-gallery-alt="Alpha">A</button></li>
        <li><button data-gallery-index="1" data-gallery-src="/img/b.jpg" data-gallery-alt="Beta">B</button></li>
      </ul>
      <div id="galleryLightbox" class="lightbox" aria-hidden="true">
        <div class="lightbox-backdrop" data-lightbox-close></div>
        <div class="lightbox-dialog">
          <button class="lightbox-btn lightbox-btn--close" data-lightbox-close>&#x2715;</button>
          <button class="lightbox-btn lightbox-btn--prev" data-lightbox-prev>&#x2039;</button>
          <figure>
            <img id="lightboxImg" src="" alt="">
            <figcaption id="lightboxCaption"></figcaption>
          </figure>
          <button class="lightbox-btn lightbox-btn--next" data-lightbox-next>&#x203A;</button>
        </div>
      </div>
    `;
    loadMain();
    document.querySelector('[data-gallery-index="0"]').click();
    getNext().click();
    expect(getImg().src).toContain('/img/b.jpg');
    expect(getCaption().textContent).toBe('Beta');
  });
});

function buildDOMWithCategories(cats) {
  var items = cats.map(function (cat, i) {
    return `<li class="gallery-item" data-gallery-category="${cat}">` +
      `<button data-gallery-index="${i}" data-gallery-src="/img/${i}.jpg" data-gallery-alt="Image ${i}">Img ${i}</button>` +
      `</li>`;
  }).join('');
  document.body.innerHTML = `<ul id="galleryGrid">${items}</ul>`;
}

describe('H) Gallery Tabs – early return', () => {
  test('no tabs rendered when gallery has no .gallery-item elements', () => {
    loadMain();
    expect(document.querySelector('.gallery-tabs')).toBeNull();
  });

  test('no tabs rendered when all items share one category', () => {
    buildDOMWithCategories(['Kategorie A', 'Kategorie A', 'Kategorie A']);
    loadMain();
    expect(document.querySelector('.gallery-tabs')).toBeNull();
  });
});

describe('H) Gallery Tabs – tab bar rendering', () => {
  beforeEach(() => {
    buildDOMWithCategories(['Kategorie A', 'Kategorie B', 'Kategorie A', 'Kategorie C']);
  });

  test('inserts .gallery-tabs immediately before the grid', () => {
    loadMain();
    const tabs = document.querySelector('.gallery-tabs');
    expect(tabs).not.toBeNull();
    expect(tabs.nextElementSibling).toBe(document.getElementById('galleryGrid'));
  });

  test('first tab is "Alle" with aria-pressed="true" and empty filter', () => {
    loadMain();
    const buttons = document.querySelectorAll('.gallery-tab');
    expect(buttons[0].textContent).toBe('Alle');
    expect(buttons[0].getAttribute('aria-pressed')).toBe('true');
    expect(buttons[0].dataset.galleryFilter).toBe('');
  });

  test('renders unique categories in order of first appearance', () => {
    loadMain();
    const buttons = document.querySelectorAll('.gallery-tab');
    expect(buttons[1].textContent).toBe('Kategorie A');
    expect(buttons[2].textContent).toBe('Kategorie B');
    expect(buttons[3].textContent).toBe('Kategorie C');
  });
});

describe('H) Gallery Tabs – filtering', () => {
  beforeEach(() => {
    buildDOMWithCategories(['Kategorie A', 'Kategorie B', 'Kategorie A']);
  });

  test('clicking a category tab hides non-matching items', () => {
    loadMain();
    const katBTab = Array.from(document.querySelectorAll('.gallery-tab'))
      .find(b => b.textContent === 'Kategorie B');
    katBTab.click();
    const items = document.querySelectorAll('.gallery-item');
    expect(items[0].hidden).toBe(true);
    expect(items[1].hidden).toBe(false);
    expect(items[2].hidden).toBe(true);
  });

  test('clicking "Alle" shows all items', () => {
    loadMain();
    const tabs = document.querySelectorAll('.gallery-tab');
    tabs[2].click();
    tabs[0].click();
    document.querySelectorAll('.gallery-item').forEach(item => {
      expect(item.hidden).toBe(false);
    });
  });

  test('clicking a tab sets its aria-pressed="true" and others to "false"', () => {
    loadMain();
    const tabs = Array.from(document.querySelectorAll('.gallery-tab'));
    tabs[1].click();
    expect(tabs[0].getAttribute('aria-pressed')).toBe('false');
    expect(tabs[1].getAttribute('aria-pressed')).toBe('true');
    expect(tabs[2].getAttribute('aria-pressed')).toBe('false');
  });
});
