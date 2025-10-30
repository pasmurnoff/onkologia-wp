{{--
Template Name: О фонде
--}}
@extends('layouts.app')

@section('content')
    @include('components.modals.wrap')
    @include('components.main-banner.wrap')

    <div class="container_rg">
        <section class="about-section">
            <div class="about-section__title">
                <h2>Основные направления работы</h2>
            </div>
            <div class="about-section__content">
                @include('components.napravleniya.wrap')
            </div>

        </section>

        <section class="about-section partners">
            <div class="about-section__title partners__title">
                <h2>Партнеры фонда</h2>
                <div class="partners__controls">
                    <button class="partners__btn partners__btn--prev" type="button" aria-label="Назад" disabled>
                        <!-- иконка влево -->
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                            <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </button>
                    <button class="partners__btn partners__btn--next" type="button" aria-label="Вперёд">
                        <!-- иконка вправо -->
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                            <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="about-section__content">
                @include('components.partners.wrap')
            </div>

        </section>

        <section class="about-section">
            <div class="about-section__title">
                <h2>Наша команда</h2>
            </div>
            <div class="about-section__content">
                @include('components.team.wrap')
            </div>

        </section>

        <section class="about-section">
            <div class="about-section__title">
                <h2>«Решение Жить» – сообщество,<br>
                    где важен каждый человек</h2>
            </div>
            <div class="about-section__content">
                @include('components.feedback.wrap')
            </div>

        </section>

        <section class="about-section_divided">
            <div class="recviz">
                <div class="divided__title">
                    <h2>Реквизиты</h2>
                </div>
                <div class="recviz__wrap">
                    @include('components.recviz.wrap')
                </div>
            </div>

            @php
                // ID текущей страницы (важно объявить ДО вызовов get_field)
                $page_id = get_the_ID();

                // Хелпер "человекочитаемый" размер файла
                if (!function_exists('human_filesize')) {
                    function human_filesize($bytes)
                    {
                        if (function_exists('size_format')) {
                            return size_format($bytes);
                        } // WP helper
                        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
                        $i = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
                        return round($bytes / pow(1024, max($i, 1)), 0) . ($units[$i] ?? 'B');
                    }
                }

                // Соберём данные: список и архив (чтобы решить — показывать секцию или нет)
                $has_list = have_rows('fond_files', $page_id);
                $archive = get_field('fond_files_archive', $page_id);
                $has_archive = $archive && !empty($archive['url']);
            @endphp

            @if ($has_list || $has_archive)
                <div class="files">
                    <div class="divided__title">
                        <h2>Файлы</h2>
                    </div>

                    <div class="files__wrap">
                        <div class="files-card">

                            {{-- Элементы из репитера --}}
                            @if ($has_list)
                                @while (have_rows('fond_files', $page_id))
                                    @php the_row(); @endphp
                                    @php
                                        $f = get_sub_field('file');
                                        if (!$f) {
                                            continue;
                                        }

                                        $url = $f['url'] ?? '';
                                        $id = $f['ID'] ?? 0;
                                        $title = get_sub_field('name') ?: $f['title'] ?? basename($url);

                                        // Размер (если включено)
                                        $show_size = (bool) get_sub_field('show_size');
                                        $size_text = '';
                                        if ($show_size) {
                                            $bytes = $f['filesize'] ?? 0;
                                            if (!$bytes && $id) {
                                                $path = get_attached_file($id);
                                                if ($path && file_exists($path)) {
                                                    $bytes = filesize($path);
                                                }
                                            }
                                            if ($bytes) {
                                                $size_text = human_filesize((int) $bytes);
                                            }
                                        }
                                    @endphp

                                    <div class="files-card__item">
                                        <div class="files-card__left">
                                            <div class="files-card__icon">
                                                {{-- Твой SVG-значок файла --}}
                                                <svg width="40" height="46" viewBox="0 0 40 46" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M27.9568 2.88031C28.1839 2.9021 28.3976 2.99888 28.5604 3.15613L38.4813 12.7389C38.6672 12.9186 38.7715 13.1631 38.7715 13.4172V38.3336C38.7714 39.6042 38.2488 40.8231 37.3187 41.7216C36.3886 42.62 35.1268 43.1248 33.8115 43.1249H10.002C8.6865 43.1249 7.42399 42.62 6.49377 41.7216C5.56383 40.8231 5.04114 39.6041 5.04102 38.3336V7.66719C5.04102 6.39646 5.56367 5.17687 6.49377 4.27828C7.42401 3.37967 8.6864 2.87402 10.002 2.87402H27.8591L27.9568 2.88031ZM10.002 4.79219C9.21262 4.79219 8.45537 5.09486 7.89723 5.63402C7.33923 6.17317 7.02576 6.9048 7.02576 7.66719V38.3336C7.02589 39.0959 7.33919 39.8277 7.89723 40.3667C8.45534 40.9057 9.2128 41.2086 10.002 41.2086H33.8115C34.6004 41.2085 35.3573 40.9055 35.9153 40.3667C36.4733 39.8277 36.7875 39.0959 36.7877 38.3336V16.2922H29.842C28.5265 16.2922 27.264 15.7873 26.3338 14.8888C25.4037 13.9903 24.8812 12.7706 24.881 11.4999V4.79219H10.002ZM26.8658 11.4999C26.8659 12.2623 27.1792 12.994 27.7373 13.5331C28.2954 14.0721 29.0528 14.3749 29.842 14.3749H36.7877V13.8143L27.448 4.79219H26.8658V11.4999Z"
                                                        style="fill: var(--black);" fill-opacity="0.08"></path>
                                                    <rect x="1.63477" y="20.1113" width="29.6667" height="16"
                                                        rx="3.83333" style="fill: var(--red);"></rect>
                                                    <path
                                                        d=" M6.24724 32.1113V24.2123H9.20936C9.81618 24.2123 10.3253 24.3255 10.7367
                                                                                                    24.5517C11.1507 24.778 11.4631 25.0891 11.6739 25.4851C11.8874 25.8785 11.9941
                                                                                                    26.3259 11.9941 26.8273C11.9941 27.3339 11.8874 27.7839 11.6739 28.1773C11.4605
                                                                                                    28.5707 11.1455 28.8805 10.729 29.1068C10.3124 29.3305 9.79947 29.4423 9.19008
                                                                                                    29.4423H7.2269V28.266H8.99723C9.35207 28.266 9.64262 28.2043 9.8689
                                                                                                    28.0808C10.0952 27.9574 10.2623 27.7877 10.3703 27.5717C10.4809 27.3557 10.5361
                                                                                                    27.1076 10.5361 26.8273C10.5361 26.5471 10.4809 26.3002 10.3703 26.0868C10.2623
                                                                                                    25.8734 10.0939 25.7075 9.86504 25.5893C9.63877 25.4684 9.34692 25.408 8.98952
                                                                                                    25.408H7.67816V32.1113H6.24724ZM15.9137 32.1113H13.237V24.2123H15.9677C16.7519
                                                                                                    24.2123 17.4256 24.3705 17.9887 24.6867C18.5544 25.0004 18.9889 25.4517 19.2923
                                                                                                    26.0405C19.5957 26.6293 19.7475 27.3339 19.7475 28.1541C19.7475 28.9769 19.5945
                                                                                                    29.684 19.2885 30.2754C18.9851 30.8668 18.5467 31.3207 17.9733 31.6369C17.4024
                                                                                                    31.9532 16.7159 32.1113 15.9137 32.1113ZM14.6679 30.8733H15.8442C16.3945 30.8733
                                                                                                    16.8535 30.773 17.2212 30.5724C17.5889 30.3693 17.8653 30.0672 18.0504
                                                                                                    29.666C18.2355 29.2623 18.3281 28.7584 18.3281 28.1541C18.3281 27.5499 18.2355
                                                                                                    27.0485 18.0504 26.6499C17.8653 26.2488 17.5914 25.9492 17.2289 25.7513C16.8689
                                                                                                    25.5507 16.4215 25.4504 15.8867 25.4504H14.6679V30.8733ZM21.107
                                                                                                    32.1113V24.2123H26.1673V25.4118H22.5379V27.5563H25.8202V28.7558H22.5379V32.1113H21.107Z"
                                                        style="fill: #ffff;"></path>
                                                </svg>
                                            </div>
                                            <div class="files-card__info">
                                                <span class="files-card__name">{{ e($title) }}</span>
                                                @if ($size_text)
                                                    <span class="files-card__size">{{ e($size_text) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="files-card__action">
                                            <a href="{{ esc_url($url) }}" download="{{ esc_attr($title) }}">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M17.5 12.5V15.8333C17.5 16.2754 17.3244 16.6993 17.0118 17.0118C16.6993 17.3244 16.2754 17.5 15.8333 17.5H4.16667C3.72464 17.5 3.30072 17.3244 2.98816 17.0118C2.67559 16.6993 2.5 16.2754 2.5 15.8333V12.5M5.83333 8.33333L10 12.5M10 12.5L14.1667 8.33333M10 12.5V2.5"
                                                        style="stroke: var(--black);" stroke-width="1.67"
                                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                @endwhile
                            @endif

                            {{-- Кнопка "Скачать все файлы" --}}
                            @php
                                $archive_name = get_field('fond_files_archive_name', $page_id) ?: 'all';
                            @endphp
                            @if ($has_archive)
                                <a class="btn_secondary" href="{{ esc_url($archive['url']) }}"
                                    download="{{ esc_attr($archive_name) }}">
                                    Скачать все файлы
                                </a>
                            @endif

                        </div>
                    </div>
                </div>
            @endif

        </section>
    </div>
@endsection
