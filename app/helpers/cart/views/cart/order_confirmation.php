<?php include('app/views/layouts/one-column.php') ?>
<?php startblock('content') ?>
<h1><?=$cart["step_confirmation"]?></h1>
<?php if(@include(COMPLETE_URL_ROOT . 'app/helpers/cart/includes/cart-sequence.php')); ?>
<form action="<?=URL_ROOT . $lang2 . "/" . $routes["checkout_order-confirmation"]?>" method="POST" id="fShippingCost" name="fShippingCost">
<h3><?=$cart["shipping_cost"]?></h3>
<select name="shipping_rates_select" id="shipping_rates_select" class="shipping_rates_select">
	<?php foreach($final_shipping_rates as $rate) : ?>
    <option value="<?=$rate["service_code"]?>" <?php if($rate["service_code"]==$_SESSION['cart']["calculated_shipping_rates"]) : ?> selected<?php endif; ?>><?=$rate["service_name"]." (".$rate["shipping_cost"]." $) - ".$app->writePrettyDate($rate["expected_delivery_date"])?></option>
    <?php endforeach; ?>
</select>
</form>
<form action="<?=URL_ROOT . $lang2 . "/" . $routes["checkout_order-confirmation"]?>" method="POST" name="fATC">
  <div>
    <?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/messages.php')); ?>
		<div class="l-grid l-row-2">
	      <div class="l-grid-50">
              <div class="cart-card">
                <h2><?=$cart["billed_to"]?></h2>
                <p>
                <?=$_SESSION['customer']['payment_first_name'] . ' ' . $_SESSION['customer']['payment_last_name']; ?><br />
                <?php if($_SESSION['customer']['payment_company']) echo $_SESSION['customer']['shipping_company'] . '<br />' ?>
                <?=$_SESSION['customer']['payment_address_1']; ?><br />
                <?php if($_SESSION['customer']['payment_address_2']) : ?><?=$_SESSION['customer']['payment_address_2']; ?><br /><?php endif; ?>
                <?=$_SESSION['customer']['payment_city'] . ', ' .$_SESSION['customer']['payment_province']; ?><br />
                <?=$_SESSION['customer']['payment_zip'];?> 
                </p>
                <p>
                <?= $_SESSION['customer']['payment_email']; ?><br />
                <?php if(isset($_SESSION['customer']['payment_phone'])) echo $_SESSION['customer']['payment_phone']; ?><br />
                </p>
              </div>
          </div>
	      <div class="l-grid-50">
              <div class="cart-card">
                <h2><?=$cart["send_to"]?></h2>
                <p>
                <?=$_SESSION['customer']['shipping_first_name'] . ' ' . $_SESSION['customer']['shipping_last_name']; ?><br />
                <?php if($_SESSION['customer']['shipping_company']) echo $_SESSION['customer']['shipping_company'] . '<br />' ?>
                <?=$_SESSION['customer']['shipping_address_1']; ?><br />
                <?php if($_SESSION['customer']['shipping_address_2']) : ?><?=$_SESSION['customer']['shipping_address_2']; ?><br /><?php endif; ?>
                <?=$_SESSION['customer']['shipping_city'] . ', ' .$_SESSION['customer']['shipping_province']; ?><br />
                <?=$_SESSION['customer']['shipping_zip']?>
                </p>
              </div>
          </div>
		</div>
		<?php if(!isset($_SESSION['customer']['id'])): ?>
		<input type="checkbox" name="create_account" <?php if(isset($_SESSION['customer']['create_account']) && ($_SESSION['customer']['create_account'])) echo 'checked'; ?>  id="create_account" value="yes">
		<label for="create_account"><?=$cart["create_account"]?></label>
		<div class="l-grid l-row-2" id="new_account_box">
	      <div class="l-grid-50">
	        <label for="password"><?=$cart["form_password"]?></label>
	        <input name="password" type="password" value="<?php if(isset($_SESSION['customer']['password'])) echo $_SESSION['customer']['password']; ?>" >
	      </div>
	      <div class="l-grid-50">
	        <label for="password_confirm"><?=$cart["form_password_confirm"]?></label>
	        <input name="password_confirm" type="password" value="<?php if(isset($_SESSION['customer']['password_confirm'])) echo $_SESSION['customer']['password_confirm']; ?>" >
	      </div>
		</div>
		<?php endif; ?>
        <hr>
	  <table class="is-full-table shopping-cart">
	    <tr>
	      <th><?=$cart["product"]?></th>
	      <th>Description</th>
	      <th class="th-price"><?=$cart["price"]?></th>
	      <th class="th-qty"><?=$cart["qty"]?></th>
	      <th class="th-total">Total</th>
	    </tr>
	    <?php foreach($cart_session['items'] as $key => $item): ?>
	    <tr class="item<?=(isset($item['invalid']) ? ' error' : '');?>" id="<?=$key?>">
	      <td><a href="<?=URL_ROOT.$lang2."/".$routes["product"]."/".$item['data']["products.slug_$lang3"]?>" target="_blank"><img src="<?=URL_ROOT . PUBLIC_FOLDER . WBR_FOLDER . $app->getPicturePath($item['data']["products.pic_t"])?>" class="is-full-width"></a></td>
          <td><a href="<?=URL_ROOT.$lang2."/".$routes["product"]."/".$item['data']["products.slug_$lang3"]?>" target="_blank"><?=$item['data']["products.name_$lang3"]?></a></td>
		  <td class="price"><?php if(!empty($item['price'])) echo number_format($item['price'], 2) . ' $'; ?></td>
	      <td class="qty">
	      	<?php $qty_type = 'qty';
	      	$qty_value = $item['qty'][$qty_type]; ?>
			<?php if(is_array($qty_value)): ?>
	  			<?php foreach($qty_value as $qty_option => $qty_option_value):?>
	  			<?php if($qty_option_value): ?>
	  		<label><?=$qty_option;?> : <?=$qty_option_value;?></label>
	  			<?php endif; ?>
	  			<?php endforeach; ?>
	  		<?php else: ?>
	  		<label><?=$qty_value;?></label>
	  		<?php endif; ?>
	      </td>
	      <td class="total"><?=number_format($item['total_price'], 2);?> $</td>
	    </tr>
	    <?php endforeach; ?>
        </table>
        <table class="shopping-cart shopping-cart-total">
	    <tr>
	      <td colspan="3">&nbsp;</td>
	      <td colspan="2"><?=$cart["sub_total"]?> </td>
	      <td class="grand_total"><?=number_format($cart_session['sub_total'], 2)?> $</td>
	    </tr>
        <?php if($final_shipping_rates) : ?>
        <tr class="shipping_rates">
          <td colspan="3">&nbsp;</td>
          <td colspan="2"><?=$cart["delivery_cost"]?><br>
            <?php foreach($final_shipping_rates as $rate) : ?>	
                <?php if($rate["service_code"]==$_SESSION['cart']["calculated_shipping_rates"]) : ?>
                <?=$rate["service_name"]." - ".$app->writePrettyDate($rate["expected_delivery_date"])?>
                <?php endif; ?>
            <?php endforeach; ?>
          </td>
          <td class="shipping_cost">
            <?php foreach($final_shipping_rates as $rate) : ?>	
                <?php if($rate["service_code"]==$_SESSION['cart']["calculated_shipping_rates"]) : ?>
                <span class="shipping_rate_estimate"><?=($rate["shipping_cost"])?></span> $
                <input type="hidden" class="shipping_rates_estimate" name="shipping_rates_estimate" value="<?=$rate["shipping_cost"]?>" />
                <?php endif; ?>
            <?php endforeach; ?>
          </td>
        </tr>
        <?php endif; ?>
	    <?php foreach($cart_session['taxes'] as $tax_name => $tax_data):?>
		<tr>
	      <td colspan="3">&nbsp;</td>
	      <td colspan="2"><?=$tax_name;?></td>
	      <td class="grand_total"><?=number_format($tax_data['amount'], 2)?> $</td>
	    </tr>
		<?php endforeach; ?>
	    <tr>
	      <td colspan="3">&nbsp;</td>
	      <td colspan="2">Total</td>
	      <td class="grand_total"><?=number_format($cart_session['total'], 2)?> $</td>
	    </tr>
	  </table>
     <!-- PayPal Logo --><table border="0" class="paypal-logo" cellpadding="10" cellspacing="0" align="right"><tr><td align="center"></td></tr><tr><td align="right"><a href="https://www.paypal.com/webapps/mpp/paypal-popup" title="How PayPal Works" onclick="javascript:window.open('https://www.paypal.com/webapps/mpp/paypal-popup','WIPaypal','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1060, height=700'); return false;"><img src="https://www.paypalobjects.com/webstatic/mktg/logo/bdg_payments_by_pp_2line.png" border="0" alt="Payments by PayPal"></a><div style="text-align:center"><a href="https://www.paypal.com/webapps/mpp/how-paypal-works"></a></div></td></tr></table><!-- PayPal Logo -->
      <div class="l-grid l-row-2">
          <div class="l-grid-50"><a href="<?=URL_ROOT.$lang2."/".$routes["checkout_billing-address"]?>" class="btn is-full-width"><?=$cart["modify"]?></a></div>
	      <div class="l-grid-50"><button type="submit" class="btn is-full-width" name="continue"><?=$cart["step_confirmation"]?></button></div>
      </div>
  </div>
</form>
<?php endblock() ?>
<?php startblock('extended-scripts') ?>
<script>
$( document ).ready(function() {
	$('input[name=create_account]').on('change', function (e){
		if($(this).is(":checked")) $("#new_account_box").show();
		else  $("#new_account_box").hide();
	}).trigger("change");
	
	$('select[name=shipping_rates_select]').on('change', function (e){
		$(this).parents("form").submit();
	});
});
</script>
<?php endblock() ?>
