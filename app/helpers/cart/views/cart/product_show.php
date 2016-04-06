<?php include('app/views/layouts/two-columns.php') ?>
<?php startblock('content') ?>
<h1><?=$product["products.name_$lang3"]?></h1>
<img src="<?=URL_ROOT . PUBLIC_FOLDER . WBR_FOLDER . $helper->getPicturePath($product["products.pic_t"])?>" alt="<?=$product["products.name_$lang3"]?>">
<?=$product["products.description_$lang3"]?>
<?php endblock() ?>
<?php startblock('sidebar') ?>
<h1><?=$cart["commander"]?></h1>
<form action="<?=URL_ROOT . $lang2 . "/" . $routes["cart_add"]?>" method="POST" name="addToCart" id="addToCart" class="add-to-cart">
    <div id="message"></div>
    <label for="qty"><?=$cart["qty_full"]?></label>
    <select id="qty" name="qty">
    	<?php for ($i = 1; $i <= 10; $i++) : ?>
    	<option value="<?=$i?>"><?=$i?></option>
        <?php endfor; ?>
    </select>
    <input type="hidden" name="id" value="<?=$product["products.id"]?>">
    <input type="hidden" name="slug" value="<?=$product["products.slug_$lang3"]?>">
    <button type="submit" class="btn is-full-width"><?=$cart["add_to_cart"]?></button>
</form>
<?php endblock() ?>
<?php startblock('extended-scripts') ?>
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/flexslider.php')); ?>
<script src="<?=URL_ROOT?>app/helpers/cart/scripts/add-to-cart.js" type="text/javascript"></script>
<?php endblock() ?>
