{{-- resources/views/single.blade.php --}}
@extends('layouts.app')

@php
    // URL "назад": сперва реферер, если нет — страница записей, если нет — главная
    $back_url = wp_get_referer();
    if (!$back_url) {
        $posts_page_id = (int) get_option('page_for_posts');
        $back_url = $posts_page_id ? get_permalink($posts_page_id) : home_url('/');
    }

    // --- Локализованная дата и "сколько назад" (WordPress-способ) ---
    $date_human = get_the_date('j F Y', $post);
    $ago = human_time_diff(get_the_time('U', $post), current_time('timestamp')) . ' назад';

    // --- ACF данные ---
    $vk_url = function_exists('get_field') ? (string) get_field('vk_video_url', $post->ID) : '';
    $gallery = function_exists('get_field') ? (array) (get_field('gallery', $post->ID) ?: []) : [];

    // --- Генерация HTML для слайдов ---
    $slides = [];

    // Слайд с видео VK — если есть
    if ($vk_url) {
        // сначала oEmbed
        $vk_embed = wp_oembed_get($vk_url);

        // если oEmbed не сработал — жёстко делаем правильный embed
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
        // $img — массив ACF изображения
        $url = isset($img['sizes']['large']) ? $img['sizes']['large'] : $img['url'];
        $alt = isset($img['alt']) ? $img['alt'] : (get_the_title($post) ?: '');
        $slides[] = [
            'type' => 'image',
            'html' => sprintf('<img src="%s" alt="%s" loading="lazy" />', esc_url($url), esc_attr($alt)),
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
                <h1>{{ get_the_title() }}</h1>

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
@endsection
