@if (have_rows('team_members'))
    <div class="team">
        <div class="team__wrap">
            @while (have_rows('team_members'))
                @php the_row(); @endphp

                @php
                    // Поля ACF из репитера
                    $img_id = get_sub_field('photo');
                    $name = trim((string) get_sub_field('name'));
                    $pos = (string) get_sub_field('position');
                    $quote = trim((string) get_sub_field('quote_text'));
                    $showQ = (bool) get_sub_field('show_quote');

                    // Картинки нужных размеров (заранее добавь add_image_size в setup.php)
                    $photo_main = $img_id ? wp_get_attachment_image_url($img_id, 'team-photo') : null;
                    $photo_small = $img_id ? wp_get_attachment_image_url($img_id, 'team-avatar') : null;

                    // Инициал-аватар (фоллбэк при отсутствии фото)
                    $initial = $name !== '' ? mb_strtoupper(mb_substr($name, 0, 1, 'UTF-8'), 'UTF-8') : '•';
                    $palette = ['#FF8F33', '#6C5CE7', '#00B894', '#0984E3', '#E17055', '#E84393', '#2D3436'];
                    $bg = $name !== '' ? $palette[crc32($name) % count($palette)] : '#999';
                @endphp

                <div class="team__element">
                    <div class="team__photo">
                        @if ($photo_main)
                            <img src="{{ esc_url($photo_main) }}" alt="{{ esc_attr($name) }}">
                        @else
                            <div class="avatar-initial" aria-label="{{ esc_attr($name) }}"
                                style="background: {{ $bg }};">
                                {{ $initial }}
                            </div>
                        @endif

                        @if ($showQ && $quote !== '')
                            <div class="team__quote">
                                <div class="quote-card">
                                    <div class="quote-card__user">
                                        @if ($photo_small)
                                            <img src="{{ esc_url($photo_small) }}" alt="{{ esc_attr($name) }}">
                                        @else
                                            <div class="avatar-initial avatar-initial--sm"
                                                aria-label="{{ esc_attr($name) }}"
                                                style="background: {{ $bg }};">
                                                {{ $initial }}
                                            </div>
                                        @endif
                                        <span>{{ $name }}</span>
                                    </div>
                                    <div class="quote-card__body">
                                        <p>{{ $quote }}</p>
                                    </div>
                                </div>

                                <svg width="13" height="10" viewBox="0 0 13 10" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                                    <g>
                                        <path
                                            d="M2.91183 0.0778809C4.48979 0.0778809 5.76897 1.33708 5.76897 2.89038C5.76897 5.42163 4.3404 7.67163 2.3404 9.07788L2.6529 5.6908C1.19631 5.56196 0.0546875 4.35775 0.0546875 2.89038C0.0546875 1.33708 1.33387 0.0778809 2.91183 0.0778809Z"
                                            style="fill: var(--silver-grey);" />
                                        <path
                                            d="M9.62612 0.0778809C11.2041 0.0778809 12.4833 1.33708 12.4833 2.89038C12.4833 5.42163 11.0547 7.67163 9.05469 9.07788L9.36719 5.6908C7.9106 5.56196 6.76897 4.35775 6.76897 2.89038C6.76897 1.33708 8.04816 0.0778809 9.62612 0.0778809Z"
                                            style="fill: var(--silver-grey);" />
                                    </g>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <div class="team__text">
                        @if ($name !== '')
                            <span class="team__name">{{ $name }}</span>
                        @endif
                        @if ($pos !== '')
                            <span class="team__position">{{ $pos }}</span>
                        @endif
                    </div>
                </div>
            @endwhile
        </div>

        <div class="team__wrap_quadro"></div>
    </div>
@else
    <div class="team">
        <p>Состав команды пока не добавлен.</p>
    </div>
@endif
