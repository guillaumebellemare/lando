<form id="estimate_shipping_cost" name="estimate_shipping_cost" action="<?=URL_ROOT . $lang2 . "/" . $routes["cart"]?>" method="POST">
    <h1>Estimer les coûts de livraison</h1>
    <label for="zip_code_estimate">Code postal</label>
    <input id="zip_code_estimate" name="zip_code_estimate" value="<?php if($cart_session["zip_code_estimate"]) echo $cart_session["zip_code_estimate"]; ?>" type="text">
    <select name="shipping_rates_select" class="shipping_rates_select">
    	<?php foreach($shipping_rates as $rate) : ?>	
        	<option value="<?=$rate["service_code"]?>" price="<?php echo $rate["shipping_cost"]; ?>" name="<?php echo $rate["service_name"]; ?>" estimated_date="<?php echo $app->writePrettyDate($rate["expected_delivery_date"]); ?>"<?php if($rate["service_code"] == $_SESSION['cart']["shipping_rates_estimate"]) echo ' selected="selected"'; ?>><?=$rate["service_name"]." (".$rate["shipping_cost"]." $) - ".$app->writePrettyDate($rate["expected_delivery_date"])?></option>
        <?php endforeach; ?>
    </select>
    <input name="action" type="hidden" value="estimate">
    <button type="submit" class="btn is-full-width" name="btn_estimate">Estimer</button>
</form>
