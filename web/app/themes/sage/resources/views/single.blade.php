{{-- resources/views/single.blade.php --}}
@extends('layouts.app')

@php
    // URL "назад": сперва реферер, если нет — страница записей, если нет — главная
    $back_url = wp_get_referer();
    if (!$back_url) {
        $posts_page_id = (int) get_option('page_for_posts');
        $back_url = $posts_page_id ? get_permalink($posts_page_id) : home_url('/');
    }

    // Картинка
    $thumb_html = has_post_thumbnail()
        ? get_the_post_thumbnail(null, 'large', ['class' => 'detail-page__image-el'])
        : '';

    // Дата и "сколько назад"
    use Carbon\Carbon;
    Carbon::setLocale('ru');
    setlocale(LC_TIME, 'ru_RU.UTF-8');
    $date_human = Carbon::parse($post->post_date)->formatLocalized('%e %B %Y');
    $ago = human_time_diff(get_the_time('U'), current_time('timestamp')) . ' назад';
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

            @if ($thumb_html)
                <div class="detail-page__image">
                    <a>
                        {!! $thumb_html !!}
                    </a>
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
