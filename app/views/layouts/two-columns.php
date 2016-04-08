<?php if(@include(COMPLETE_URL_ROOT . 'app/core/app_messages.php')); ?>
<!doctype html>
<html>
<head>
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/meta.php')) ?>
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/header.php')) ?>
<?php startblock('extended-styles') ?><?php endblock() ?>
</head>
<body lang="<?=$lang2?>">
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/analyticstracking.php')); ?>
<div id="l-wrap">
	<?php startblock('header') ?>
    <header>
      <div class="wrapper">
        <?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/nav.php')); ?>
        <ul class="languages is-no-list is-inline-list">
          <?php foreach($possible_languages as $language => $code) : ?>
              <?php if($language!=$lang2) : ?>
              <li><a href="<?=URL_ROOT.$language.Translate::translateFromPage($language, $routes)?>" title="<?=strtoupper($language)?>"><?=strtoupper($language)?></a></li>
              <?php endif; ?>
          <?php endforeach; ?>
        </ul>
        <div class="clear"></div>
        <?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/slider.php')); ?>
      </div>
    </header>
    <?php endblock() ?>
    <div class="v-spacer">
      <div class="wrapper">
        <div class="l-grid l-row-2">
          <div class="l-grid-65">
            <section>
			<?php startblock('content') ?>
			<?php endblock() ?>
            </section>
          </div>
          <div class="l-grid-35">
            <section>
			<?php startblock('sidebar') ?>
			<?php endblock() ?>
            </section>
          </div>
        </div>
      </div>
	</div>
</div>
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/footer.php')); ?>
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/scripts.php')); ?>
<?php startblock('extended-scripts') ?><?php endblock() ?>
</body>
</html>