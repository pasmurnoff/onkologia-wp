<?php
function render_categories_section() {
  // --- локальный конфиг ---
  $DISCOURSE_BASE = 'https://forum.onkologia.ru';
  $SOURCE_URL     = $DISCOURSE_BASE . '/categories.json?include_subcategories=true';
  $CACHE_DIR      = __DIR__ . '/../cache';
  $CACHE_FILE     = $CACHE_DIR . '/categories.json';
  $CACHE_TTL      = 300;
  $ORDER          = []; // например: [7,8,5,9,12,10]

  // --- локальные хелперы как замыкания ---
  @mkdir($CACHE_DIR, 0775, true);

  $http_get_json_raw = function(string $url): string {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_TIMEOUT        => 6,
      CURLOPT_CONNECTTIMEOUT => 4,
      CURLOPT_USERAGENT      => 'onkologia-landing/1.0 (+categories)',
    ]);
    $body = curl_exec($ch);
    if ($body === false) throw new Exception('cURL: ' . curl_error($ch));
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($code >= 400) throw new Exception('HTTP ' . $code);
    return $body;
  };

  $write_cache_atomic = function(string $path, string $content): void {
    $tmp = $path . '.' . getmypid() . '.tmp';
    if (@file_put_contents($tmp, $content, LOCK_EX) !== false) {
      @rename($tmp, $path);
    }
  };

  $h = function($s){ return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); };

  $decl = function(int $n, string $f1, string $f2, string $f5): string {
    $n = abs($n) % 100; $n1 = $n % 10;
    if ($n > 10 && $n < 20) return $f5;
    if ($n1 > 1 && $n1 < 5)  return $f2;
    if ($n1 == 1)            return $f1;
    return $f5;
  };

  // --- загрузка/кеш ---
  $need_refresh = !file_exists($CACHE_FILE) || (time() - @filemtime($CACHE_FILE) > $CACHE_TTL);
  if ($need_refresh) {
    try {
      $raw = $http_get_json_raw($SOURCE_URL);
      $write_cache_atomic($CACHE_FILE, $raw);
    } catch (Throwable $e) {
      if (!file_exists($CACHE_FILE)) {
        $write_cache_atomic($CACHE_FILE, '{"category_list":{"categories":[]}}');
      }
    }
  }

  $json = @file_get_contents($CACHE_FILE);
  $data = json_decode($json ?: '{"category_list":{"categories":[]}}', true);
  $cats = $data['category_list']['categories'] ?? [];

  // публичные корневые
  $cats = array_values(array_filter($cats, static function ($c) {
    return empty($c['read_restricted']) && empty($c['parent_category_id']);
  }));

  // порядок
  if (!empty($ORDER)) {
    $pos = array_flip($ORDER);
    usort($cats, static function($a,$b) use($pos){
      $ia = $pos[$a['id']] ?? PHP_INT_MAX;
      $ib = $pos[$b['id']] ?? PHP_INT_MAX;
      return $ia <=> $ib;
    });
  }

  $cats = array_slice($cats, 0, 6);

  // иконки
  // SVG по умолчанию (если для категории не задана своя иконка)
