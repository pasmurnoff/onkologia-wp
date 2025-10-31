(function () {
  const container = document.querySelector('.main-banner__text');
  if (!container) return;

  const slider = container.querySelector('.text-slider');
  const nav = container.querySelector('.text-slider__nav');
  if (!slider || !nav) return;

  const slides = Array.from(slider.querySelectorAll('.slide'));
  if (slides.length <= 1) return;

  const interval = Number(slider.dataset.interval) || 10000; // мс
  let index = 0;
  let timeout = null;

  // --- утилиты ---
  function setContainerHeight(el) {
    slider.style.height = el.offsetHeight + 'px';
  }

  function restartProgressAnim(el, durMs) {
    el.style.setProperty('--dur', (durMs / 1000) + 's');
    el.classList.remove('is-active');
    // force reflow
    void el.offsetWidth;
    el.classList.add('is-active');
  }

  function updateNav(durMs) {
    navItems.forEach((item, i) => {
      item.classList.toggle('is-active', i === index);
      item.classList.toggle('is-complete', i < index);
    });
    restartProgressAnim(navItems[index], durMs);
  }

  function show(i, durMs) {
    slides.forEach((s, idx) => s.classList.toggle('is-active', idx === i));
    requestAnimationFrame(() => setContainerHeight(slides[i]));
    updateNav(durMs);
  }

  function goTo(i) {
    index = i % slides.length;
    if (index < 0) index = slides.length - 1;
    show(index, interval);
  }

  // --- точный цикл без дрейфа ---
  function clearTimeoutSafe() {
    if (timeout) {
      clearTimeout(timeout);
      timeout = null;
    }
  }

  function schedule(ms) {
    clearTimeoutSafe();
    timeout = setTimeout(tick, ms);
  }

  function tick() {
    goTo(index + 1);
    schedule(interval);
  }

  function start() {
    goTo(index);
    schedule(interval);
  }

  // --- индикаторы ---
  const navItems = slides.map((_, i) => {
    const item = document.createElement('button');
    item.type = 'button';
    item.className = 'nav-item';
    item.setAttribute('aria-label', `Слайд ${i + 1}`);
    item.addEventListener('click', () => {
      if (i === index) return;
      clearTimeoutSafe();     // сбрасываем текущий таймер
      goTo(i);                // переходим к выбранному слайду
      schedule(interval);     // перезапускаем цикл
    });
    nav.appendChild(item);
    return item;
  });

  // --- запуск ---
  window.addEventListener('load', start);

  // --- пересчёт высоты ---
  window.addEventListener('resize', () => setContainerHeight(slides[index]));
  slider.querySelectorAll('img').forEach((img) => {
    if (img.complete) return;
    img.addEventListener('load', () => {
      if (slides[index].contains(img)) setContainerHeight(slides[index]);
    });
  });

  // никаких пауз, наблюдателей и т.п. — слайдер работает непрерывно
})();
