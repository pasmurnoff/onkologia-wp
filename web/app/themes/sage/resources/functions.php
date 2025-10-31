<?php

/**
 * Do not edit anything in this file unless you know what you're doing
 */

use Roots\Sage\Config;
use Roots\Sage\Container;

/**
 * Helper function for prettying up errors
 * @param string $message
 * @param string $subtitle
 * @param string $title
 */
$sage_error = function ($message, $subtitle = '', $title = '') {
    $title = $title ?: __('Sage &rsaquo; Error', 'sage');
    $footer = '<a href="https://roots.io/sage/docs/">roots.io/sage/docs/</a>';
    $message = "<h1>{$title}<br><small>{$subtitle}</small></h1><p>{$message}</p><p>{$footer}</p>";
    wp_die($message, $title);
};

/**
 * Ensure compatible version of PHP is used
 */
if (version_compare('7.1', phpversion(), '>=')) {
    $sage_error(__('You must be using PHP 7.1 or greater.', 'sage'), __('Invalid PHP version', 'sage'));
}

/**
 * Ensure compatible version of WordPress is used
 */
if (version_compare('4.7.0', get_bloginfo('version'), '>=')) {
    $sage_error(__('You must be using WordPress 4.7.0 or greater.', 'sage'), __('Invalid WordPress version', 'sage'));
}

/**
 * Ensure dependencies are loaded
 */
if (!class_exists('Roots\\Sage\\Container')) {
    if (!file_exists($composer = __DIR__ . '/../vendor/autoload.php')) {
        $sage_error(
            __('You must run <code>composer install</code> from the Sage directory.', 'sage'),
            __('Autoloader not found.', 'sage')
        );
    }
    require_once $composer;
}

/**
 * Sage required files
 *
 * The mapped array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 */
array_map(function ($file) use ($sage_error) {
    $file = "../app/{$file}.php";
    if (!locate_template($file, true, true)) {
        $sage_error(sprintf(__('Error locating <code>%s</code> for inclusion.', 'sage'), $file), 'File not found');
    }
}, ['helpers', 'setup', 'filters', 'admin']);

/**
 * Here's what's happening with these hooks:
 * 1. WordPress initially detects theme in themes/sage/resources
 * 2. Upon activation, we tell WordPress that the theme is actually in themes/sage/resources/views
 * 3. When we call get_template_directory() or get_template_directory_uri(), we point it back to themes/sage/resources
 *
 * We do this so that the Template Hierarchy will look in themes/sage/resources/views for core WordPress themes
 * But functions.php, style.css, and index.php are all still located in themes/sage/resources
 *
 * This is not compatible with the WordPress Customizer theme preview prior to theme activation
 *
 * get_template_directory()   -> /srv/www/example.com/current/web/app/themes/sage/resources
 * get_stylesheet_directory() -> /srv/www/example.com/current/web/app/themes/sage/resources
 * locate_template()
 * ├── STYLESHEETPATH         -> /srv/www/example.com/current/web/app/themes/sage/resources/views
 * └── TEMPLATEPATH           -> /srv/www/example.com/current/web/app/themes/sage/resources
 */
array_map(
    'add_filter',
    ['theme_file_path', 'theme_file_uri', 'parent_theme_file_path', 'parent_theme_file_uri'],
    array_fill(0, 4, 'dirname')
);
Container::getInstance()
    ->bindIf('config', function () {
        return new Config([
            'assets' => require dirname(__DIR__) . '/config/assets.php',
            'theme' => require dirname(__DIR__) . '/config/theme.php',
            'view' => require dirname(__DIR__) . '/config/view.php',
        ]);
    }, true);


/**
 * On this file we only append another function files
 */
require_once dirname(__DIR__ . '/resources') . '/functions/remove.php';

/**
 * Превращает любые популярные формы ссылок VK-видео в iframe-плеер.
 * Примеры входа:
 *  - https://vk.com/video-226644696_456239023
 *  - https://vk.com/video?z=video-226644696_456239023
 *  - https://m.vk.com/video-226644696_456239023
 * Выход:
 *  - https://vk.com/video_ext.php?oid=-226644696&id=456239023
 */
