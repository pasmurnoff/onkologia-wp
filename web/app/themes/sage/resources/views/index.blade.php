{{-- resources/views/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <section class="events container_rg">
        @php
            $posts_page_id = get_option('page_for_posts');
            $page_title = $posts_page_id ? get_the_title($posts_page_id) : 'События';

            $paged = get_query_var('paged') ? (int) get_query_var('paged') : 1;

            $events_query = new WP_Query([
                'post_type' => 'post',
                'category_name' => 'sobytiya',
                'posts_per_page' => 9,
                'paged' => $paged,
                'ignore_sticky_posts' => true,
            ]);
        @endphp

        <h1 class="page-title">{{ $page_title }} фонда</h1>

        @includeIf('components.follow-us-events.wrap')

        @if ($events_query->have_posts())
            <div class="events-cards">
                @while ($events_query->have_posts())
                    @php $events_query->the_post(); @endphp

                    @php
                        // --- Обложка ---
                        if (function_exists('mytheme_get_cover_image_html')) {
                            $thumb_html = mytheme_get_cover_image_html(get_the_ID(), 'medium_large', [
                                'class' => 'events-cards__image',
                            ]);
                        } else {
                            $thumb_html = has_post_thumbnail()
                                ? get_the_post_thumbnail(null, 'medium_large', ['class' => 'events-cards__image'])
                                : '';
                        }

                        // --- Дата ---
                        $date_human = get_the_date('j F Y');
                        $ago = human_time_diff(get_the_time('U'), current_time('timestamp')) . ' назад';

                        // --- ТЕКСТЫ БЕЗ ДВОЙНОГО ЭКРАНИРОВАНИЯ ---

                        // Заголовок
                        $title_raw = get_the_title();
                        $title = html_entity_decode($title_raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');

                        // Анонс
                        $excerpt_raw = get_the_excerpt();
                        if (!$excerpt_raw) {
                            $excerpt_raw = get_the_content();
                        }

                        // декодируем HTML-сущности, чистим HTML и пробелы
                        $excerpt_decoded = html_entity_decode($excerpt_raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                        $excerpt_clean = wp_strip_all_tags($excerpt_decoded);
                        $excerpt_clean = trim(preg_replace('/\s+/', ' ', $excerpt_clean));

                        // ограничение длины
                        $limit = 120;
                        if (mb_strlen($excerpt_clean, 'UTF-8') > $limit) {
                            $excerpt_clean = mb_substr($excerpt_clean, 0, $limit, 'UTF-8') . '…';
                        }
                    @endphp

                    <article @php post_class('events-cards__element') @endphp>
                        <a href="{{ get_permalink() }}" aria-label="{{ esc_attr($title) }}">
                            {!! $thumb_html !!}
                        </a>

                        <div class="events-cards__content">
                            <div class="events-cards__title">
                                <a href="{{ get_permalink() }}">{{ $title }}</a>
                            </div>

                            <div class="events-cards__text">
                                <a href="{{ get_permalink() }}">
                                    <p>{{ $excerpt_clean }}</p>
                                </a>
                            </div>

                            <div class="events-cards__bottom">
                                <div class="events-card__date">{{ $date_human }}</div>
                                <div class="events-cards__time">{{ $ago }}</div>
                            </div>
                        </div>
                    </article>
                @endwhile
            </div>

            @php wp_reset_postdata(); @endphp
        @else
            <p>Пока нет событий.</p>
        @endif
    </section>
@endsection
