{{-- resources/views/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <section class="events container_rg">
        @php
            // Заголовок страницы записей
            $posts_page_id = get_option('page_for_posts');
            $page_title = $posts_page_id ? get_the_title($posts_page_id) : 'События';

            // Подготовка пагинации
            $paged = get_query_var('paged') ? (int) get_query_var('paged') : 1;

            // Получаем только записи из рубрики "sobytiya"
            $events_query = new WP_Query([
                'post_type' => 'post',
                'category_name' => 'sobytiya', // ← ключ рубрики (slug)
                'posts_per_page' => 9,
                'paged' => $paged,
                'ignore_sticky_posts' => true,
            ]);
        @endphp

        <h1 class="page-title">{{ $page_title }}</h1>

        @includeIf('components.follow-us-events.wrap')

        @if ($events_query->have_posts())
            <div class="events-cards">
                @while ($events_query->have_posts())
                    @php $events_query->the_post(); @endphp

                    @php
                        $thumb = has_post_thumbnail()
                            ? get_the_post_thumbnail(null, 'medium_large', ['class' => 'events-cards__image'])
                            : '<img class="events-cards__image" src="' .
                                esc_url(get_theme_file_uri('resources/assets/images/placeholder.png')) .
                                '" alt="' .
                                esc_attr(get_the_title()) .
                                '">';
                        $date_human = get_the_date('j F Y');
                        $ago = human_time_diff(get_the_time('U'), current_time('timestamp')) . ' назад';
                    @endphp

                    <article @php post_class('events-cards__element') @endphp>
                        <a href="{{ get_permalink() }}" aria-label="{{ esc_attr(get_the_title()) }}">
                            {!! $thumb !!}
                        </a>

                        <div class="events-cards__content">
                            <div class="events-cards__title">
                                <a href="{{ get_permalink() }}">{{ get_the_title() }}</a>
                            </div>

                            @php
                                $excerpt = get_the_excerpt();
                                if (!$excerpt) {
                                    $excerpt = wp_strip_all_tags(get_the_content());
                                } else {
                                    $excerpt = wp_strip_all_tags($excerpt);
                                }

                                $limit = 120; // сколько символов оставить
                                if (mb_strlen($excerpt, 'UTF-8') > $limit) {
                                    $excerpt = mb_substr($excerpt, 0, $limit, 'UTF-8') . '…';
                                }
                            @endphp

                            <div class="events-cards__text">
                                <p>{!! $excerpt !!}</p>
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
