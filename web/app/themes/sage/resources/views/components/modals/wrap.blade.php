<div class="modal" id="modal-feedback" role="dialog" aria-modal="true" aria-hidden="true"
    aria-labelledby="modal-feedback-title">
    <div class="modal__backdrop" data-modal-close></div>
    <div class="modal__dialog_sm" role="document">
        <button class="modal__close" aria-label="Закрыть" data-modal-close> <svg width="24" height="24"
                viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19.2003 4.80005L4.80029 19.2M4.80029 4.80005L19.2003 19.2" style="stroke: var(--black);"
                    stroke-width="2.4048" stroke-linecap="round" stroke-linejoin="round" />
            </svg></button>
        <h2 id="modal-title">Отзыв</h2>
        <div class="modal__body">
            <form class="form" id="feedback-form">
                <input type="text" class="form_text" name="name" placeholder="ФИО" required>
                <input type="text" class="form_text" name="phone" placeholder="Телефон" required>
                <textarea class="form_textarea" name="message" placeholder="Напишите отзыв" rows="5" required></textarea>
                <div class="form-checkbox">
                    <input type="checkbox" required>
                    <label>Я согласен на обработку моих персональных данных. С <a href="">Политикой обработки
                            персональных данных</a>
                        ознакомлен.</label>
                </div>
                <button type="submit" class="btn_primary">Отправить</button>
            </form>
            <div class="form-feedback-result" hidden></div>
        </div>
    </div>
</div>


<div class="modal" id="modal-writeus" role="dialog" aria-modal="true" aria-hidden="true"
    aria-labelledby="modal-writeus-title">
    <div class="modal__backdrop" data-modal-close></div>
    <div class="modal__dialog_sm" role="document">
        <button class="modal__close" aria-label="Закрыть" data-modal-close> <svg width="24" height="24"
                viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19.2003 4.80005L4.80029 19.2M4.80029 4.80005L19.2003 19.2" style="stroke: var(--black);"
                    stroke-width="2.4048" stroke-linecap="round" stroke-linejoin="round" />
            </svg></button>
        <div class="modal-title-witharrow" id="m-title">
            <a class="modal__back">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 19L5 12M5 12L12 5M5 12H19" style="stroke: var(--black)" stroke-width="2.4048"
                        stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
            <h2 id="modal-title">Заголовок</h2>
        </div>
        <div class="modal__body">
            <form class="form" id="writeus-form">
                <input type="text" class="form_text" name="name" placeholder="ФИО" required>
                <input type="text" class="form_text" name="phone" placeholder="Телефон" required>
                <input type="text" class="form_text" name="email" placeholder="Эл. почта" required>
                <textarea class="form_textarea" name="message" placeholder="" rows="5" required></textarea>
                <div class="form-checkbox">
                    <input type="checkbox" required>
                    <label>Я согласен на обработку моих персональных данных. С <a href="">Политикой обработки
                            персональных данных</a>
                        ознакомлен.</label>
                </div>
                <button type="submit" class="btn_primary">Отправить</button>
            </form>

            <div class="form-result" hidden></div>
        </div>
    </div>
</div>

@php
    $page_id = isset($page_id) ? $page_id : get_the_ID();
    $qr_id = get_field('donate_qr', $page_id);
    $qr_caption = get_field('donate_qr_caption', $page_id);
@endphp

<div class="modal" id="modal-donate" role="dialog" aria-modal="true" aria-hidden="true"
    aria-labelledby="modal-donate-title">
    <div class="modal__backdrop" data-modal-close></div>
    <div class="modal__dialog_sm" role="document">
        <button class="modal__close" aria-label="Закрыть" data-modal-close> <svg width="24" height="24"
                viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19.2003 4.80005L4.80029 19.2M4.80029 4.80005L19.2003 19.2" style="stroke: var(--black);"
                    stroke-width="2.4048" stroke-linecap="round" stroke-linejoin="round" />
            </svg></button>
        <div class="modal__body">
            @if ($qr_id)
                <div class="modal__qr">
                    {!! wp_get_attachment_image($qr_id, 'large', false, [
                        'class' => 'modal__qr-image',
                        'loading' => 'lazy',
                        'decoding' => 'async',
                    ]) !!}
                    @if ($qr_caption)
                        <div class="modal__qr-caption">{{ $qr_caption }}</div>
                    @endif
                    @php $qr_full = wp_get_attachment_image_src($qr_id, 'full'); @endphp

                </div>
            @else
                <p>QR-код пока не загружен.</p>
            @endif
        </div>

    </div>
</div>

