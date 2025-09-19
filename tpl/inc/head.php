<!DOCTYPE html>
<html lang="<?= $this->lang ?>">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <title><?= $this->title ?></title>
  <link rel="icon" type="image/svg+xml" href="<?= $this->logo ?>" />

  <meta name="description" content="<?= $this->description ?>" />
  <meta name="robots" content="<?= $this->robots ?>" />

  <?
  $isDev = (int)isDev();
  $bust = bust();
  echo script("var SLG={SL:'" . SL . "', isDev:$isDev, bust:'$bust'}");
  ?>

  <? require __DIR__ . '/font.php' ?>

  <link rel="stylesheet" href="<?= SL . 'css/main.css' . $bust ?>" />
  <link rel="stylesheet" href="<?= SL . 'css/els.css' . $bust ?>" title="shadow" />
  <? if (is_file("$tplBase.css")) { ?>
    <link rel="stylesheet" href="<?= "$tplPath.css" . $bust ?>" />
  <? } ?>

  <script src="<?= RT . 'dist/main.js' . $bust ?>"></script>
  <? if (is_file("$tplBase.js")) { ?>
    <script src="<?= "$tplPath.js" . $bust ?>"></script>
  <? } ?>
</head>

<body>
  <?
  require FSL . 'inc/readEls.php';
  while ($this->elsTodo) {
    [$newTags, $els] = $this->readEls();
    foreach ($els as [$tag, $tpl, $cls]) {
      echo "<template id='$tag'>$tpl\n</template>\n";
      echo "<script type='module'>defElem('$tag', $cls)</script>\n";
    }
    $this->elsTodo = $newTags;
  }
  ?>
