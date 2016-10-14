<a href="<?=URL_ROOT.$this->lang2.'/'.$this->routes['cart']; ?>" class="cart-counter-wrapper"><?=$cart["cart"]?><?php if(isset($_SESSION['cart']) && count($_SESSION['cart']['items'])): ?>
    <span class="cart-counter"><?php echo count($_SESSION['cart']['items']); ?></span><?php endif; ?>
</a>
<ul>
    <li>
        <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']['items'])): ?>
        <div class="cart-quickview">
            <div class="cart-quickview-inner">
                <?php foreach($_SESSION['cart']['items'] as $key => $cart_item): ?>
                <?php $cart_picture = current($cart_item['data']["item_pictures"]); ?>
                <table id="<?=$key?>">
                    <tr>
                        <td><a href="<?=URL_ROOT.$this->lang2."/".$this->routes["product"]."/".$cart_item['data']["products.slug_$lang3"]?>"><?php if($cart_item['data']["products.pic_t"]) : ?><img src="<?=URL_ROOT . PUBLIC_FOLDER . WBR_FOLDER;?><?=$helper->getPicturePath($cart_item['data']["products.pic_t"])?>" width="90" /><?php endif; ?></a></td>
                        <td>
                            <span class="product-title"><?php echo $cart_item['data']["products.name_$lang3"]; ?></span><br />
                            <span class="product-price"><span class="product-price-price"><?php echo number_format($cart_item['total_price'], 2); ?></span> $</span><br />
                            <span class="product-qty"><?php echo $cart['qty'] . ' : ';?><span class="product-qty-qty"><?=$cart_item['qty']['qty']; ?></span></span>
                        </td>
                    </tr>
                </table>
                <?php endforeach; ?>
                <span class="cart-subtotal"><?php echo $cart['sub_total'] . ' : ';?><span class="cart-subtotal-subtotal"><?=number_format($_SESSION['cart']['sub_total'], 2);?></span> $</span>
            </div>
            <a href="<?=URL_ROOT.$this->lang2.'/'.$this->routes['cart']; ?>" class="btn is-full-width has-no-margin"><?=$cart["see_cart"]?></a>
        </div>
        <?php endif; ?>
    </li>
</ul>
