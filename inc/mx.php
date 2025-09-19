<? require __DIR__ . '/mx-impl.php';

function mxOneLine($mx) {
  return mx\mxOneLine($mx);
}

function mx($mx) {
  echo mx\mx($mx);
}

function mx_div($mx, $extra = '') {
  if ($extra) $extra = " $extra";
  echo "<div$extra>" . mx\mx($mx) . "</div>";
}

function p_name($mx) {
  echo "<p-name>" . mx\mx($mx) . "</p-name>";
}

function p_text($mx) {
  echo "<p-text>" . mx\mx($mx) . "</p-text>";
}

function p_text1($mx) {
  echo "<p-text1>" . mx\mx($mx) . "</p-text1>";
}

function p_text2($mx) {
  echo "<p-text2>" . mx\mx($mx) . "</p-text2>";
}

function p_img($url, $alt = '') {
  if ($alt) $alt = " alt='$alt'";
  echo "<p-img src='$url'$alt></p-img>";
}