$svgIconDefault = <<<SVG
<svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
  <rect width="56" height="56" rx="12" fill="#A7A7C9" fill-opacity="0.15"></rect>
  <path d="M27.99 27.22C29.002 26.002 30 25 30 24C30 23.4696 29.7893 22.9609 29.4142 22.5858C29.0391 22.2107 28.5304 22 28 22C27.4696 22 26.9609 22.2107 26.5858 22.5858C26.2107 22.9609 26 23.4696 26 24C26 25 27 25.997 28 27.22L25.35 30.53M28 34L30.57 30.5M28 34L30.646 37.602C30.8026 37.8119 31.0353 37.9521 31.2941 37.9923C31.5528 38.0325 31.8171 37.9695 32.03 37.817L33.924 36.457C34.0336 36.3783 34.1261 36.2782 34.196 36.1628C34.2659 36.0473 34.3117 35.9189 34.3307 35.7853C34.3497 35.6517 34.3414 35.5157 34.3064 35.3853C34.2714 35.255 34.2103 35.1331 34.127 35.027L30.57 30.5M28 34L25.321 37.593C25.164 37.8043 24.93 37.9452 24.6697 37.9851C24.4095 38.025 24.1441 37.9606 23.931 37.806L22.066 36.453C21.9575 36.3743 21.866 36.2746 21.7969 36.1599C21.7277 36.0451 21.6823 35.9176 21.6634 35.785C21.6444 35.6523 21.6523 35.5173 21.6866 35.3877C21.7209 35.2582 21.7809 35.1369 21.863 35.031L25.35 30.53M30.57 30.5C32.222 28.235 33.995 26.22 34 23C34 21.6739 33.3678 20.4021 32.2426 19.4645C31.1174 18.5268 29.5913 18 28 18C26.4087 18 24.8826 18.5268 23.7573 19.4645C22.6321 20.4021 22 21.6739 22 23C22 26.221 23.728 28.246 25.35 30.53M22.243 25.0159C22.8866 24.0857 23.7459 23.3254 24.7475 22.7999C25.7491 22.2744 26.8631 21.9994 27.9942 21.9985C29.1253 21.9977 30.2397 22.2709 31.2421 22.7948C32.2445 23.3187 33.1051 24.0777 33.75 25.0069" style="stroke: var(--black);" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"></path>
</svg>
SVG;

