<?php if(@include('includes/app_messages.inc.php')); ?>
<!doctype html>
<html>
<head>
<?php if(@include('includes/meta.inc.php')) ?>
<?php if(@include('includes/header.inc.php')) ?>
<?php startblock('extended-styles') ?>
<?php endblock() ?>
</head>
<body>
<?php if(@include('includes/analyticstracking.inc.php')); ?>
<div id="l-wrap">
  <?php startblock('header') ?>
  <header>
    <section>
      <?php include('includes/nav.inc.php'); ?>
      <a href="<?php echo URL_ROOT.$lang2_trans; ?>/">Langue</a>
      <div class="l-grid">
        <div class="l-grid-100">
          <?php if(@include('includes/slider.inc.php')); ?>
        </div>
      </div>
    </section>
  </header>
  <?php endblock() ?>
  <div class="wrapper">
    <section>
      <div class="l-grid l-row-1">
        <div class="l-grid-100">
          <?php startblock('content') ?>
          <?php endblock() ?>
        </div>
      </div>
    </section>
  </div>
</div>
<?php if(@include('includes/footer.inc.php')); ?>
<?php if(@include('includes/scripts.inc.php')); ?>
<?php startblock('extended-scripts') ?>
<?php endblock() ?>
</body>
</html>