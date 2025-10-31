<?php

namespace App;

/**
 * Add <body> classes
 */
add_filter('body_class', function (array $classes) {
    /** Add page slug if it doesn't exist */
    if (is_single() || is_page() && !is_front_page()) {
        if (!in_array(basename(get_permalink()), $classes)) {
            $classes[] = basename(get_permalink());
        }
    }

    /** Add class if sidebar is active */
    if (display_sidebar()) {
        $classes[] = 'sidebar-primary';
    }

    /** Clean up class names for custom templates */
    $classes = array_map(function ($class) {
        return preg_replace(['/-blade(-php)?$/', '/^page-template-views/'], '', $class);
    }, $classes);

    return array_filter($classes);
});

/**
 * Add "… Continued" to the excerpt
 */
add_filter('excerpt_more', function () {
    return ' &hellip; <a href="' . get_permalink() . '">' . __('Continued', 'sage') . '</a>';
});

/**
 * Template Hierarchy should search for .blade.php files
 */
collect([
    'index',
    '404',
    'archive',
    'author',
    'category',
    'tag',
    'taxonomy',
    'date',
    'home',
    'frontpage',
    'page',
    'paged',
    'search',
    'single',
    'singular',
    'attachment',
    'embed'
])->map(function ($type) {
    add_filter("{$type}_template_hierarchy", __NAMESPACE__ . '\\filter_templates');
});

/**
 * Render page using Blade
 */
add_filter('template_include', function ($template) {
    collect(['get_header', 'wp_head'])->each(function ($tag) {
        ob_start();
        do_action($tag);
        $output = ob_get_clean();
        remove_all_actions($tag);
        add_action($tag, function () use ($output) {
            echo $output;
        });
    });
    $data = collect(get_body_class())->reduce(function ($data, $class) use ($template) {
        return apply_filters("sage/template/{$class}/data", $data, $template);
    }, []);
    if ($template) {
        echo template($template, $data);
        return get_stylesheet_directory() . '/index.php';
    }
    return $template;
}, PHP_INT_MAX);

/**
 * Render comments.blade.php
 */
add_filter('comments_template', function ($comments_template) {
    $comments_template = str_replace(
        [get_stylesheet_directory(), get_template_directory()],
        '',
        $comments_template
    );

    $data = collect(get_body_class())->reduce(function ($data, $class) use ($comments_template) {
        return apply_filters("sage/template/{$class}/data", $data, $comments_template);
    }, []);

    $theme_template = locate_template(["views/{$comments_template}", $comments_template]);

    if ($theme_template) {
        echo template($theme_template, $data);
        return get_stylesheet_directory() . '/index.php';
    }

    return $comments_template;
}, 100);

add_action('save_post', function ($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;
    if (wp_is_post_revision($post_id))
        return;

    if (!function_exists('get_field'))
        return;

    $vk_url = (string) get_field('vk_video_url', $post_id);
    if (!$vk_url)
        return;

    // Если уже есть миниатюра — ничего не делаем
    if (has_post_thumbnail($post_id))
        return;

    $thumb_url = mytheme_resolve_vk_thumbnail($vk_url);
    if (!$thumb_url)
        return;

    // Сайдлоадим изображение и назначаем как featured
    $tmp = download_url($thumb_url);
    if (is_wp_error($tmp))
        return;

    $file = [
        'name' => basename(parse_url($thumb_url, PHP_URL_PATH)),
        'type' => 'image/jpeg',
        'tmp_name' => $tmp,
        'size' => filesize($tmp),
        'error' => 0,
    ];

    $id = media_handle_sideload($file, $post_id, 'VK video thumbnail');
    if (is_wp_error($id)) {
        @unlink($tmp);
        return;
    }

    set_post_thumbnail($post_id, $id);
}, 20);

/**
 * Получить URL превью для VK-видео.
 * 1) Пытаемся через oEmbed (thumbnail_url)
 * 2) Если нет — парсим Open Graph на странице (og:image)
 * Результат кешируем в transient на сутки.
 */
function mytheme_resolve_vk_thumbnail($url)
{
    $key = 'vk_thumb_' . md5($url);
    $cached = get_transient($key);
    if ($cached !== false)
        return $cached;

    // 1) oEmbed
    $oembed = mytheme_fetch_oembed($url);
    if (!empty($oembed['thumbnail_url'])) {
        set_transient($key, $oembed['thumbnail_url'], DAY_IN_SECONDS);
        return $oembed['thumbnail_url'];
    }

    // 2) OpenGraph
    $og = mytheme_fetch_og_image($url);
    if ($og) {
        set_transient($key, $og, DAY_IN_SECONDS);
        return $og;
    }

    set_transient($key, '', HOUR_IN_SECONDS);
    return '';
}

function mytheme_fetch_oembed($url)
{
    // Пытаемся через WordPress oEmbed (если провайдер известен/есть discovery)
    $provider = _wp_oembed_get_object();
    $data = $provider->get_data($url);
    if ($data && is_object($data)) {
        return (array) $data;
    }
    return [];
}

function mytheme_fetch_og_image($url)
{
    $response = wp_remote_get($url, [
        'timeout' => 8,
        'redirection' => 5,
        'user-agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url('/'),
    ]);
    if (is_wp_error($response))
        return '';

    $html = wp_remote_retrieve_body($response);
    if (!$html)
        return '';

    // Ищем <meta property="og:image" content="...">
    if (preg_match('/<meta[^>]+property=["\']og:image["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $m)) {
        return esc_url_raw(html_entity_decode($m[1]));
    }
    return '';
}
