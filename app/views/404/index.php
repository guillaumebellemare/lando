<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/app_messages.inc.php')); ?>
<!doctype html>
<html>
<head>
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/meta.inc.php')) ?>
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/header.inc.php')) ?>
<?php startblock('extended-styles') ?>
<?php endblock() ?>
</head>
<body>
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/analyticstracking.inc.php')); ?>
<div id="l-wrap">
  <?php startblock('header') ?>
  <?php endblock() ?>
  <div class="wrapper">
    <section>
      <div class="l-grid l-row-1">
        <div class="l-grid-100">
          <?php startblock('content') ?>
          	<p>Erreur 404</p>
          <?php endblock() ?>
        </div>
      </div>
    </section>
  </div>
</div>
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/footer.inc.php')); ?>
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/scripts.inc.php')); ?>
<?php startblock('extended-scripts') ?>
<?php endblock() ?>
</body>
</html>