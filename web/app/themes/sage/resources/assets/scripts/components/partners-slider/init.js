// partners-slider.js — листаем по 1 карточке, активен только ≥1024px
(function () {
  const root = document.querySelector('.partners');
  if (!root) return;

  const viewport = root.querySelector('.partners__viewport'); // СКРОЛЛИМ ЕГО
  const track = root.querySelector('.partners__track');
  const prevBtn = root.querySelector('.partners__btn--prev');
  const nextBtn = root.querySelector('.partners__btn--next');
  if (!viewport || !track || !prevBtn || !nextBtn) return;

  const GAP = parseFloat(getComputedStyle(track).gap) || 12;
  const isDesktop = () => window.innerWidth >= 1024;

  // ширина одной карточки + gap
  function getStep() {
    const card = track.querySelector('.partners__item');
    if (!card) return viewport.clientWidth; // fallback
    return card.getBoundingClientRect().width + GAP;
  }

  function maxScrollLeft() {
    // скроллим viewport, поэтому считаем от него
    return viewport.scrollWidth - viewport.clientWidth;
  }

  function clamp(v, min, max) {
    return Math.min(Math.max(v, min), max);
  }

  function scrollByOne(dir = 1) {
    const step = getStep();
    const target = clamp(viewport.scrollLeft + dir * step, 0, maxScrollLeft());
    viewport.scrollTo({ left: target, behavior: 'smooth' });
  }

  function updateButtons() {
    if (!isDesktop()) {
      prevBtn.disabled = true;
      nextBtn.disabled = true;
      return;
    }
    const sl = viewport.scrollLeft;
    const max = maxScrollLeft();
    const EPS = 2;
    prevBtn.disabled = sl <= EPS;
    nextBtn.disabled = sl >= (max - EPS);
  }

  function onPrev() { scrollByOne(-1); }
  function onNext() { scrollByOne(1); }

  // включение/выключение слайдера по брейкпоинту
  let active = false;

  function enable() {
    if (active) return;
    active = true;
    prevBtn.addEventListener('click', onPrev);
    nextBtn.addEventListener('click', onNext);
    viewport.addEventListener('scroll', updateButtons, { passive: true });
    updateButtons();
  }

  function disable() {
    if (!active) return;
    active = false;
    prevBtn.removeEventListener('click', onPrev);
    nextBtn.removeEventListener('click', onNext);
    viewport.removeEventListener('scroll', updateButtons);
    prevBtn.disabled = true;
    nextBtn.disabled = true;
  }

  function handleResize() {
    if (isDesktop()) enable(); else disable();
  }

  window.addEventListener('resize', () => {
    // небольшой debounce не обязателен, но полезен
    clearTimeout(handleResize._t);
    handleResize._t = setTimeout(() => {
      updateButtons(); // пересчитать края
      handleResize();
    }, 100);
  });

  // init
  handleResize();
  // на всякий случай перерасчёт после загрузки шрифтов/SVG
  window.addEventListener('load', updateButtons);
})();
