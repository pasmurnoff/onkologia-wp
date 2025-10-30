(function () {
  const THEME_KEY = 'theme';
  const root = document.documentElement;

  const headerToggle = document.querySelector('.header__change-theme');
  const mobileToggle = document.querySelector('.theme-toggle-mobile');
  const mobileText = mobileToggle ? mobileToggle.querySelector('.theme-toggle-text') : null;

  const mobileMenu = document.querySelector('.mobile-menu');
  const burgerBtn = document.querySelector('.header__hamburger');

  // инициализация темы
  const saved = localStorage.getItem(THEME_KEY);
  if (saved === 'dark' || saved === 'light') {
    root.setAttribute('data-theme', saved);
  } else {
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    root.setAttribute('data-theme', prefersDark ? 'dark' : 'light');
  }

  function updateMobileText() {
    if (!mobileText) return;
    const isDark = root.getAttribute('data-theme') === 'dark';
    mobileText.textContent = isDark ? 'Светлая тема' : 'Тёмная тема';
  }

  updateMobileText();

  function toggleTheme(e) {
    if (e) e.preventDefault();
    const current = root.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
    const next = current === 'dark' ? 'light' : 'dark';
    root.setAttribute('data-theme', next);
    localStorage.setItem(THEME_KEY, next);
    updateMobileText();

    // закрыть мобильное меню при клике внутри него
    if (mobileMenu && mobileMenu.classList.contains('active')) {
      mobileMenu.classList.remove('active');
      if (burgerBtn) burgerBtn.classList.remove('active');
    }
  }

  if (headerToggle) headerToggle.addEventListener('click', toggleTheme);
  if (mobileToggle) mobileToggle.addEventListener('click', toggleTheme);

  const mq = window.matchMedia('(prefers-color-scheme: dark)');
  if (mq && typeof mq.addEventListener === 'function') {
    mq.addEventListener('change', (e) => {
      const explicit = localStorage.getItem(THEME_KEY);
      if (!explicit) {
        root.setAttribute('data-theme', e.matches ? 'dark' : 'light');
        updateMobileText();
      }
    });
  } else if (mq && typeof mq.addListener === 'function') {
    // для старых Safari
    mq.addListener((e) => {
      const explicit = localStorage.getItem(THEME_KEY);
      if (!explicit) {
        root.setAttribute('data-theme', e.matches ? 'dark' : 'light');
        updateMobileText();
      }
    });
  }

  // burger
  if (burgerBtn) {
    const menu = document.querySelector('.mobile-menu');
    burgerBtn.addEventListener('click', function (e) {
      e.preventDefault();
      if (menu) menu.classList.toggle('active');
      burgerBtn.classList.toggle('active');
    });
  }

  // sticky header
  document.addEventListener('scroll', function () {
    const header = document.querySelector('.header');
    if (!header) return;
    if (window.scrollY > 0) header.classList.add('active');
    else header.classList.remove('active');
  });

  // humanize tags
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.tag-pill').forEach((el) => {
      el.textContent = el.textContent.replace(/-/g, ' ');
    });
  });

  // copy buttons
  document.querySelectorAll('.value').forEach((valueEl) => {
    const btn = document.createElement('button');
    btn.className = 'copy-btn';
    btn.setAttribute('aria-label', 'Скопировать');
    btn.innerHTML =
      '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>';
    valueEl.appendChild(btn);

    btn.addEventListener('click', (e) => {
      const text = (valueEl.childNodes[0] && valueEl.childNodes[0].textContent || '').trim();
      navigator.clipboard.writeText(text).then(() => {
        btn.textContent = 'Скопировано!';
        setTimeout(() => {
          btn.innerHTML =
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>';
        }, 1200);
      });
      e.stopPropagation();
    });
  });

  function setQuoteCardPosition() {
    if (window.innerWidth > 380) return;
    document.querySelectorAll('.team__element').forEach((el) => {
      const rect = el.getBoundingClientRect();
      const windowCenter = window.innerWidth / 2;
      const quote = el.querySelector('.quote-card');
      if (!quote) return;
      if (rect.left < windowCenter) {
        quote.classList.add('quote-right');
        quote.classList.remove('quote-left');
      } else {
        quote.classList.add('quote-left');
        quote.classList.remove('quote-right');
      }
    });
  }

  window.addEventListener('load', setQuoteCardPosition);
  window.addEventListener('resize', setQuoteCardPosition);
})();

document.addEventListener('DOMContentLoaded', () => {
  if (!document.body.classList.contains('blog')) return;

  const wrapper = document.querySelector('.wrapper');
  const footer = document.querySelector('.footer');

  if (wrapper && footer) {
    const divFlex = document.createElement('div');
    divFlex.classList.add('div-flex');

    const parent = wrapper.parentNode;
    parent.insertBefore(divFlex, wrapper);

    divFlex.appendChild(wrapper);
    divFlex.appendChild(footer);
  }
});
