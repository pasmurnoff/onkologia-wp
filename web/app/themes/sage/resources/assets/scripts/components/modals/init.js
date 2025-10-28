(() => {
  const OPEN_ATTR = 'data-modal-open';
  const CLOSE_ATTR = 'data-modal-close';
  const openers = document.querySelectorAll('[' + OPEN_ATTR + ']');
  const modals = new Map();      // id -> modal
  const lastFocus = new Map();   // id -> element

  // Собираем модалки по id из кнопок
  openers.forEach(function (btn) {
    const id = btn.getAttribute(OPEN_ATTR);
    const modal = document.getElementById('modal-' + id);
    if (modal) modals.set(id, modal);
  });

  // Фокусируемый контент
  function focusable(root) {
    return root.querySelectorAll(
      'a[href], button:not([disabled]), textarea, input, select, [tabindex]:not([tabindex="-1"])'
    );
  }

  // Блокируем/возвращаем скролл (и компенсация ширины скроллбара)
  function lockScroll(lock) {
    if (lock) {
      var sw = window.innerWidth - document.documentElement.clientWidth;
      document.body.style.setProperty('--scrollbar', sw + 'px');
      document.body.classList.add('modal-open');
    } else {
      document.body.classList.remove('modal-open');
      document.body.style.removeProperty('--scrollbar');
    }
  }

  // Открыть модалку
  function openModal(id, openerEl) {
    const modal = modals.get(id);
    if (!modal) return;

    // Закрываем другие, если открыты
    Array.prototype.forEach.call(
      document.querySelectorAll('.modal[aria-hidden="false"]'),
      function (m) { closeModal(m); }
    );

    lastFocus.set(id, openerEl || document.activeElement);
    modal.setAttribute('aria-hidden', 'false');
    lockScroll(true);

    // Фокус-трап
    const focusables = Array.prototype.slice.call(focusable(modal));
    (focusables[0] || modal).focus();

    function trap(e) {
      if (e.key === 'Escape') { closeModal(modal); return; }
      if (e.key !== 'Tab' || focusables.length === 0) return;

      const first = focusables[0];
      const last  = focusables[focusables.length - 1];

      if (e.shiftKey && document.activeElement === first) {
        last.focus(); e.preventDefault();
      } else if (!e.shiftKey && document.activeElement === last) {
        first.focus(); e.preventDefault();
      }
    }

    modal.addEventListener('keydown', trap);
    modal._trap = trap;
  }

  // Закрыть модалку
  function closeModal(modal) {
    if (!modal) return;
    modal.setAttribute('aria-hidden', 'true');
    if (modal._trap) modal.removeEventListener('keydown', modal._trap);
    lockScroll(false);

    // вернуть фокус
    const id = modal.id.replace('modal-', '');
    const opener = lastFocus.get(id);
    if (opener && document.body.contains(opener)) opener.focus();
  }

  // Делегирование: открыть/закрыть
  document.addEventListener('click', function (e) {
    var opener = e.target.closest('[' + OPEN_ATTR + ']');
    if (opener) {
      e.preventDefault();
      openModal(opener.getAttribute(OPEN_ATTR), opener);
      return;
    }
    if (e.target.closest('[' + CLOSE_ATTR + ']')) {
      var m = e.target.closest('.modal');
      closeModal(m);
    }
  });

  // Клик по фону закрывает
  Array.prototype.forEach.call(
    document.querySelectorAll('.modal__backdrop'),
    function (bg) {
      bg.addEventListener('click', function () {
        var m = bg.closest('.modal');
        closeModal(m);
      });
    }
  );

  // Инициализация: все модалки скрыты для ассистивок
  Array.prototype.forEach.call(
    document.querySelectorAll('.modal'),
    function (m) { m.setAttribute('aria-hidden', 'true'); }
  );
})();

// === Переключение между модалками (Контакты → Написать нам) ===

const optionConfig = {
  help: {
    title: 'Мне нужна помощь',
    placeholder: 'Какая помощь Вам нужна?',
  },
  donate: {
    title: 'Я хочу помочь',
    placeholder: 'Я хочу помочь',
  },
  other: {
    title: 'Другой вопрос',
    placeholder: 'Какой у Вас вопрос?',
  },
};

// Находим первую модалку
var contactModal = document.getElementById('modal-contact');
if (contactModal) {
  contactModal.addEventListener('click', function (e) {
    var item = e.target.closest('.contact-menu__element');
    if (!item) return;

    // Определяем, какая опция выбрана
    var titleNode = item.querySelector('.title span');
    var titleText = titleNode ? String(titleNode.textContent).trim() : '';
    var key = 'other';
    if (titleText.indexOf('нужна помощь') > -1) key = 'help';
    else if (titleText.indexOf('хочу помочь') > -1) key = 'donate';

    var config = optionConfig[key];
    var writeModalEl = document.getElementById('modal-writeus');
    if (!writeModalEl) return;

    // Меняем заголовок и плейсхолдер
    var titleEl = writeModalEl.querySelector('#modal-title');
    var textarea = writeModalEl.querySelector('.form_textarea');
    if (titleEl) titleEl.textContent = config.title;
    if (textarea) textarea.placeholder = config.placeholder;

    // Закрываем первую модалку и открываем вторую
    contactModal.setAttribute('aria-hidden', 'true');
    writeModalEl.setAttribute('aria-hidden', 'false');

    // Фокус на первое поле формы
    var firstInput = writeModalEl.querySelector('.form_text');
    if (firstInput) firstInput.focus();
  });
}

