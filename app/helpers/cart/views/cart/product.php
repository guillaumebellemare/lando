<?php include('app/views/layouts/one-column.php') ?>
<?php startblock('content') ?>
<h1><?=$cart["product"]?></h1>
<div class="l-grid l-row-4">
<?php foreach($products as $product): ?>
	<div class="l-grid-25"><a href="<?=URL_ROOT.$lang2."/".$routes["product"]."/".$product["products.slug_$lang3"]?>" class="cart-card"><img src="<?=URL_ROOT . PUBLIC_FOLDER . WBR_FOLDER . $helper->getPicturePath($product["products.pic_t"])?>" class="is-full-width" alt="<?=$product["products.name_$lang3"]?>"><?=$product["products.name_$lang3"]?></a></div>
<?php endforeach; ?>
</div>
<div class="clear"></div>
<?php endblock() ?>
<?php startblock('extended-scripts') ?>
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/flexslider.php')); ?>
<?php endblock() ?>
