<div>
<?=$_POST["shipping_rates_select"]?>
<?php foreach($cart_session['calculated_shipping_rates'] as $rate) : ?>	
	<?=$rate["shipping_cost"]?>
<?php endforeach; ?>
</div>
