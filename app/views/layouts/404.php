<?php if(@include(COMPLETE_URL_ROOT . 'app/core/app_messages.php')); ?>
<!doctype html>
<html>
<head>
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/meta.php')) ?>
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/header.php')) ?>
<?php startblock('extended-styles') ?>
<?php endblock() ?>
</head>
<body>
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/analyticstracking.inc.php')); ?>
<div id="l-wrap">
  <?php startblock('header') ?>
  <header>
    <section></section>
  </header>
  <?php endblock() ?>
  <div class="wrapper">
    <section>
      <?php startblock('content') ?>
      <?php endblock() ?>
    </section>
  </div>
</div>
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/footer.php')); ?>
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/scripts.php')); ?>
<?php startblock('extended-scripts') ?>
<?php endblock() ?>
</body>
</html>