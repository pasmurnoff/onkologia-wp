@php
    if (!isset($q)) {
        $q = new \WP_Query([
            'category_name' => 'otzyvy',
            'posts_per_page' => 5,
            'no_found_rows' => true,
            'post_status' => 'publish',
        ]);
    }
@endphp

@if ($q->have_posts())
    <div class="feedback">
        <div class="feedback__wrap">
            @while ($q->have_posts())
                @php
                    $q->the_post();
                    $post_id = get_the_ID();

                    // Имя/слаг
                    $name = get_the_title($post_id);
                    $slug = get_post_field('post_name', $post_id);

                    // Фото (миниатюра поста)
                    $avatar_url = get_the_post_thumbnail_url($post_id, 'feedback-avatar'); // или 'thumbnail'

                    // Инициал-аватар
                    $initial = mb_strtoupper(mb_substr(trim($name), 0, 1, 'UTF-8'), 'UTF-8');
                    $palette = ['#FF8F33', '#6C5CE7', '#00B894', '#0984E3', '#E17055', '#E84393', '#2D3436'];
                    $bg = $palette[crc32($name) % count($palette)];

                    // Короткий текст для карточки (по символам)
                    $text = get_the_excerpt($post_id);
                    $text = $text
                        ? wp_strip_all_tags($text)
                        : wp_strip_all_tags(get_the_content(null, false, $post_id));
                    $limit = 80;
                    if (mb_strlen($text, 'UTF-8') > $limit) {
                        $text = mb_substr($text, 0, $limit, 'UTF-8') . '…';
                    }

                    // Полный текст для модалки: content → excerpt → «голый» content
                    $raw = (string) get_post_field('post_content', $post_id);
                    $full = apply_filters('the_content', do_shortcode($raw));
                    // убрать только старые вложенные .modal
                    if ($full) {
                        $full = preg_replace('~<div[^>]*class="[^"]*\bmodal\b[^"]*"[^>]*>.*?</div>~si', '', $full);
                    }
                    if (!trim(wp_strip_all_tags($full))) {
                        $ex = (string) get_the_excerpt($post_id);
                        if ($ex) {
                            $full = wpautop(esc_html($ex));
                        }
                    }
                    if (!trim(wp_strip_all_tags($full))) {
                        $raw_text = trim(wp_strip_all_tags($raw));
                        if ($raw_text !== '') {
                            $long_limit = 1500; // ограничение для очень длинных отзывов
                            if (mb_strlen($raw_text, 'UTF-8') > $long_limit) {
                                $raw_text = mb_substr($raw_text, 0, $long_limit, 'UTF-8') . '…';
                            }
                            $full = wpautop(esc_html($raw_text));
                        }
                    }
                    if (!trim(wp_strip_all_tags($full))) {
                        $full = '<p>Текст отзыва скоро добавим.</p>';
                    }
                @endphp

                <div class="feedback__item">
                    <div class="feedback__user">
                        <div class="user">
                            <div class="user__image">
                                @if ($avatar_url)
                                    <img src="{{ esc_url($avatar_url) }}" alt="{{ esc_attr($name) }}">
                                @else
                                    <div class="avatar-initial" style="background: {{ $bg }};" role="img"
                                        aria-label="{{ $name }}">
                                        {{ $initial }}
                                    </div>
                                @endif
                            </div>
                            <div class="user__info">
                                <span class="user__name">{{ $name }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="feedback__body">
                        <p>{{ $text }}</p>
                    </div>

                    <div class="feedback__action">
                        <a href="#" class="btn_secondary js-open-feedback" data-id="{{ $post_id }}"
                            data-modal-open="feedback-item">
                            Читать отзыв
                        </a>
                    </div>

                    {{-- Шаблон модалки с полным отзывом --}}
                    <script type="text/template" id="tmpl-feedback-item-{{ $post_id }}">
          <div class="modal__user">
            <div class="image">
              @if ($avatar_url)
                <img src="{{ esc_url($avatar_url) }}" alt="{{ esc_attr($name) }}">
              @else
                <div class="avatar-initial" style="background: {{ $bg }};" role="img" aria-label="{{ $name }}">
                  {{ $initial }}
                </div>
              @endif
            </div>
            <div class="info">
              <span class="name">{{ $name }}</span>
            </div>
          </div>

          <div class="modal__body">
            {!! $full !!}
          </div>
        </script>
                </div>
            @endwhile

            {{-- Карточка "Написать отзыв" --}}
            <div class="feedback__item_create">
                <div class="feedback__promo">
                    <div class="feedback__icon">
                        <svg width="19" height="18" viewBox="0 0 19 18" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M8.22545 0.771372C8.60603 -0.257126 10.0607 -0.257123 10.4413 0.771374L12.176 5.4594C12.2957 5.78275 12.5506 6.0377 12.874 6.15735L17.562 7.89208C18.5905 8.27266 18.5905 9.72734 17.562 10.1079L12.874 11.8426C12.5506 11.9623 12.2957 12.2172 12.176 12.5406L10.4413 17.2286C10.0607 18.2571 8.60603 18.2571 8.22545 17.2286L6.49073 12.5406C6.37107 12.2172 6.11613 11.9623 5.79277 11.8426L1.10475 10.1079C0.0762485 9.72734 0.076251 8.27265 1.10475 7.89208L5.79277 6.15735C6.11613 6.0377 6.37107 5.78275 6.49073 5.4594L8.22545 0.771372Z"
                                fill="#FF8F33" />
                        </svg>
                    </div>
                    <div class="feedback__slogan">
                        <p>Мы всегда рады любым вашим <span>теплым словам</span> о нашем фонде!</p>
                    </div>
                </div>
                <div class="feedback__action_create">
                    <a href="https://forum.onkologia.ru/t/otzyvy-o-deyatelnosti-fonda-reshenie-zhit/90"
                        class="btn_secondary">Написать отзыв</a>
                </div>
            </div>
        </div>
    </div>

    @php wp_reset_postdata(); @endphp
@else
    <div class="feedback">
        <div class="feedback__wrap">
            <p>Пока отзывов нет.</p>
        </div>
    </div>
@endif
