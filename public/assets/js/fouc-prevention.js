/**
 * FOUC Prevention
 * Applies saved theme and color scheme before page render.
 * Must load synchronously in <head> (no defer/async).
 */
(function() {
  var theme = localStorage.getItem('theme');
  var colorScheme = localStorage.getItem('colorScheme');
  var cookieDismissed = localStorage.getItem('cookieNoticeDismissed');
  if (theme) document.documentElement.setAttribute('data-theme', theme);
  if (colorScheme) document.documentElement.setAttribute('data-color-scheme', colorScheme);
  if (cookieDismissed) document.documentElement.setAttribute('data-cookie-dismissed', 'true');
})();
