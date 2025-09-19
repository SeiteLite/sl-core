<?
// --- define paths, with leading and trailing slashes ---

// file system, absolute paths
define('WSR', realpath($_SERVER['DOCUMENT_ROOT']) . '/');             // web server root
define('FRT', dirname(realpath($_SERVER['SCRIPT_FILENAME'])) . '/');  // website root (with index.php)
define('FSL', realpath(__DIR__) . '/');                               // SeiteLite framework root

// corresponding relative paths
define('RT', substr(FRT, strlen(WSR) - 1));                           // website root
define('SL', RT . substr(FSL, strlen(FRT)));                          // SeiteLite root

// website URL root
define('URT', ('on' == ($_SERVER['HTTPS'] ?? '') ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . RT);

// --- defines ---

define('ERR_DIR', 'err');

// --- common utilities ---

// escape for HTML
function h(string $s): string {
  return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

// escape for JS
function j(string $s): string {
  return json_encode($s, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

// generic tag with content and extra attrs
function tag($tag, $content, $extra = '') {
  $extra && $extra = " $extra";
  return "<$tag$extra>$content</$tag>";
}

// script tag with content
function script($content) {
  return tag('script', $content);
}

// get the set of custom tags, quick and dirty
function customTags(string $html): array {
  return array_unique(
    preg_match_all('/<([a-z][a-z0-9]*-[^>\s]+)/i', strtolower($html), $m) ? $m[1] : [],
  );
}

// include file with TODO optional URL path
function incFile(string $file /* TODO, $path = []*/): bool {
  // these vars are available in the included file: $file, $path
  return false !== @include $file;
}

// --- config ---

// get config value
function conf($key, $def = null): mixed {
  global $conf;
  return $conf[$key] ?? $def;
}

// is dev mode?
function isDev(): bool {
  return conf('dev', false) && ini_get('display_errors');
}

// cache busting in development mode
function bust(): string {
  return isDev() ? '?bust=' . time() : '';
}

// --- routing ---

// array of path legs
$path = explode('/', $_REQUEST['path'] ?? '');
$path = array_map('trim', $path);

// --- error handling ---

// SeiteLite exceptions
class SlErr extends Exception {
}

// throw error
function err(string $msg, int $code = 500) {
  throw new SlErr($msg, $code);
}

// runtime check
function check($expr, $msg = 'runtime check') {
  if (!$expr) err($msg);
}

// error pages
require FSL . 'err.php';

// --- autoload classes ---
spl_autoload_register(function (string $cls) {
  // namespace to path
  $file = str_replace('\\', '/', $cls) . '.php';
  // search class dirs
  foreach ([FRT, FSL] as $dir)
    if (incFile($dir . 'cls/' . $file))
      return true;
  return false;
});

// --- let's go ---

// resolve route
function route(array $route, array $path): false|array {
  for (;;) {
    if (!($head = ($path[0] ?? '')))      // no more path
      break;
    if (!($leg = ($route[$head] ?? '')))  // no route
      break;
    array_shift($path);
    if (!is_array($leg))                  // route end
      return [$leg, $path];               // target file + remaining path
    return route($leg, $path);            // dive in
  }

  return ['index', $path];                // default
}

try {
  // resolve route
  [$file, $path] = route($route, $path);
  // check for all but error pages
  if (ERR_DIR != ($path[0] ?? '')) checkVersion();
  // include page
  $pages = conf('pages', 'pages/');
  incFile(FRT . "$pages$file.php", $path) or err('Page \'' . $file . '\' not found', 404);
} catch (Exception $e) {
  errPage($e);
}
