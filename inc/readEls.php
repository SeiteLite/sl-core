<?
function pruneNLs($s) {
  return trim(preg_replace('/^\h*\v+/m', '', $s));
}

// script comments and more
function compressCss($s) {
  $s = preg_replace('!/\*.*?\*/!s', '', $s);          // CSS comments
  $s = preg_replace('/\s+/', ' ', $s);                // whitespace
  $s = preg_replace('/\s*([{}|:;,])\s*/', '$1', $s);  // spaces around {}|:;, characters
  $s = preg_replace('/;}/', '}', $s);                 // last semicolon before }
  return pruneNLs($s);
}

// strip comments
function stripHtml($s) {
  $s = preg_replace('/<!--.*?-->/s', '', $s);         // HTML comments
  $s = preg_replace_callback(
    '/(<style\b[^>]*>)(.*?)(<\/style>)/is',           // <style> tags
    fn($m) => $m[1] . compressCss($m[2]) . $m[3],
    $s
  );
  return pruneNLs($s);
}

// strip comments
function stripJs($s) {
  $s = preg_replace('!//.*$!m', '', $s);              // single-line comments
  $s = preg_replace('!/\*.*?\*/!s', '', $s);          // multi-line comments
  return pruneNLs($s);
}


function readEls(string $tplDir1, string $tplDir2, string $jsDir, array $els) {
  $res = [];
  $custTags = []; // newly discovered

  foreach ($els as $el) {
    $cls = "$jsDir/$el.js"; // class

    $tpl = "$tplDir1/$el.php";  // template
    if (!is_file($tpl) || !is_file($cls)) {
      $tpl = "$tplDir2/$el.php";  // template
      if (!is_file($tpl) || !is_file($cls))
        continue;
    }

    try {
      ob_start();
      require $tpl;
    } finally {
      $tpl = stripHtml(ob_get_clean());
    }

    $cls = stripJs(file_get_contents($cls));

    $custTags = array_merge($custTags, customTags($tpl));
    $res[] = [$el, $tpl, $cls];
  }

  return [$custTags, $res];
}
