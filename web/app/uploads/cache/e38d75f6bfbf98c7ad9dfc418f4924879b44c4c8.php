<section class="main-section">
    <div class="main-section__img">
        <svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect width="60" height="60" style="fill: var(--card)" />
            <path
                d="M29.9772 10.6724C19.258 10.6724 10.5737 19.3567 10.5737 30.0758C10.5737 40.795 19.258 49.4793 29.9772 49.4793C40.6963 49.4793 49.3806 40.795 49.3806 30.0758C49.3806 19.3567 40.6963 10.6724 29.9772 10.6724ZM29.9772 38.6875C25.2171 38.6875 21.3474 34.8177 21.3474 30.0577C21.3474 25.2976 25.2171 21.4278 29.9772 21.4278C34.7372 21.4278 38.607 25.2976 38.607 30.0577C38.607 34.8177 34.7372 38.6875 29.9772 38.6875Z"
                fill="#C9212B" />
        </svg>

    </div>
    <h3>Благотворительный фонд</h3>
    <h1>«Решение Жить»</h1>

    <div class="main-banner">
        <div class="main-banner__wrap">
            <div class="main-banner__picture">
                <img src="<?= App\asset_path('images/banner.png'); ?>">
            </div>
            <div class="main-banner__bottom">
                <div class="main-banner__text">
                    <div class="text-slider__nav" aria-label="Навигация по слайдам"></div>

                    <div class="text-slider" data-interval="10000">
                        <div class="slide is-active">
                            <span>Фонд оказывает всестороннюю поддержку людям, столкнувшимся с онкологическими
                                заболеваниями, а также их близким.</span>
                        </div>
                        <div class="slide">
                            <span>Фонд создан людьми, которые сами прошли через трудности онкодиагноза, поэтому
                                понимают, насколько важны участие и поддержка в такой ситуации.</span>
                        </div>
                        <div class="slide">
                            <span>Если вы или ваши близкие нуждаются в помощи - не стесняйтесь обратиться. Вместе можно
                                найти силы и ресурсы для борьбы с болезнью.</span>
                        </div>
                    </div>
                </div>
                <div class="main-banner__actions">
                    <div class="main-banner__buttons">
                        <a class="long" data-modal-open="contact">Связаться с нами</a>
                        <a class="icon" id="vk-icon" href="https://vk.com/resheniezhitkzn">
                            <svg width="38" height="24" viewBox="0 0 38 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M20.8213 23.4674C8.13773 23.4674 0.903245 14.772 0.601807 0.302979H6.95518C7.16387 10.9229 11.8477 15.4212 15.5577 16.3488V0.302979H21.5403V9.46204C25.2039 9.06785 29.0526 4.89412 30.3511 0.302979H36.3337C35.3366 5.96075 31.1628 10.1345 28.1948 11.8504C31.1628 13.2416 35.9166 16.8821 37.7252 23.4674H31.1397C29.7253 19.0617 26.201 15.6531 21.5403 15.1894V23.4674H20.8213Z"
                                    style="fill: #111111;" />
                            </svg>
                        </a>
                        <a class="icon" id="tg-icon" href="https://t.me/onkologia_forum">
                            <svg width="34" height="26" viewBox="0 0 34 26" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M2.52299 11.3315C11.5359 7.79737 17.5459 5.46748 20.553 4.34179C29.1389 1.12773 30.923 0.569414 32.0858 0.550978C32.3416 0.546923 32.9134 0.603968 33.2838 0.874482C33.5966 1.1029 33.6827 1.41146 33.7238 1.62802C33.765 1.84458 33.8163 2.33792 33.7755 2.7234C33.3103 7.12321 31.297 17.8004 30.2728 22.7282C29.8394 24.8134 28.9861 25.5125 28.1599 25.581C26.3645 25.7297 25.0012 24.5131 23.2623 23.4872C20.5412 21.8819 19.004 20.8825 16.3627 19.316C13.3102 17.5057 15.289 16.5107 17.0286 14.8845C17.4839 14.459 25.3944 7.98323 25.5475 7.39578C25.5667 7.32231 25.5844 7.04844 25.4037 6.90383C25.2229 6.75922 24.9561 6.80867 24.7635 6.848C24.4906 6.90375 20.1437 9.48961 11.7227 14.6056C10.4888 15.3681 9.37119 15.7397 8.36986 15.7202C7.26597 15.6987 5.14253 15.1585 3.56397 14.6966C1.62781 14.1302 0.0889805 13.8307 0.222978 12.8687C0.292772 12.3677 1.05944 11.8553 2.52299 11.3315Z"
                                    style="fill: #111111;" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="history-block">
        <div class="history-block__content">
            <div class="image">
                <img src="<?= App\asset_path('images/klim.png'); ?>">
            </div>

            <div class="content">
                <span class="name">Климентий Исмагилов</span>
                <span class="description">Президент благотворительного фонда <span>«Решение Жить»</span></span>
            </div>
        </div>
        <div class="history-block__action">
            <a data-modal-open="history" class="btn_secondary">Читать историю</a>
        </div>
    </div>
    </div>
</section>
