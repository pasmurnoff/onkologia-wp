// resources/assets/scripts/feedback-modal.js
(() => {
  const MODAL_ID = 'modal-feedback-item';
  let lastActiveEl = null;

  const getModal = () => document.getElementById(MODAL_ID);
  const $ = (sel, root = document) => root.querySelector(sel);

  function lockScroll() {
    document.documentElement.classList.add('no-scroll');
  }
  function unlockScroll() {
    document.documentElement.classList.remove('no-scroll');
  }

  function openModal(modal) {
    if (!modal) return;
    lastActiveEl = document.activeElement;
    modal.setAttribute('aria-hidden', 'false');
    modal.classList.add('is-open');
    lockScroll();

    // Фокус на кнопку закрытия
    const closeBtn = $('[data-modal-close]', modal);
    if (closeBtn) closeBtn.focus();
  }

  function closeModal(modal) {
    if (!modal) return;
    modal.setAttribute('aria-hidden', 'true');
    modal.classList.remove('is-open');
    unlockScroll();

    // Возвращаем фокус
    if (lastActiveEl && typeof lastActiveEl.focus === 'function') {
      lastActiveEl.focus();
    }
  }

  // Вставка разметки отзыва в модалку
  function fillModalFromTemplate(postId) {
    const tpl = document.getElementById('tmpl-feedback-item-' + postId);
    const modal = getModal();
    if (!tpl || !modal) return;

    const userWrap = $('.js-modal-user', modal);
    const bodyWrap = $('.js-modal-body', modal);
    if (!userWrap || !bodyWrap) return;

    // Читаем HTML из <script type="text/template">
    const html = (tpl.textContent || '').trim();
    if (!html) {
      userWrap.innerHTML = '';
      bodyWrap.innerHTML = '';
      return;
    }

    const tmp = document.createElement('div');
    tmp.innerHTML = html;

    const userBlock = $('.modal__user', tmp);
    const bodyBlock = $('.modal__body', tmp);

    userWrap.innerHTML = userBlock ? userBlock.outerHTML : '';
    bodyWrap.innerHTML = bodyBlock ? bodyBlock.outerHTML : '';
  }

  // Открытие по клику на кнопку
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.js-open-feedback');
    if (!btn) return;

    e.preventDefault();
    const id = btn.dataset.id;
    if (!id) return;

    fillModalFromTemplate(id);
    openModal(getModal());
  });

  // Закрытие по крестику/бекдропу
  document.addEventListener('click', (e) => {
    const target = e.target;
    const isClose = target.matches('[data-modal-close]') || target.closest('[data-modal-close]');
    const isBackdrop = target.classList && target.classList.contains('modal__backdrop');

    if (!isClose && !isBackdrop) return;

    const modal = target.closest('.modal');
    if (modal && modal.id === MODAL_ID) {
      e.preventDefault();
      closeModal(modal);
    }
  });

  // Закрытие по ESC
  document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    const modal = getModal();
    if (modal && modal.classList.contains('is-open')) {
      e.preventDefault();
      closeModal(modal);
    }
  });
})();
