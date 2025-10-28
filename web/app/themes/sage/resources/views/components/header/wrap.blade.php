<header class="header">
    <div class="container">
        <div class="header__inner">
            <div class="header__logo">
                <a href="/">

                    <img src="@asset('images/logo.svg')" alt="logo" class="logo logo--light">
                    <img src="@asset('images/logo-dark.svg')" alt="logo" class="logo logo--dark">
                </a>
            </div>
            <div class="header__menu">
                <nav class="menu">
                    @if (has_nav_menu('primary'))
                        {!! wp_nav_menu([
                            'theme_location' => 'primary',
                            'container' => false,
                            'menu_class' => 'menu__list',
                            'depth' => 1,
                            'echo' => false,
                        ]) !!}
                    @else
                        {{-- Опционально: фоллбек, пока не назначили меню --}}
                        <ul class="menu__list">
                            <li class="menu__item"><a class="menu__link" href="{{ home_url('/') }}">Главная</a></li>
                        </ul>
                    @endif

                </nav>
            </div>
            <div class="header__actions">

                <a href="#" class="header__hamburger">

                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M3.33331 10H16.6666M3.33331 5H16.6666M3.33331 15H16.6666" style="stroke: var(--black);"
                            stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>

                </a>

                <nav class="mobile-menu">
                    <ul>
                        <li><a href="https://forum.onkologia.ru/">Форум</a></li>
                        <li><a href="/fond">Фонд</a></li>
                        <li><a href="/o-nas">О нас</a></li>
                        <li><a href="#" class="theme-toggle-mobile">

                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M9 1.5C8.00544 2.49456 7.4467 3.84348 7.4467 5.25C7.4467 6.65652 8.00544 8.00544 9 9C9.99456 9.99456 11.3435 10.5533 12.75 10.5533C14.1565 10.5533 15.5054 9.99456 16.5 9C16.5 10.4834 16.0601 11.9334 15.236 13.1668C14.4119 14.4001 13.2406 15.3614 11.8701 15.9291C10.4997 16.4968 8.99168 16.6453 7.53683 16.3559C6.08197 16.0665 4.7456 15.3522 3.6967 14.3033C2.64781 13.2544 1.9335 11.918 1.64411 10.4632C1.35472 9.00832 1.50325 7.50032 2.07091 6.12987C2.63856 4.75943 3.59986 3.58809 4.83323 2.76398C6.0666 1.93987 7.51664 1.5 9 1.5Z"
                                        style="stroke: var(--black)" stroke-width="1.67" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                                <span class="theme-toggle-text">Тёмная тема</span>


                            </a></li>
                    </ul>
                </nav>

                <a href="#" class="header__change-theme">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M10 2.5C9.00544 3.49456 8.4467 4.84348 8.4467 6.25C8.4467 7.65652 9.00544 9.00544 10 10C10.9946 10.9946 12.3435 11.5533 13.75 11.5533C15.1565 11.5533 16.5054 10.9946 17.5 10C17.5 11.4834 17.0601 12.9334 16.236 14.1668C15.4119 15.4001 14.2406 16.3614 12.8701 16.9291C11.4997 17.4968 9.99168 17.6453 8.53683 17.3559C7.08197 17.0665 5.7456 16.3522 4.6967 15.3033C3.64781 14.2544 2.9335 12.918 2.64411 11.4632C2.35472 10.0083 2.50325 8.50032 3.07091 7.12987C3.63856 5.75943 4.59986 4.58809 5.83323 3.76398C7.0666 2.93987 8.51664 2.5 10 2.5Z"
                            style="stroke: var(--black);" stroke-width="1.67" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </a>
                <a href="https://forum.onkologia.ru/login" class="header__login">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12.5 2.5H15.8333C16.2754 2.5 16.6993 2.67559 17.0118 2.98816C17.3244 3.30072 17.5 3.72464 17.5 4.16667V15.8333C17.5 16.2754 17.3244 16.6993 17.0118 17.0118C16.6993 17.3244 16.2754 17.5 15.8333 17.5H12.5M8.33333 14.1667L12.5 10M12.5 10L8.33333 5.83333M12.5 10H2.5"
                            style="stroke: var(--pill-color);" stroke-width="1.67" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>

                    Войти</a>
            </div>
        </div>
</header>
