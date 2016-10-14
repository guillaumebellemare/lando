<ul class="order-steps">
    <li<?php if($_GET["page"]."/".$_GET["param1"]==$this->routes["checkout_billing-address"]) echo " class='is-selected'";?>><a href="<?=URL_ROOT . $this->lang2 . "/" . $this->routes["checkout_billing-address"]?>"><?=$cart["step_billing"]?></a></li>
    <li<?php if($_GET["page"]."/".$_GET["param1"]==$this->routes["checkout_shipping-address"]) echo " class='is-selected'";?>>
    <?php if($_GET["page"]."/".$_GET["param1"]==$this->routes["checkout_shipping-address"] || $_GET["page"]."/".$_GET["param1"]==$this->routes["checkout_order-confirmation"]) : ?>
    <a href="<?=URL_ROOT . $this->lang2 . "/" . $this->routes["checkout_shipping-address"]?>"><?=$cart["step_checkout"]?></a>
    <?php else: ?>
    <span><?=$cart["step_checkout"]?></span>
    <?php endif; ?>
    </li>
    <li<?php if($_GET["page"]."/".$_GET["param1"]==$this->routes["checkout_order-confirmation"]) echo " class='is-selected'";?>>
    <?php if($_GET["page"]."/".$_GET["param1"]==$this->routes["checkout_order-confirmation"]) : ?>
    <a href="<?=URL_ROOT . $this->lang2 . "/" . $this->routes["checkout_order-confirmation"]?>"><?=$cart["step_confirmation"]?></a>
    <?php else: ?>
    <span><?=$cart["step_confirmation"]?></span>
    <?php endif; ?>
    </li>
</ul>
<div class="clear"></div>
