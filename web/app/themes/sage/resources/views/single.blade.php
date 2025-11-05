{{-- resources/views/single.blade.php --}}
@extends('layouts.app')

@php
    // --- URL "назад" ---
    $back_url = wp_get_referer();
    if (!$back_url) {
        $posts_page_id = (int) get_option('page_for_posts');
        $back_url = $posts_page_id ? get_permalink($posts_page_id) : home_url('/');
    }

    // --- Дата и "сколько назад" ---
    $date_human = get_the_date('j F Y');
    $ago = human_time_diff(get_the_time('U'), current_time('timestamp')) . ' назад';

    // --- Заголовок: берём СЫРОЙ из БД (без фильтров), затем декодируем сущности ---
    $post_id = get_queried_object_id() ?: get_the_ID();
    $title_raw_db = (string) get_post_field('post_title', $post_id, 'raw'); // без фильтров
    $title_decoded = html_entity_decode($title_raw_db, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // Не трогаем кавычки: если в БД заголовок == "", так и показываем две кавычки.
    // (Если хочешь фолбэк: if ($title_decoded === '') $title_decoded = 'Без названия';)

    // --- ACF данные ---
    $vk_url = function_exists('get_field') ? (string) get_field('vk_video_url', $post_id) : '';
    $gallery = function_exists('get_field') ? (array) (get_field('gallery', $post_id) ?: []) : [];

    // --- Генерация HTML для слайдов ---
    $slides = [];

    // Слайд с видео VK — если есть
    if ($vk_url) {
        $vk_embed = wp_oembed_get($vk_url);
        if (!$vk_embed && function_exists('mytheme_vk_video_embed_iframe')) {
            $vk_embed = mytheme_vk_video_embed_iframe($vk_url);
        }
        if ($vk_embed) {
            $slides[] = [
                'type' => 'video',
                'html' => $vk_embed,
            ];
        }
    }

    // Слайды с изображениями из галереи
    foreach ($gallery as $img) {
        $full = isset($img['url']) ? $img['url'] : '';
        $url = isset($img['sizes']['large']) ? $img['sizes']['large'] : $full;
        $alt = isset($img['alt']) && $img['alt'] !== '' ? $img['alt'] : $title_decoded;

        $w = isset($img['width']) ? (int) $img['width'] : 0;
        $h = isset($img['height']) ? (int) $img['height'] : 0;
        $lg_size = $w && $h ? $w . '-' . $h : '';

        $slides[] = [
            'type' => 'image',
            'html' => sprintf(
                '<a class="media-slider__image-link" href="%s" data-lg="1"%s data-sub-html="%s">
                    <img src="%s" alt="%s" loading="lazy">
                 </a>',
                esc_url($full),
                $lg_size ? ' data-lg-size="' . esc_attr($lg_size) . '"' : '',
                esc_attr($alt),
                esc_url($url),
                esc_attr($alt),
            ),
        ];
    }

    $has_slider = !empty($slides);
@endphp

@section('content')
    <section class="container_rg single-post">

        <a class="detail__back" href="{{ esc_url($back_url) }}">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"
                aria-hidden="true">
                <path d="M12.5 15L7.5 10L12.5 5" style="stroke: var(--silver-grey);" stroke-width="1.67"
                    stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            Назад
        </a>

        <div class="detail-page">
            @if ($has_slider)
                <div class="detail-page__image media-slider" data-autoplay="0" data-interval="8000">
                    <div class="media-slider__viewport">
                        <div class="media-slider__track">
                            @foreach ($slides as $i => $slide)
                                <div class="media-slider__slide" data-index="{{ $i }}">
                                    @if ($slide['type'] === 'video')
                                        <div class="media-slider__video">
                                            {!! $slide['html'] !!}
                                        </div>
                                    @else
                                        <div class="media-slider__image">
                                            {!! $slide['html'] !!}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <button class="media-slider__nav media-slider__nav--prev" type="button" aria-label="Назад">‹</button>
                    <button class="media-slider__nav media-slider__nav--next" type="button" aria-label="Вперёд">›</button>

                    @if (count($slides) > 1)
                        <div class="media-slider__dots">
                            @foreach ($slides as $i => $_)
                                <button class="media-slider__dot" type="button" aria-label="Слайд {{ $i + 1 }}"
                                    data-to="{{ $i }}"></button>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            <div class="detail-page__content">
                {{-- Заголовок из БД (raw) с декодированными сущностями. Поддерживает "" --}}
                <h1>{{ $title_decoded }}</h1>

                {{-- Основной контент записи --}}
                <div class="detail-page__text">
                    {!! apply_filters('the_content', get_the_content()) !!}
                </div>

                <div class="detail-page__bottom">
                    <div class="detail-page__divider"></div>

                    <div class="detail-page__postinfo">
                        <div class="detail-page__date">{{ $date_human }}</div>
                        <div class="detail-page__date">{{ $ago }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Инициализация lightGallery --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var track = document.querySelector('.media-slider .media-slider__track');
            if (!track || typeof window.lightGallery !== 'function') return;

            window.lgInstance = window.lightGallery(track, {
                selector: 'a[data-lg="1"]',
                plugins: [lgZoom, lgThumbnail],
                download: false,
                speed: 300,
                zoom: true,
                thumbnail: true,
                appendSubHtmlTo: '.lg-item',
                showCloseIcon: true,
                mobileSettings: {
                    controls: true,
                    showCloseIcon: true,
                    download: false
                }
            });
        });
    </script>
@endsection
