// resources/assets/scripts/media-slider.js
(function () {
  function initSlider(root) {
    const track = root.querySelector('.media-slider__track');
    const slides = Array.from(root.querySelectorAll('.media-slider__slide'));
    const prev = root.querySelector('.media-slider__nav--prev');
    const next = root.querySelector('.media-slider__nav--next');
    const dotsWrap = root.querySelector('.media-slider__dots');
    const dots = dotsWrap ? Array.from(dotsWrap.querySelectorAll('.media-slider__dot')) : [];
    const autoplay = Number(root.dataset.autoplay) === 1;
    const interval = Number(root.dataset.interval) || 8000;

    let index = 0;
    let timer = null;

    function setHeights() {
      // авто-высота под содержимое активного слайда
      const active = slides[index];
      if (!active) return;
      const viewport = root.querySelector('.media-slider__viewport');
      viewport.style.height = active.offsetHeight + 'px';
    }

    function go(to) {
      index = (to + slides.length) % slides.length;
      const offset = -index * 100;
      track.style.transform = `translateX(${offset}%)`;
      slides.forEach((s, i) => s.classList.toggle('is-active', i === index));
      dots.forEach((d, i) => d.classList.toggle('is-active', i === index));
      setHeights();
      restart();
    }

    function nextSlide() { go(index + 1); }
    function prevSlide() { go(index - 1); }

    function restart() {
      if (!autoplay || slides.length < 2) return;
      clearTimeout(timer);
      timer = setTimeout(nextSlide, interval);
    }

    // Пересчёт высоты на загрузке медиа
    const imgs = root.querySelectorAll('img');
    imgs.forEach(img => {
      if (img.complete) setHeights();
      else img.addEventListener('load', setHeights, { once: true });
    });

    // iframe может менять высоту — делаем небольшой поллинг при старте
    setTimeout(setHeights, 50);
    setTimeout(setHeights, 300);
    setTimeout(setHeights, 1000);

    // Навигация
    if (next) next.addEventListener('click', nextSlide);
    if (prev) prev.addEventListener('click', prevSlide);
    if (dots.length) {
      dots.forEach((d) => {
        d.addEventListener('click', () => go(Number(d.dataset.to) || 0));
      });
    }

    // свайпы
    let startX = 0;
    let dx = 0;
    const viewport = root.querySelector('.media-slider__viewport');

    function onStart(e) {
      startX = e.touches ? e.touches[0].clientX : e.clientX;
      dx = 0;
      track.classList.add('is-dragging');
    }
    function onMove(e) {
      if (!startX) return;
      const x = e.touches ? e.touches[0].clientX : e.clientX;
      dx = x - startX;
      const percent = (-index * 100) + (dx / viewport.offsetWidth) * 100;
      track.style.transform = `translateX(${percent}%)`;
    }
    function onEnd() {
      track.classList.remove('is-dragging');
      if (Math.abs(dx) > viewport.offsetWidth * 0.2) {
        dx < 0 ? nextSlide() : prevSlide();
      } else {
        go(index);
      }
      startX = 0;
      dx = 0;
    }

    viewport.addEventListener('mousedown', onStart);
    viewport.addEventListener('mousemove', onMove);
    viewport.addEventListener('mouseleave', onEnd);
    viewport.addEventListener('mouseup', onEnd);
    viewport.addEventListener('touchstart', onStart, { passive: true });
    viewport.addEventListener('touchmove', onMove, { passive: true });
    viewport.addEventListener('touchend', onEnd);

    // старт
    go(0);
    window.addEventListener('resize', setHeights);
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.media-slider').forEach(initSlider);
  });
})();
