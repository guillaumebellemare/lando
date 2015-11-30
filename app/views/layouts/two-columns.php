<?php if(@include(COMPLETE_URL_ROOT . 'app/core/app_messages.php')); ?>
<!doctype html>
<html>
<head>
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/meta.php')) ?>
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/header.php')) ?>
<?php startblock('extended-styles') ?><?php endblock() ?>
</head>
<body>
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/analyticstracking.php')); ?>
<div id="l-wrap">
  <?php startblock('header') ?>
  <header>
    <section>
      <ul class="languages is-no-list is-inline-list"><?php foreach($possible_languages as $language => $code) : ?><?php if($language!=$lang2) : ?><li><a href="<?=URL_ROOT.$language.Translate::translateFromPage($language)?>" title="<?=strtoupper($language)?>"><?=strtoupper($language)?></a></li><?php endif; ?><?php endforeach; ?></ul>
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