function mytheme_vk_video_embed_iframe($url)
{
    $oid = null;
    $id = null;

    // 1) Прямой формат: /video-XXXX_YYYY
    if (preg_match('~vk\.com/(?:.*?/)??video(?P<oid>-?\d+)_(?P<id>\d+)~i', $url, $m)) {
        $oid = $m['oid'];
        $id = $m['id'];
    }

    // 2) Формат /video?z=video-XXXX_YYYY
    if (!$oid || !$id) {
        $parts = parse_url($url);
        if (!empty($parts['query'])) {
            parse_str($parts['query'], $q);
            if (!empty($q['z']) && preg_match('~video(?P<oid>-?\d+)_(?P<id>\d+)~', $q['z'], $m2)) {
                $oid = $m2['oid'];
                $id = $m2['id'];
            }
        }
    }

    if (!$oid || !$id) {
        return ''; // не смогли распарсить
    }

    // базовый embed
    $src = sprintf('https://vk.com/video_ext.php?oid=%s&id=%s', rawurlencode($oid), rawurlencode($id));

    // соберём iframe
    $iframe = sprintf(
        '<iframe src="%s" frameborder="0" allow="autoplay; encrypted-media; fullscreen; picture-in-picture" ' .
        'allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade" ' .
        'style="width:100%%;height:100%%;display:block;"></iframe>',
        esc_url($src)
    );

    return $iframe;
}


/**
 * --- УСТАНОВКА ОБЛОЖКИ ПО ПОЛЮ "vk_video_url" ---
 * Работает после сохранения ACF, выкачивает превью и ставит featured image.
 */

add_action('acf/save_post', function ($post_id) {
    // 1. Проверим, что это не ревизия и не автосейв
    if (wp_is_post_revision($post_id) || defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

    // 2. Проверим, что это пост (если хочешь — поменяй на свой CPT)
    if (get_post_type($post_id) !== 'post')
        return;

    if (!function_exists('get_field'))
        return;

    // 3. Забираем URL из ACF
    $vk_url = trim((string) get_field('vk_video_url', $post_id));
    if (!$vk_url)
        return;

    // 4. Если уже есть миниатюра — пропускаем (можно закомментировать, если хочешь перезаписывать)
    if (has_post_thumbnail($post_id))
        return;

    // 5. Получаем превью (через oEmbed → OG)
    $thumb_url = mytheme_resolve_vk_thumbnail($vk_url);
    if (!$thumb_url) {
        error_log("VK thumbnail not found for {$vk_url}");
        return;
    }

    // 6. Скачиваем и ставим как featured image
    $att_id = mytheme_sideload_image($thumb_url, $post_id, 'VK video thumbnail');
    if ($att_id) {
        set_post_thumbnail($post_id, $att_id);
        error_log("✅ VK thumbnail set for post {$post_id}");
    } else {
        error_log("❌ Failed to sideload image for post {$post_id}");
    }
}, 20);

/**
 * --- ХЕЛПЕРЫ ---
 */

function mytheme_fetch_oembed($url)
{
    $o = _wp_oembed_get_object();
    $data = $o ? $o->get_data($url) : null;
    return ($data && is_object($data)) ? (array) $data : [];
}

function mytheme_fetch_og_image($url)
{
    $res = wp_remote_get($url, [
        'timeout' => 10,
        'redirection' => 5,
        'user-agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url('/'),
    ]);
    if (is_wp_error($res)) {
        error_log("VK OG fetch error: " . $res->get_error_message());
        return '';
    }
    $html = wp_remote_retrieve_body($res);
    if (!$html)
        return '';
    if (preg_match('/<meta[^>]+property=["\']og:image["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $m)) {
        return esc_url_raw(html_entity_decode($m[1]));
    }
    return '';
}

function mytheme_resolve_vk_thumbnail($video_url)
{
    $key = 'vk_thumb_' . md5($video_url);
    $cached = get_transient($key);
    if ($cached !== false)
        return $cached;

    $thumb = '';
    $oe = mytheme_fetch_oembed($video_url);
    if (!empty($oe['thumbnail_url']))
        $thumb = $oe['thumbnail_url'];
    if (!$thumb)
        $thumb = mytheme_fetch_og_image($video_url);

    set_transient($key, $thumb ?: '', DAY_IN_SECONDS);
    return $thumb;
}

function mytheme_sideload_image($img_url, $post_id, $desc = '')
{
    if (!function_exists('media_handle_sideload')) {
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
    }

    $tmp = download_url($img_url, 15);
    if (is_wp_error($tmp)) {
        error_log("download_url error: " . $tmp->get_error_message());
        return 0;
    }

    $name = basename(parse_url($img_url, PHP_URL_PATH) ?: 'vk-thumb.jpg');
    $file = [
        'name' => $name,
        'type' => 'image/jpeg',
        'tmp_name' => $tmp,
        'size' => filesize($tmp),
        'error' => 0,
    ];

    $att_id = media_handle_sideload($file, $post_id, $desc);
    if (is_wp_error($att_id)) {
        @unlink($tmp);
        error_log("media_handle_sideload error: " . $att_id->get_error_message());
        return 0;
    }

    return (int) $att_id;
}
