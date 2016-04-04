<div>
	<div id="content"><?php echo $msg; ?></div>
	<div id="item_count"><?php echo $item_count; ?></div>
	<?php if(@include(COMPLETE_URL_ROOT . 'app/helpers/cart/includes/cart-quickview.php')); ?>
</div>