// Карта: id категории => SVG
$ICONS = [
  7 => <<<SVG
<svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
<rect width="56" height="56" rx="12" fill="#A7A7C9" fill-opacity="0.15"/>
<path d="M22 34H30M19 38H37M30 38C31.8565 38 33.637 37.2625 34.9497 35.9497C36.2625 34.637 37 32.8565 37 31C37 29.1435 36.2625 27.363 34.9497 26.0503C33.637 24.7375 31.8565 24 30 24H29M25 30H27M28 22V19C28 18.7348 27.8946 18.4804 27.7071 18.2929C27.5196 18.1054 27.2652 18 27 18H25C24.7348 18 24.4804 18.1054 24.2929 18.2929C24.1054 18.4804 24 18.7348 24 19V22M25 28C24.4696 28 23.9609 27.7893 23.5858 27.4142C23.2107 27.0391 23 26.5304 23 26V22H29V26C29 26.5304 28.7893 27.0391 28.4142 27.4142C28.0391 27.7893 27.5304 28 27 28H25Z" style="stroke: var(--black);" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
SVG,

  8 => <<<SVG
<svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
<rect width="56" height="56" rx="12" fill="#A7A7C9" fill-opacity="0.15"/>
<path d="M24.5 24.5L31.5 31.5M26.5 36.5L36.5 26.5C36.9673 26.0421 37.3391 25.4961 37.5941 24.8935C37.849 24.291 37.982 23.6439 37.9853 22.9896C37.9886 22.3354 37.8622 21.687 37.6133 21.0819C37.3645 20.4768 36.9982 19.9271 36.5355 19.4645C36.0729 19.0018 35.5232 18.6355 34.9181 18.3867C34.313 18.1378 33.6646 18.0114 33.0104 18.0147C32.3561 18.018 31.709 18.151 31.1065 18.4059C30.5039 18.6609 29.9579 19.0327 29.5 19.5L19.5 29.5C19.0327 29.9579 18.6609 30.5039 18.4059 31.1065C18.151 31.709 18.018 32.3561 18.0147 33.0104C18.0114 33.6646 18.1378 34.313 18.3867 34.9181C18.6355 35.5232 19.0018 36.0729 19.4645 36.5355C19.9271 36.9982 20.4768 37.3645 21.0819 37.6133C21.687 37.8622 22.3354 37.9886 22.9896 37.9853C23.6439 37.982 24.291 37.849 24.8935 37.5941C25.4961 37.3391 26.0421 36.9673 26.5 36.5Z" style="stroke: var(--black);" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
SVG,

  5 => <<<SVG
<svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
  <rect width="56" height="56" rx="12" fill="#A7A7C9" fill-opacity="0.15"></rect>
  <path d="M27.99 27.22C29.002 26.002 30 25 30 24C30 23.4696 29.7893 22.9609 29.4142 22.5858C29.0391 22.2107 28.5304 22 28 22C27.4696 22 26.9609 22.2107 26.5858 22.5858C26.2107 22.9609 26 23.4696 26 24C26 25 27 25.997 28 27.22L25.35 30.53M28 34L30.57 30.5M28 34L30.646 37.602C30.8026 37.8119 31.0353 37.9521 31.2941 37.9923C31.5528 38.0325 31.8171 37.9695 32.03 37.817L33.924 36.457C34.0336 36.3783 34.1261 36.2782 34.196 36.1628C34.2659 36.0473 34.3117 35.9189 34.3307 35.7853C34.3497 35.6517 34.3414 35.5157 34.3064 35.3853C34.2714 35.255 34.2103 35.1331 34.127 35.027L30.57 30.5M28 34L25.321 37.593C25.164 37.8043 24.93 37.9452 24.6697 37.9851C24.4095 38.025 24.1441 37.9606 23.931 37.806L22.066 36.453C21.9575 36.3743 21.866 36.2746 21.7969 36.1599C21.7277 36.0451 21.6823 35.9176 21.6634 35.785C21.6444 35.6523 21.6523 35.5173 21.6866 35.3877C21.7209 35.2582 21.7809 35.1369 21.863 35.031L25.35 30.53M30.57 30.5C32.222 28.235 33.995 26.22 34 23C34 21.6739 33.3678 20.4021 32.2426 19.4645C31.1174 18.5268 29.5913 18 28 18C26.4087 18 24.8826 18.5268 23.7573 19.4645C22.6321 20.4021 22 21.6739 22 23C22 26.221 23.728 28.246 25.35 30.53M22.243 25.0159C22.8866 24.0857 23.7459 23.3254 24.7475 22.7999C25.7491 22.2744 26.8631 21.9994 27.9942 21.9985C29.1253 21.9977 30.2397 22.2709 31.2421 22.7948C32.2445 23.3187 33.1051 24.0777 33.75 25.0069" style="stroke: var(--black);" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"></path>
</svg>
SVG,

 9 => <<<SVG
<svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
<rect width="56" height="56" rx="12" fill="#A7A7C9" fill-opacity="0.15"/>
<path d="M25 28H31M28 25V31M36 29C36 34 32.5 36.5 28.34 37.95C28.1222 38.0238 27.8855 38.0203 27.67 37.94C23.5 36.5 20 34 20 29V22C20 21.7347 20.1054 21.4804 20.2929 21.2929C20.4804 21.1053 20.7348 21 21 21C23 21 25.5 19.8 27.24 18.28C27.4519 18.099 27.7214 17.9995 28 17.9995C28.2786 17.9995 28.5481 18.099 28.76 18.28C30.51 19.81 33 21 35 21C35.2652 21 35.5196 21.1053 35.7071 21.2929C35.8946 21.4804 36 21.7347 36 22V29Z" stroke="#111111" stroke-width="1.67" style="stroke: var(--black);" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
SVG,

 12 => <<<SVG
<svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
<rect width="56" height="56" rx="12" fill="#A7A7C9" fill-opacity="0.15"/>
<path d="M27 18V20M21 18V20M21 19H20C19.4696 19 18.9609 19.2107 18.5858 19.5858C18.2107 19.9609 18 20.4696 18 21V25C18 26.5913 18.6321 28.1174 19.7574 29.2426C20.8826 30.3679 22.4087 31 24 31M24 31C25.5913 31 27.1174 30.3679 28.2426 29.2426C29.3679 28.1174 30 26.5913 30 25V21C30 20.4696 29.7893 19.9609 29.4142 19.5858C29.0391 19.2107 28.5304 19 28 19H27M24 31C24 32.5913 24.6321 34.1174 25.7574 35.2426C26.8826 36.3679 28.4087 37 30 37C31.5913 37 33.1174 36.3679 34.2426 35.2426C35.3679 34.1174 36 32.5913 36 31V28M36 28C37.1046 28 38 27.1046 38 26C38 24.8954 37.1046 24 36 24C34.8954 24 34 24.8954 34 26C34 27.1046 34.8954 28 36 28Z" style="stroke: var(--black);" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
SVG,

 10 => <<<SVG
<svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
<rect width="56" height="56" rx="12" fill="#A7A7C9" fill-opacity="0.15"/>
<path d="M24.7141 30H21.0041C20.7944 30.0001 20.5901 30.0661 20.4199 30.1886C20.2498 30.3112 20.1225 30.4841 20.0561 30.683L18.0521 36.683C18.0018 36.8333 17.988 36.9934 18.0118 37.1501C18.0356 37.3068 18.0963 37.4556 18.1889 37.5842C18.2815 37.7128 18.4033 37.8176 18.5443 37.8899C18.6854 37.9622 18.8416 37.9999 19.0001 38H37.0001C37.1584 37.9999 37.3145 37.9621 37.4554 37.8899C37.5964 37.8177 37.7181 37.713 37.8107 37.5845C37.9033 37.456 37.964 37.3074 37.9879 37.1508C38.0118 36.9942 37.9981 36.8343 37.9481 36.684L35.9481 30.684C35.8817 30.4848 35.7543 30.3115 35.584 30.1888C35.4137 30.066 35.209 29.9999 34.9991 30H31.2871M34 24C34 27.613 30.131 31.429 28.607 32.795C28.4327 32.9282 28.2194 33.0003 28 33.0003C27.7806 33.0003 27.5673 32.9282 27.393 32.795C25.87 31.429 22 27.613 22 24C22 22.4087 22.6321 20.8826 23.7574 19.7574C24.8826 18.6321 26.4087 18 28 18C29.5913 18 31.1174 18.6321 32.2426 19.7574C33.3679 20.8826 34 22.4087 34 24ZM30 24C30 25.1046 29.1046 26 28 26C26.8954 26 26 25.1046 26 24C26 22.8954 26.8954 22 28 22C29.1046 22 30 22.8954 30 24Z" style="stroke: var(--black);" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
SVG,


];

  // --- вывод разметки ---
  ?>
  <div class="section">
    <div class="section__header">
      <h2 class="section__title">Категории обсуждений</h2>
      <a href=" https://forum.onkologia.ru/categories" class="section__link">Все
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path d="M7.5 15L12.5 10L7.5 5" style="stroke: var(--silver-grey);" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </a>
    </div>

    <div class="section__cards">
      <div class="cards">
        <?php foreach ($cats as $c):
          $id    = (int)($c['id'] ?? 0);
          $name  = $c['name'] ?? '';
          $slug  = $c['slug'] ?? '';
          $url   = $DISCOURSE_BASE . '/c/' . rawurlencode($slug) . '/' . $id;
          $icon  = $ICONS[$id] ?? $svgIconDefault;
          $count = (int)($c['topics_all_time'] ?? $c['topic_count'] ?? $c['topics_year'] ?? 0);
          $label = $count . ' ' . $decl($count, 'обсуждение', 'обсуждения', 'обсуждений');
        ?>
        <a class="cards__item" href="<?= $h($url) ?>" target="_blank" rel="noopener">
          <div class="cards__image"><?= $icon ?></div>
          <div class="cards__content">
            <span class="cards__title"><?= $h($name) ?></span>
            <span class="cards__text"><?= $h($label) ?></span>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php
} // end function
// вызвать где нужно:
render_categories_section();
