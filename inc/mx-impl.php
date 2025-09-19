<?

namespace mx;

// If MX code ($mx) comes from the server side,
// use this function to prepare the code for sending to the client.
function mxOneLine($mx) {
  return addslashes(mxNormalize($mx));
}

function mxNormalize($mx) {
  // sanitize line ends + trim
  $mx = preg_split('/$\R?^/m', $mx);
  $mx = array_map('trim', $mx);
  $mx = implode("\n", $mx);

  // empty lines = line breaks
  $mx = preg_replace('/\n{2,}/m', '[br]', $mx);
  // away with line ends
  $mx = str_replace("\n", ' ', $mx);
  return trim($mx);
}

function readTo(&$tx, ...$chars) {
  $pos = -1;

  // the closest one of them
  foreach ($chars as $c) {
    $p = strpos($tx, $c);
    if ($p !== false && ($pos < 0 || $p < $pos))
      $pos = $p;
  }

  $head = '';
  if ($pos < 0) {
    $head = $tx;
    $tx = '';
  } else {
    $head = substr($tx, 0, $pos);
    $tx = substr($tx, $pos);
  }

  // the part before
  return $head;
}

function skip(&$tx) {
  $tx = substr($tx, 1);
}

function is($c, &$tx) {
  return $tx && $c == $tx[0];
}

function splitTag($tagClsPar) {
  // param
  $parts = explode('/', $tagClsPar);
  $tagCls = $parts[0] ?? '';
  $par = $parts[1] ?? '';

  // class
  $tagParts = explode('.', $tagCls);
  $tag = $tagParts[0] ?? '';
  $cls = $tagParts[1] ?? '';

  return [$tag, $cls, $par];
}

function unquote($tx) {
  return str_replace(['&quot;', '&#39;'], ['"', "'"], $tx);
}

function parseTag($inTag, $tx, $arg) {
  if (!$inTag) return $tx;
  list($tag, $cls, $par) = splitTag($inTag);

  // empty tag is <span>
  $tag = $tag ?: 'span';

  $cls = $cls ? ' class="' . $cls . '"' : '';

  // pars: Array, par: string
  $pars = array_filter(explode(' ', unquote($par)));

  // par: passed on literally
  $par = implode(' ', $pars);
  $par = $par ? ' ' . $par : '';

  $arg = $arg ?: '';

  // comment
  if ($tag[0] == '-') return '';

  // special tags
  switch ($tag) {
    case 'star':
      return '<sup>*</sup>';
    case 'br':
      return '<br>';
    case 'br2':
      return '<br><br>';
    case 'hr':
      return "<hr$cls>";
    case 'a':
      $tx = $tx ?: $arg;
      $arg = $arg ?: $tx;
      $ps = in_array('blank', $pars) ? ' target="_blank"' : '';
      return "<a$cls href=\"$arg\"$ps>$tx</a>";
    case 'mailto':
      $tx = $tx ?: $arg;
      $arg = $arg ?: $tx;
      return "<a$cls href=\"mailto:$arg\">$tx</a>";
    case 'tel':
      $tx = $tx ?: $arg;
      $arg = $arg ?: $tx;
      $arg = str_replace(' ', '', $arg);
      return "<a$cls href=\"tel:$arg\">$tx</a>";
    case 'img':
      return "<img$cls$par src=\"$arg\" alt=\"\">";
    case 'th2':
      return "<th colspan=\"2\"$cls>$tx</th>";
    case 'td2':
      return "<td colspan=\"2\"$cls>$tx</td>";
  }

  // allowed tags
  $allowedTags = [
    'div' => 'div',
    'span' => 'span',
    'b' => 'b',
    'i' => 'i',
    'u' => 'u',
    'h1' => 'h1',
    'h2' => 'h2',
    'h3' => 'h3',
    'ul' => 'ul',
    'ol' => 'ol',
    'li' => 'li',
    'row' => 'flex-row',
    'table' => 'table',
    'tr' => 'tr',
    'th' => 'th',
    'td' => 'td',
    // custom element tags
    'btn' => 'x-btn',
    'icon' => 'x-icon',
  ];

  // allowed tags
  if (!($t = $allowedTags[$tag] ?? null))
    // ignore the tag and use the content as is
    return $tx;

  // formatted tag
  $arg = $arg ? ' ' . $arg : '';
  return "<$t$cls$par$arg>$tx</$t>";
}

function readTag(&$tx) {
  $tag = '';
  $par = ''; // parameter
  $head = readTo($tx, '[', ']');

  if ($tx) switch ($tx[0]) {
    // MX tag starts
    case '[':
      skip($tx);
      $tag = trim(readTo($tx, ':', ']'));
      if (is(':', $tx)) {
        skip($tx);
        // skip the first space after :
        if (is(' ', $tx)) skip($tx);
      }
      break;
    // MX tag ends
    case ']':
      skip($tx);
      $tag = ']';
      // optional parameter (...)
      if (is('(', $tx)) {
        skip($tx);
        $par = trim(readTo($tx, ')'));
        skip($tx);
      }
      break;
  }

  return [$head, $tag, $par];
}

function parse($inTag, &$tx) {
  $typo = [
    // @ is a special mark for typography
    // typographical quotes
    '@34' => '"',
    '@39' => "'",
    // _ -> nbsp
    'nbsp' => '_',
    // Non-Breaking Hyphen
    '#8209' => '~'
  ];

  $out = '';
  while (true) {
    list($head, $tag, $par) = readTag($tx);
    foreach ($typo as $entity => $rx)
      $head = str_replace($rx, "&{$entity};", $head);
    $out .= $head;
    if (!$tag) return $out;
    if (']' == $tag) return parseTag($inTag, $out, $par);
    $out .= parse($tag, $tx);
  }
};


function mx($tx) {
  $tx = mxNormalize($tx);

  $chrs = [
    'amp' => '&',
    'lt' => '<',
    'gt' => '>',
    // escaped quotes
    '#34' => '\\"',
    '#39' => "\\'",
    // escaped [:]() chars, used in MX tags
    '#91' => '\\[',
    '#58' => '\\:',
    '#93' => '\\]',
    '#40' => '\\(',
    '#41' => '\\)',
    '#95' => '\\_',
    '#126' => '\\~'
  ];

  foreach ($chrs as $entity => $rx)
    $tx = str_replace($rx, "&{$entity};", $tx);

  // escape the colon in urls, e.g. https://
  $tx = preg_replace('/(?<=(?:[a-zA-Z0-9])):\/\//', '&#58;//', $tx);


  // TODO based on language
  $quotes = [
    '&@34;' => ['„', '“'],
    '&@39;' => ['‚', '‘'],
  ];

  $typography = function ($tx) use ($quotes) {
    foreach ($quotes as $e => $q) {
      $i = 0;
      do {
        $tx0 = $tx;
        // replace only one occurence
        $pos = strpos($tx, $e);
        if ($pos !== false)
          $tx = substr_replace($tx, $q[$i], $pos, strlen($e));
        $i = $i ? 0 : 1;
      } while ($tx0 != $tx);
    }
    return $tx;
  };

  return $typography(parse('', $tx));
}
