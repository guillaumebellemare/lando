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