<!doctype html>
<html>
<head>
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
            <?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/nav-lang.php')); ?>
            <div class="clear"></div>
        </div>
    </header>
    <?php endblock() ?>
    <div class="v-spacer">
        <div class="wrapper">
            <section>
			<?php startblock('content') ?>
			<?php endblock() ?>
            </section>
        </div>
    </div>
</div>
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/footer.php')); ?>
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/scripts.php')); ?>
<?php startblock('extended-scripts') ?><?php endblock() ?>
</body>
</html>