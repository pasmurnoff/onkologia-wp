<?php
// вызывать там, где нужна секция: render_latest_topics_section();
function render_latest_topics_section()
{
  // ===== Конфиг =====
  $DISCOURSE_BASE = 'https://forum.onkologia.ru';
  $SOURCE_URL = $DISCOURSE_BASE . '/latest.json';
  $CACHE_DIR = __DIR__ . '/../cache';
  $CACHE_FILE = $CACHE_DIR . '/latest.json';
  $CACHE_TTL = 60;   // сек
  $TOPICS_LIMIT = 10;   // строк
  $AVATAR_SIZE = 48;   // px
  $AVATARS_LIMIT = 5;    // аватарок
  $CATMAP_FILE = $CACHE_DIR . '/categories-map.json';
  $CATMAP_TTL = 300;  // сек

  @mkdir($CACHE_DIR, 0775, true);

  // ===== Хелперы =====
  $h = function ($s) {
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
  };

  // мини-рендерер эмодзи :shortcode: -> Unicode (для заголовков)
  $render_emojis = function (string $s): string {
    static $map = [
    ':wave:' => '👋',
    ':smile:' => '😄',
    ':grinning:' => '😀',
    ':grin:' => '😁',
    ':wink:' => '😉',
    ':blush:' => '😊',
    ':slightly_smiling_face:' => '🙂',
    ':heart:' => '❤️',
    ':blue_heart:' => '💙',
    ':green_heart:' => '💚',
    ':yellow_heart:' => '💛',
    ':purple_heart:' => '💜',
    ':heartpulse:' => '💗',
    ':cry:' => '😢',
    ':sob:' => '😭',
    ':thinking:' => '🤔',
    ':star:' => '⭐',
    ':fire:' => '🔥',
    ':ok_hand:' => '👌',
    ':thumbsup:' => '👍',
    ':thumbsdown:' => '👎',
    ':clap:' => '👏',
    ':tada:' => '🎉'
    ];
    return strpos($s, ':') === false ? $s : strtr($s, $map);
  };

  $http_get_json_raw = function (string $url): string {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_TIMEOUT => 6,
      CURLOPT_CONNECTTIMEOUT => 4,
      CURLOPT_USERAGENT => 'onkologia-landing/1.0 (+latest-topics)',
    ]);
    $body = curl_exec($ch);
    if ($body === false)
      throw new Exception('cURL: ' . curl_error($ch));
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($code >= 400)
      throw new Exception('HTTP ' . $code);
    return $body;
  };

  $write_cache_atomic = function (string $path, string $content): void {
    $tmp = $path . '.' . getmypid() . '.tmp';
    if (@file_put_contents($tmp, $content, LOCK_EX) !== false) {
      @rename($tmp, $path);
    }
  };

  $avatar_url = function ($template, int $size, string $base): string {
    if (!$template)
      return '';
    $u = str_replace('{size}', (string) $size, $template);
    if (strpos($u, 'http') === 0)
      return $u;
    if (strpos($u, '//') === 0)
      return 'https:' . $u;
    return rtrim($base, '/') . $u;
  };

  // ===== Загрузка / кеш latest =====
  $need_refresh = !file_exists($CACHE_FILE) || (time() - @filemtime($CACHE_FILE) > $CACHE_TTL);
  if ($need_refresh) {
    try {
      $raw = $http_get_json_raw($SOURCE_URL);
      $write_cache_atomic($CACHE_FILE, $raw);
    } catch (Throwable $e) {
      if (!file_exists($CACHE_FILE)) {
        $write_cache_atomic($CACHE_FILE, '{"topic_list":{"topics":[]},"users":[],"categories":[]}');
      }
    }
  }

  $payload = json_decode(@file_get_contents($CACHE_FILE) ?: '{"topic_list":{"topics":[]},"users":[],"categories":[]}', true);
  if (!is_array($payload))
    $payload = ["topic_list" => ["topics" => []], "users" => [], "categories" => []];

  $topics = $payload['topic_list']['topics'] ?? [];
  $users = $payload['users'] ?? [];
  $cats = $payload['categories'] ?? [];

  // ===== Мапы =====
  $userById = [];
  foreach ($users as $u)
    if (isset($u['id']))
      $userById[(int) $u['id']] = $u;

  // Категории из latest.json (если есть)
  $catById = [];
  foreach ($cats as $c)
    if (isset($c['id']))
      $catById[(int) $c['id']] = [
        'id' => (int) $c['id'],
        'name' => (string) ($c['name'] ?? ''),
        'slug' => (string) ($c['slug'] ?? '')
      ];

  // Fallback: локальная карта из categories.json
  if (empty($catById)) {
    $cats_need_refresh = !file_exists($CATMAP_FILE) || (time() - @filemtime($CATMAP_FILE) > $CATMAP_TTL);
    if ($cats_need_refresh) {
      try {
        $rawCats = $http_get_json_raw($DISCOURSE_BASE . '/categories.json?include_subcategories=true');
        $src = json_decode($rawCats, true);
        $map = [];
        foreach ($src['category_list']['categories'] ?? [] as $c) {
          if (!empty($c['id']) && !empty($c['name'])) {
            $map[(int) $c['id']] = [
              'id' => (int) $c['id'],
              'name' => (string) $c['name'],
              'slug' => (string) ($c['slug'] ?? '')
            ];
          }
        }
        $write_cache_atomic($CATMAP_FILE, json_encode($map, JSON_UNESCAPED_UNICODE));
      } catch (Throwable $e) {
        if (!file_exists($CATMAP_FILE))
          $write_cache_atomic($CATMAP_FILE, '{}');
      }
    }
    $catById = json_decode(@file_get_contents($CATMAP_FILE) ?: '{}', true);
    if (!is_array($catById))
      $catById = [];
  }

  // Ограничение по числу тем
  $topics = array_slice($topics, 0, $TOPICS_LIMIT);
  ?>

  <!-- START latest-topics-section -->
  <div class="section">
    <div class="section__header">
      <h2 class="section__title">Последние обсуждения</h2>
      <a href="<?= $h($DISCOURSE_BASE) ?>" class="section__link">Все
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"
          focusable="false">
          <path d="M7.5 15L12.5 10L7.5 5" style="stroke: var(--silver-grey);" stroke-width="1.67" stroke-linecap="round"
            stroke-linejoin="round" />
        </svg>
      </a>
    </div>

    <div class="section__table">
      <div class="table">
        <?php foreach ($topics as $t):
          // Заголовок (+ эмодзи)
          $title = $render_emojis($t['title'] ?? '');
          $slug = $t['slug'] ?? '';
          $tid = (int) ($t['id'] ?? 0);
          $url = $DISCOURSE_BASE . '/t/' . rawurlencode($slug) . '/' . $tid;

          // Категория темы
          $catName = '';
          $catUrl = '';
          $cat = null;
          $cid = 0;
          if (!empty($t['category_id'])) {
            $cid = (int) $t['category_id'];
            if (isset($catById[$cid])) {
              $cat = $catById[$cid];
              $catName = (string) ($cat['name'] ?? '');
              $slugC = (string) ($cat['slug'] ?? '');
              $catUrl = $slugC !== '' ? ($DISCOURSE_BASE . '/c/' . rawurlencode($slugC) . '/' . $cid)
                : ($DISCOURSE_BASE . '/c/' . $cid);
            }
          }

          // Теги
          $tags = [];
          if (!empty($t['tags']) && is_array($t['tags'])) {
            // оставим, например, до 5 тегов
            $tags = array_slice(array_values(array_filter($t['tags'], 'strlen')), 0, 5);
          }

          // Аватары участников
          $posterIds = [];
          if (!empty($t['posters']) && is_array($t['posters'])) {
            foreach ($t['posters'] as $p)
              if (isset($p['user_id']))
                $posterIds[] = (int) $p['user_id'];
            $posterIds = array_values(array_unique($posterIds));
          }
          $posterIds = array_slice($posterIds, 0, $AVATARS_LIMIT);

          $avatars = [];
          foreach ($posterIds as $uid) {
            $u = $userById[$uid] ?? null;
            if (!$u)
              continue;
            $src = $avatar_url($u['avatar_template'] ?? '', $AVATAR_SIZE, $DISCOURSE_BASE);
            if ($src)
              $avatars[] = ['src' => $src, 'alt' => $u['username'] ?? 'User Avatar'];
          }
          ?>
          <div class="table__item">
            <div class="table__content">
              <a class="table__title" href="<?= $h($url) ?>" target="_blank" rel="noopener"><?= $h($title) ?></a>

              <?php if ($catName !== '' || !empty($tags)): ?>
                <div class="table__text" style="display:flex; flex-wrap:wrap; gap:0.5em; align-items:center;">
                  <?php if ($catName !== ''): ?>
                    <a href="<?= $h($catUrl) ?>" target="_blank" rel="noopener"><?= $h($catName) ?></a>
                  <?php endif; ?>

                  <div class="table__tags">
                    <?php foreach ($tags as $tag):
                      $tagUrl = $DISCOURSE_BASE . '/tag/' . rawurlencode($tag);
                      ?>
                      <a href="<?= $h($tagUrl) ?>" class="tag-pill" target="_blank" rel="noopener">
                        <?= $h($tag) ?>
                      </a>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endif; ?>
            </div>

            <div class="table__users">
              <div class="user-avatars">
                <?php foreach ($avatars as $av): ?>
                  <img src="<?= $h($av['src']) ?>" alt="<?= $h($av['alt']) ?>">
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <!-- END latest-topics-section -->
  <?php
}