// === Кнопка "Назад" во второй модалке ===
var writeModal = document.getElementById('modal-writeus');
if (writeModal) {
  var backBtn = writeModal.querySelector('.modal__back');
  if (backBtn) {
    backBtn.addEventListener('click', function (e) {
      e.preventDefault();

      writeModal.setAttribute('aria-hidden', 'true');

      var contactModal2 = document.getElementById('modal-contact');
      if (contactModal2) contactModal2.setAttribute('aria-hidden', 'false');

      var firstOption = contactModal2 ? contactModal2.querySelector('.contact-menu__element') : null;
      if (firstOption) firstOption.focus();
    });
  }
}

// === Отправка формы из модалки "Написать нам" ===
var writeForm = document.getElementById('writeus-form');
if (writeForm) {
writeForm.addEventListener('submit', function (e) {
  e.preventDefault();

  var modal = document.getElementById('modal-writeus');
  var resultEl = modal ? modal.querySelector('.form-result') : null;
  var titleEl = modal ? modal.querySelector('#modal-title') : null;

  var subject = 'Сообщение с сайта';
  if (titleEl && typeof titleEl.textContent === 'string') {
    subject = titleEl.textContent.trim() || subject;
  }

  var formData = new FormData(writeForm);
  formData.append('subject', subject);

  if (resultEl) {
    resultEl.hidden = false;
    resultEl.innerHTML = '<p>Отправляем сообщение...</p>';
  }

  fetch('./sends.php', { method: 'POST', body: formData })
    .then(function (response) {
      if (!response.ok) throw new Error('Ошибка ответа сервера');

      // успех
      writeForm.hidden = true;
      if (writeForm.style) writeForm.style.setProperty('display', 'none', 'important');
      var mTitle = modal ? modal.querySelector('#m-title') : null;
      if (mTitle && mTitle.style) mTitle.style.display = 'none';
      if (resultEl) {
        resultEl.innerHTML =
          '<div class="success" style="display:flex;flex-direction:column;justify-items:center;">' +
          '<img src="./images/check.png" style="width:60px;margin-bottom:24px;align-self:center;">' +
          '<h3 style="font-size:20px;font-weight:600;margin-top:0;color:var(--black);text-align:center;margin-bottom:8px;">Ваша заявка отправлена!</h3>' +
          '<span style="color:var(--silver-grey);font-size:15px;font-weight:500;margin-bottom:36px;text-align:center;">Спасибо за заявку, мы с вами свяжемся!</span>' +
          '<a class="btn_primary" style="display:flex;justify-content:center;" href=" ">Хорошо</a>' +
          '</div>';
      }
    })
    .catch(function (err) {
      // ошибка
      writeForm.hidden = true;
      if (writeForm.style) writeForm.style.setProperty('display', 'none', 'important');
      var mTitle = modal ? modal.querySelector('#m-title') : null;
      if (mTitle && mTitle.style) mTitle.style.display = 'none';
      if (resultEl) {
        resultEl.innerHTML =
          '<div class="error">' +
          '<h3>❌  Не удалось отправить</h3>' +
          '<p>Попробуйте позже или свяжитесь с нами другим способом</p>' +
          '</div>';
      }
      console.error(err);
    });
});

}

(function () {
  var modal    = document.getElementById('modal-feedback');
  if (!modal) return;

  var form     = modal.querySelector('#feedback-form');
  var titleEl  = modal.querySelector('.js-modal-title, #modal-title');
  var resultEl = modal.querySelector('.form-feedback-result');

  if (!form) return;

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    if (resultEl) {
      resultEl.hidden = false;
      resultEl.innerHTML = '<p>Отправляем сообщение...</p>';
    }

    var data = new FormData(form);
    var subject = 'Отзыв';
    if (titleEl && typeof titleEl.textContent === 'string') {
      subject = (titleEl.textContent || 'Отзыв').trim();
    }
    data.append('subject', subject);

    fetch('/send-feedback.php', { method: 'POST', body: data })
      .then(function (res) {
        if (!res.ok) throw new Error('Server responded ' + res.status);

        if (form && form.style) form.style.display = 'none';
        if (titleEl && titleEl.style) titleEl.style.display = 'none';
        if (resultEl) {
          resultEl.innerHTML =
            '<div class="success" style="display:flex;flex-direction:column;justify-items:center;">' +
            '<img src="./images/check.png" style="width:60px;margin-bottom:24px;align-self:center;">' +
            '<h3 style="font-size:20px;font-weight:600;margin-top:0;color:var(--black);text-align:center;margin-bottom:8px;">Ваш отзыв отправлен</h3>' +
            '<span style="color:var(--silver-grey);font-size:15px;font-weight:500;margin-bottom:36px;text-align:center;">Мы опубликуем его после модерации!</span>' +
            '<a class="btn_primary" style="display:flex;justify-content:center;" href=" ">Хорошо</a>' +
            '</div>';
        }
      })
      .catch(function (err) {
        if (form && form.style) form.style.display = 'none';
        if (titleEl && titleEl.style) titleEl.style.display = 'none';
        if (resultEl) {
          resultEl.innerHTML =
            '<div class="error">' +
            '<h3>❌  Не удалось отправить</h3>' +
            '<p>Попробуйте позже или свяжитесь с нами другим способом</p>' +
            '</div>';
        }
        console.error(err);
      });
  });
})();

