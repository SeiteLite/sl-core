<?
// route to error pages
if (ERR_DIR == ($path[0] ?? '')) {
  $err = $path[1] ?? '';
  $msg = $path[2] ?? '';

  $err = urldecode($err);
  $msg = base64_decode($msg) ?? '';

  require FSL . 'pages/err.php';
  die;
}

if (!isDev()) {
  ini_set('display_errors', '0');
  ini_set('log_errors', '1');

  register_shutdown_function(function () {
    $error = error_get_last();
    if (E_ERROR == ($error['type'] ?? 0)) {
      error_log("Fatal error: " . $error['message']);
      header('Location: ' . SL . 'pages/err500.php');
      die;
    }
  });
}

set_error_handler(function ($severity, $msg, $file, $line) {
  if (error_reporting() & $severity)
    throw new ErrorException($msg, 0, $severity, $file, $line);
});

function checkVersion() {
  check(version_compare(PHP_VERSION, '8.3.0', '>='), 'Needs PHP version 8.3+');
  preg_match('!Apache/([0-9\.]+)!', $_SERVER['SERVER_SOFTWARE'] ?? '', $m);
  check(version_compare($m[1], '2.3', '>='), 'Needs Apache version 2.4+');
}

function errPage(Exception $e) {
  $err = $e->getCode();
  $msg = $e->getMessage();
  error_log("ERR: $err $msg");

  if (!($e instanceof SlErr) && !isDev())
    $msg = 'Internal Error';

  $err = urlencode($err);
  $msg = base64_encode($msg);

  header("Location: " . RT . ERR_DIR . "/$err/$msg");
  die;
}