<div class="modal" id="modal-contact" role="dialog" aria-modal="true" aria-hidden="true"
    aria-labelledby="modal-contact-title">
    <div class="modal__backdrop" data-modal-close></div>
    <div class="modal__dialog_sm" role="document">
        <button class="modal__close" aria-label="Закрыть" data-modal-close> <svg width="24" height="24"
                viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19.2003 4.80005L4.80029 19.2M4.80029 4.80005L19.2003 19.2" style="stroke: var(--black);"
                    stroke-width="2.4048" stroke-linecap="round" stroke-linejoin="round" />
            </svg></button>
        <h2 id="modal-donate-title">Связаться</h2>
        <div class="modal__body">
            <div class="contact-menu">
                <div class="contact-menu__element">
                    <div class="left">
                        <div class="icon">

                            <svg width="22" height="20" viewBox="0 0 22 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M11 3C12.5 1.5 13.74 1 15.5 1C16.9587 1 18.3576 1.57946 19.3891 2.61091C20.4205 3.64236 21 5.04131 21 6.5C21 8.79 19.49 10.54 18 12L11 19L4 12C2.5 10.55 1 8.8 1 6.5C1 5.04131 1.57946 3.64236 2.61091 2.61091C3.64236 1.57946 5.04131 1 6.5 1C8.26 1 9.5 1.5 11 3ZM11 3L8.03998 5.96C7.8368 6.16171 7.67554 6.40162 7.5655 6.66593C7.45546 6.93023 7.3988 7.2137 7.3988 7.5C7.3988 7.7863 7.45546 8.06977 7.5655 8.33407C7.67554 8.59838 7.8368 8.83829 8.03998 9.04C8.85998 9.86 10.17 9.89 11.04 9.11L13.11 7.21C13.6288 6.73919 14.3044 6.47839 15.005 6.47839C15.7056 6.47839 16.3811 6.73919 16.9 7.21L19.86 9.87M17 13L15 11M14 16L12 14"
                                    style="stroke: var(--black);" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>

                        </div>
                        <div class="title">
                            <span>Мне нужна помощь</span>
                        </div>
                    </div>
                    <div class="action">

                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.5 15.0001L12.5 10.0001L7.5 5.00012" stroke-width="2.004"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>

                    </div>
                </div>
                <div class="contact-menu__element">
                    <div class="left">
                        <div class="icon">


                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M11 14H13C13.5304 14 14.0391 13.7893 14.4142 13.4142C14.7893 13.0391 15 12.5304 15 12C15 11.4696 14.7893 10.9609 14.4142 10.5858C14.0391 10.2107 13.5304 10 13 10H10C9.4 10 8.9 10.2 8.6 10.6L3 16M7 20L8.6 18.6C8.9 18.2 9.4 18 10 18H14C15.1 18 16.1 17.6 16.8 16.8L21.4 12.4C21.7859 12.0354 22.0111 11.5323 22.0261 11.0016C22.0411 10.4709 21.8447 9.95592 21.48 9.57003C21.1153 9.18414 20.6123 8.95892 20.0816 8.94392C19.5508 8.92891 19.0359 9.12535 18.65 9.49003L14.45 13.39M2 15L8 21M19.4999 8.5C20.1999 7.8 20.9999 6.9 20.9999 5.8C21.0698 5.18893 20.9313 4.57216 20.6068 4.04964C20.2824 3.52712 19.791 3.12947 19.2123 2.92114C18.6336 2.71281 18.0015 2.706 17.4184 2.90182C16.8354 3.09763 16.3355 3.4846 15.9999 4C15.6429 3.52458 15.1429 3.17613 14.5734 3.00578C14.0038 2.83544 13.3946 2.85219 12.8352 3.05356C12.2758 3.25494 11.7957 3.63034 11.4654 4.12465C11.1351 4.61896 10.9719 5.20614 10.9999 5.8C10.9999 7 11.7999 7.8 12.4999 8.6L15.9999 12L19.4999 8.5Z"
                                    style="stroke: var(--black);" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>


                        </div>
                        <div class="title">
                            <span>Я хочу помочь</span>
                        </div>
                    </div>
                    <div class="action">

                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.5 15.0001L12.5 10.0001L7.5 5.00012" stroke-width="2.004"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>

                    </div>
                </div>
                <div class="contact-menu__element">
                    <div class="left">
                        <div class="icon">


                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M7 7H17M17 7V17M17 7L7 17" style="stroke: var(--black);" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>


                        </div>
                        <div class="title">
                            <span>Другой вопрос</span>
                        </div>
                    </div>
                    <div class="action">

                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.5 15.0001L12.5 10.0001L7.5 5.00012" stroke-width="2.004"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="modal-history" role="dialog" aria-modal="true" aria-hidden="true"
    aria-labelledby="modal-history-title">
    <div class="modal__backdrop" data-modal-close></div>
    <div class="modal__dialog" role="document">
        <button class="modal__close" aria-label="Закрыть" data-modal-close>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path d="M19.2003 4.80005L4.80029 19.2M4.80029 4.80005L19.2003 19.2" style="stroke: var(--black);"
                    stroke-width="2.4048" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>
        <div class="modal__user">
            <div class="image">
                <img src="@asset('images/klim.png')">
            </div>
            <div class="info">
                <span class="name">Климентий Исмагилов</span>
                <span class="position">Президент благотворительного фонда «Решение Жить»</span>
            </div>
        </div>
        <div class="modal__longtext">
            <p>Дорогие друзья!</p>

            <p>Хочу сразу обозначить: я никого ни к чему не призываю. Всё, что здесь написано, — это сугубо мой опыт. Не
                надо его повторять! Обращайтесь к врачам и специалистам!</p>

            <p>Поскольку вы читаете этот текст, могу предположить, что вам небезразлична проблема онкологии. Возможно,
                вы только узнали свой диагноз и испытываете ужас, боль и беспомощность. Либо вы уже начали борьбу с этой
                болезнью и переживаете трудный период лечения. А может быть, страдает ваш близкий человек, и вам тяжело
                с этим справляться. Давайте поговорим об этом.</p>

            <p>Начну с собственной истории.</p>

            <p>Меня зовут Климентий, мне 36 лет. В 2023 году мне поставили диагноз — рак прямой кишки III стадии. Я
                прошёл 25 сеансов облучения и 5 курсов химиотерапии. После врачи настаивали на операции — резекция
                поражённого
                сегмента прямой кишки, но я отказался по личным причинам. Обычно при таких диагнозах удаляют кишку и
                человек может жить полноценной жизнью, но у меня опухоль находилась близко к выходу из прямой кишки, и
                после операции меня ожидала бы стома (искусственный вывод кишечника наружу навсегда). Врачи сказали, что
                без операции я проживу 3–4 года.</p>

            <p>После моего отказа все родные и близкие начали уговаривать меня передумать. Я получил столько звонков и
                внимания, сколько не получал даже в дни рождения. Но я стоял на своём.</p>

            <p>В тот момент я ощущал нечто необыкновенное — словно рядом было божественное присутствие. Я не боялся
                смерти. Мне приходило множество знаков. Например, один малознакомый человек подарил мне книгу
                «Перекрёсток», где главного героя звали Клим, и у него тоже был рак. В книге рассказывалось, как он
                встретился со своим ангелом хранителем, тот ему сказал со стороны как он живёт, Клим переосмыслил жизнь,
                исправил ошибки и обрел счастье. Это была моя история. Совпадение один на миллион, что мне попала в руки
                эта книга. Таких подобных знаков было много.</p>

            <p>Несмотря на прогнозы, я не отчаивался. Я искал альтернативные методы лечения, верил в исцеление — и оно
                пришло.</p>

            <p>Борьба с болезнью была тяжёлой: физически и эмоционально я чувствовал себя разбитым. Но чем больше я
                страдал, тем сильнее я становился. Сейчас я живу полной жизнью, наполненной смыслом, надеждой, радостью
                и любовью. Каждое утро я просыпаюсь с благодарностью, что сегодня смогу делать больше, чем вчера, и
                вечером благодарю за прожитый день.</p>

            <p>До болезни я не жил по-настоящему. Не было осознанности, понимания, что жизнь может оборваться в любой
                момент. Однажды, когда я ещё болел, я сидел в сквере, размышляя о жизни. Вдруг снова появилось то самое
                состояние — ощущение божественного присутствия. Мысль осенила: «Нужно создать фонд и помогать людям с
                таким же диагнозом». Мысли были ясными, без страха и сомнений. Я действовал сразу: нашёл юриста, собрал
                команду единомышленников.</p>

            <p>Сегодня в нашем фонде работают психологи, пастырь, бухгалтер, юрист и около 10 волонтёров. Все они
                помогают безвозмездно, от чистого сердца.</p>

            <p>Однажды я обратился к доброму и отзывчивому человеку с просьбой сделать сайт для фонда — и он взял на
                себя все расходы. Так появился этот крутой проект, который, я верю, поможет многим. Чудеса случаются, и
                я — тому пример.</p>

            <p>Желание помогать людям придаёт мне силы. Теперь я знаю, что живу не зря. Всё это помогло мне не только
                выздороветь, но и переосмыслить жизнь. Как ни странно, рак стал для меня наградой.</p>

            <p>Призываю вас не падать духом, бороться и верить. Важен ваш настрой, мысли и образ жизни. Нельзя винить
                Бога или судьбу — всё в наших руках. Я благодарен за всё, что со мной произошло.</p>

            <p>Вместе мы — СИЛА, дающая РЕШЕНИЕ ЖИТЬ и безусловно сможем справиться с трудностями.
                Желаю вам добра, света и здоровья.<br><br>

                С уважением,<br>
                Климентий</p>

        </div>
    </div>
</div>




<div class="modal" id="modal-feedback-item" role="dialog" aria-modal="true" aria-hidden="true"
    aria-labelledby="modal-feedback-item-title">
    <div class="modal__backdrop" data-modal-close></div>
    <div class="modal__dialog" role="document">
        <button class="modal__close" aria-label="Закрыть" data-modal-close> <svg width="24" height="24"
                viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19.2003 4.80005L4.80029 19.2M4.80029 4.80005L19.2003 19.2" style="stroke: var(--black);"
                    stroke-width="2.4048" stroke-linecap="round" stroke-linejoin="round" />
            </svg></button>

        <div class="modal__user js-modal-user"></div>
        <div class="modal__body js-modal-body"></div>
    </div>
</div>
