<? $this->els(['s-ico']) ?>
<? include FSL . '/tpl/inc/head.php' ?>
<?
$pagesUrl = URT;
$fetchPhp = URT . $this->fetchPhp . '/';
?>
<?= script("SLG.nav={basePath:'{$this->basePath}',pagesUrl:'$pagesUrl',fetchPhp:'$fetchPhp'}") ?>
<aside>
  <? if (conf('header')) { ?>
    <header>
      <span id="menu"><s-ico arrow-left></s-ico></span>
    </header>
  <? } else { ?>
    <span id="menu"><s-ico arrow-left></s-ico></span>
  <? } ?>
  <nav>
    <?= $this->nav ?>
  </nav>
  <? if (conf('footer')) { ?>
    <footer>
    </footer>
  <? } ?>
</aside>

<main>
  <? if (conf('header')) { ?>
    <header>
      <span id="menu"><s-ico menu></s-ico></span>
      <span><?= $this->title ?></span>
    </header>
  <? } else { ?>
    <span id="menu"><s-ico menu></s-ico></span>
  <? } ?>
  <article>
    <?= $this->body ?>
  </article>
  <? if (conf('footer')) { ?>
    <footer>
    </footer>
  <? } ?>
</main>

<? include FSL . '/tpl/inc/foot.php' ?>
