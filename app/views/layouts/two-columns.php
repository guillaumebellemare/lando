<?php if(@include(COMPLETE_URL_ROOT . 'app/core/app_messages.php')); ?>
<!doctype html>
<html>
<head>
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/meta.php')) ?>
<meta property="og:type" content="<?php startblock('og:type') ?>website<?php endblock() ?>" />
<meta property="og:image" content="<?php startblock('og:image') ?><?php endblock() ?>" />
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/header.php')) ?>
<?php startblock('extended-styles') ?>
<?php endblock() ?>
</head>
<body>
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/analyticstracking.php')); ?>
<div id="l-wrap">
  <?php startblock('header') ?>
  <header>
    <section>
      <a href="<?=URL_ROOT.$lang2_trans.Translate::translateFromPage()?>"><?=$global["lang"]?></a>
      <?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/nav.php')); ?>
      <div class="l-grid">
        <div class="l-grid-100">
          <?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/slider.php')); ?>
        </div>
      </div>
    </section>
  </header>
  <?php endblock() ?>
  <div class="wrapper">
    <section>
      <div class="l-grid l-row-2">
        <div class="l-grid-65">
          <?php startblock('content') ?>
          <?php endblock() ?>
        </div>
        <div class="l-grid-35">
          <?php startblock('sidebar') ?>
          <?php endblock() ?>
        </div>
      </div>
    </section>
  </div>
</div>
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/footer.php')); ?>
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/scripts.php')); ?>
<?php startblock('extended-scripts') ?>
<?php endblock() ?>
</body>
</html>