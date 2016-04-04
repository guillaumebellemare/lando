<?php include('app/views/layouts/one-column.php') ?>
<?php startblock('content') ?>
<h1><?=$cart["cart"]?></h1>
<?php if(isset($cart_session['items']) && count($cart_session['items'])) : ?>
<form id="order" name="order" action="<?=URL_ROOT . $lang2 . "/" . $routes["cart"]?>" method="POST">
  <div id="message"></div>
  <?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/messages.php')); ?>
  <table class="shopping-cart">
    <tr>
      <th><?=$cart["product"]?></th>
      <th>Description</th>
      <th class="th-price"><?=$cart["price"]?></th>
      <th class="th-qty"><?=$cart["qty"]?></th>
      <th></th>
      <th class="th-total">Total</th>
    </tr>
    <?php foreach($cart_session['items'] as $key => $item): ?>
    <tr class="item<?=(isset($item['invalid']) ? ' error' : '');?>" id="<?=$key?>">
      <td><a href="<?=URL_ROOT.$lang2."/".$routes["product"]."/".$item['data']["products.slug_$lang3"]?>"><img src="<?=URL_ROOT . PUBLIC_FOLDER . WBR_FOLDER . $app->getPicturePath($item['data']["products.pic_t"])?>" alt="<?=$item['data']["products.name_$lang3"]; ?>" class="is-full-width"></a></td>
      <td><a href="<?=URL_ROOT.$lang2."/".$routes["product"]."/".$item['data']["products.slug_$lang3"]?>"><span class="product-name"><?=$item['data']["products.name_$lang3"]; ?></span></a></td>
      <td class="price"><input name="price" value="<?=($item['price']);?>" type="hidden">
      <?php 
      if(!empty($item['price'])): echo number_format($item['price'], 2) . ' $';
      endif;
       ?>
      </td>
      <td class="qty">
      	<?php
			$qty_type = 'qty';
			$qty_value = $item['qty'][$qty_type];
		?>
		<?php if(is_array($qty_value)): ?>
  			<?php foreach($qty_value as $qty_option => $qty_option_value):?>
  				<label><?=$qty_option;?><input name="items[<?=$key?>][<?=$qty_type?>][<?=$qty_option?>]" value="<?=$qty_option_value;?>" type="number"></label>
  			<?php endforeach; ?>
  		<?php else: ?>
  		<label><input name="items[<?=$key?>][<?=$qty_type?>]" value="<?=$qty_value;?>" type="number"></label>
  		<?php endif; ?>
      </td>
      <td>
      	<ul class="actions has-no-bullets">
      		<li><a href="#" class="action-remove"><?=$cart["delete"]?></a></li>
        </ul>
      </td>
      <td class="total"><?=number_format($item['total_price'], 2);?> $</td>
    </tr>
    <?php endforeach; ?>
    </table>
    <table class="shopping-cart shopping-cart-total">
    <tr>
      <td colspan="3">&nbsp;</td>
      <td colspan="2"><?=$cart["sub_total"]?></td>
      <td class="sub_total price"><?=number_format($cart_session['sub_total'], 2)?> $</td>
    </tr>
	<tr class="shipping_rates">
      <td colspan="3">&nbsp;</td>
      <td colspan="2"><?=$cart["delivery_cost_estimate"]?><br>
        <?php foreach($shipping_rates as $rate) : ?>	
        	<?php if($rate["service_code"]==$_SESSION['cart']["shipping_rates_estimate"]) : ?>
			<span class="name_and_date"><?=$rate["service_name"]." - ".$app->writePrettyDate($rate["expected_delivery_date"])?></span>
            <?php endif; ?>
        <?php endforeach; ?>
      </td>
      <td class="shipping_cost">
        <?php foreach($shipping_rates as $rate) : ?>	
        	<?php if($rate["service_code"]==$_SESSION['cart']["shipping_rates_estimate"]) : ?>
			<span class="shipping_rate_estimate"><?=($rate["shipping_cost"])?></span> $
        	<input type="hidden" class="shipping_rates_estimate" name="shipping_rates_estimate" value="<?=$rate["shipping_cost"]?>" />
            <?php endif; ?>
        <?php endforeach; ?>
      </td>
    </tr>
    <?php foreach($cart_session['taxes'] as $tax_name => $tax_data):?>
	<tr>
      <td colspan="3">&nbsp;</td>
      <td colspan="2"><?=$tax_name;?></td>
      <td class="tax price">
      	<?=number_format($tax_data['amount'], 2)?> $
      	<input type="hidden" name="<?=$tax_name;?>_rate" value="<?=$tax_data['rate'];?>" />
      </td>
    </tr>
	<?php endforeach; ?>
    <tr>
      <td colspan="3">&nbsp;</td>
      <td colspan="2">Total</td>
      <td class="grand_total"><?=number_format($cart_session['total'], 2)?> $</td>
    </tr>
  </table>
  <div class="l-grid l-row-2">
    <div class="l-grid-55"><a href="#estimate_shipping_cost" id="trigger-estimate-shipping-cost" class="btn is-full-width">Estimer les coûts de livraison</a></div>
    <div class="l-grid-45">
      <div class="l-grid l-row-2">
      	<div class="l-grid-50"><button type="submit" class="btn is-full-width" name="empty_cart"><?=$cart["empty"]?></button></div>
      	<div class="l-grid-50"><button type="submit" class="btn is-full-width" name="checkout_billing-address"><?=$cart["commander"]?></button></div>
      </div>
    </div>
  </div>
</form>
<?php @include("estimate_shipping_rates.php"); ?>
</div>
<?php else: ?>
<p><?=$cart['message_cart_is_empty']?></p>
<?php endif; ?>
<?php endblock() ?>
<?php startblock('extended-scripts') ?>
<script src="<?=URL_ROOT . PUBLIC_FOLDER?>scripts/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script>
$('#order').on('input', '.item .qty', function (e){
	var item_price = parseFloat($(this).prev('.price').children("input").val());
	
	var $current_td = $(this);
	$(this).parent(".item").trigger("calculate");

	// Now, update the cart and check if prices change according to quantity
	$.post('<?=URL_ROOT . $lang2 . "/" . $routes["cart_update"]?>', $(this).parent(".item").data("serialized"), function(results){
		
		if($(results).find(".error").length){
			$current_td.parent("tr").addClass("error");
			$("#message").find(".error").remove();
			$("#message").append($(results).find(".error")).fadeIn().delay(5000).fadeOut();
			$(".shipping_rates").hide();
			$(".shipping_rates_estimate").val(0);
		}else {
			$(".shipping_rates").show();
			$("#message").empty();
			$current_td.parent("tr").removeClass("error");
			var $new_shipping_rates;
			$new_shipping_rates = $(results).find("#new_shipping_rates").html();
			if($new_shipping_rates)
			{
				$new_shipping_rates = JSON.parse($new_shipping_rates);
				$('.shipping_rates_select').find('option').remove();
				$.each($new_shipping_rates, function (i, elem) {
					if("<?=$_SESSION['cart']["shipping_rates_estimate"]?>"==elem.service_code[0])
					{
						$(".shipping_rate_estimate").text(elem.shipping_cost[0]);
					}
					
					$('.shipping_rates_select').append($('<option>', {
						value: elem.service_code[0],
						price: elem.shipping_cost[0],
						text: elem.service_name[0]+" ("+elem.shipping_cost[0]+" $) - "+elem.expected_delivery_date[0]
					}));
					
				});
			}
		}

		$current_td.trigger("calculate");

	})
	
}).on('calculate', '.item ', function (e){
	var qty = 0;
	//var item_price = Number($(this).find('.price').children("input").val());
	var item_price = 0;
	$(this).find(".price input").each(function(){
		if(item_price==0) item_price = Number($(this).val());
	})
	
	var sum = 0;
	var sub_total = 0;
	var grand_total = 0;
	var data = {};
	$(this).find(".qty input").each(function(){
		data[$(this).attr("name")] = $(this).val();
		qty += Number($(this).val());
	})
	
	// Update quantity live in the dropdown
	$(".cart-quickview .cart-quickview-inner").find("table#"+$(this).attr("id")).find(".product-qty .product-qty-qty").html($(this).find('.qty input').val());
	$total = $(this).find('.total');
	
	sum = item_price * qty;
	$(".cart-quickview .cart-quickview-inner").find("table#"+$(this).attr("id")).find(".product-price .product-price-price").html(sum);

	$total.data("amount", sum).html(sum.toFixed(2) + ' $');
	
	// Grand Total
	$(".total").each(function() {
		sub_total += $(this).data("amount");
	});
	$(".sub_total").html(sub_total.toFixed(2) + ' $');
	
	shipping_cost = 0;
	if(parseFloat($(".shipping_rates_estimate").val())) shipping_cost = parseFloat($(".shipping_rates_estimate").val());

	// Update subtotal in the dropdown
	$(".cart-quickview .cart-quickview-inner .cart-subtotal .cart-subtotal-subtotal").html(sub_total.toFixed(2));
	grand_total = sub_total;
	$(".tax").each(function(){
		var $rate = $(this).find("input[name*=_rate]").detach();
		var amount = (sub_total + shipping_cost) * parseFloat($rate.val());
		grand_total += amount;
		$(this).html(amount.toFixed(2) + ' $').append($rate);
	})
	if(parseFloat($(".shipping_rates_estimate").val())) grand_total += parseFloat($(".shipping_rates_estimate").val());
	
	$(".grand_total").html(grand_total.toFixed(2) + ' $');
		
	$(this).data("serialized", data);
}).on('click', '.item .action-remove', function (e){
	var $cartTable = $(this).parents("table");
	var currentID = $(this).parents("tr").attr("id");
	if(confirm('<?=$cart['message_remove']?>')){
		$(this).parents("tr").remove();
		$.post('<?=URL_ROOT . $lang2 . "/" . $routes["cart_remove"]?>', {id:currentID}, function(results){
			var item_count = $(results).find("#item_count");
			$("span.cart-counter").html(item_count).html();
			$(".cart-quickview .cart-quickview-inner").find("table#"+currentID).remove();

			$cartTable.find(".item .qty").trigger("calculate");
			//If cart is empty, we reload the page
			if(!$cartTable.find("tr.item").length){
				location.reload();
			}
		});
		
	}
	e.preventDefault();
	
}).submit(function(e){
	if($(this).find("button[type=submit]:focus" ).attr("name") == 'empty_cart'){
		if(!confirm('<?=$cart['message_empty']?>')){
			e.preventDefault();
		}
	}
		
})
$("form#estimate_shipping_cost").on("change", "select[name=shipping_rates_select]", function() {
	var new_shipping_rate_estimate = $(this).children("option:selected").attr("price");
	var new_shipping_rate_name = $(this).children("option:selected").attr("name");
	var new_shipping_rate_date = $(this).children("option:selected").attr("estimated_date");
	if($("input.shipping_rates_estimate").length == 0 && $("span.shipping_rate_estimate").length == 0){
		$("td.shipping_cost").append("<input name='shipping_rates_estimate' class='shipping_rates_estimate' type='hidden' /><span class='shipping_rate_estimate' /> $")
	}
	$("input.shipping_rates_estimate").val(new_shipping_rate_estimate);
	$("span.shipping_rate_estimate").text(new_shipping_rate_estimate);
	$("tr.shipping_rates td:nth-child(2) span.name_and_date").text(new_shipping_rate_name + ' - ' + new_shipping_rate_date);
	$('.item').trigger("calculate");
});
//hide estimate form
$("#estimate_shipping_cost").hide();
//hide estimate shipping
if(!$("td.shipping_cost").text().trim())$("td.shipping_cost").parent("tr").hide();
/*$("#trigger-estimate-shipping-cost").click(function() {
  $("#estimate_shipping_cost").toggle();
});*/

// Calculate correct amount in floatval.
$('.item').trigger("calculate");


$("form#estimate_shipping_cost").on("submit", function(){
	if($("select[name=shipping_rates_select]").val() && $("input[name=zip_code_estimate]").val() == $("input[name=zip_code_estimate]").data("originalValue")){
		$.fancybox.close();
		//On doit fixer ça plus tard ... ça ne s'enregistre pas quand on fait juste faire en ajax.
		//return false;
	}
	
		$.post('<?=URL_ROOT . $lang2 . "/" . $routes["cart_estimate_shipping_rates"]?>', $(this).serialize(), function(results){
			$("form#estimate_shipping_cost").html($(results).html());
			
			if($("select[name=shipping_rates_select]").children().length == 0){
				$("select[name=shipping_rates_select]").hide();
			}else{
				$("select[name=shipping_rates_select]").trigger("change");
			}
			$("form#estimate_shipping_cost").trigger("save");
			
		})
	
	
	return false;
}).on("save", function(){
	$("select[name=shipping_rates_select]").data("originalValue", $("select[name=shipping_rates_select]").val());
	$("input[name=zip_code_estimate]").data("originalValue", $("input[name=zip_code_estimate]").val());
	if($("td.shipping_cost").text().trim()) $("td.shipping_cost").parent("tr").show();

}).trigger("save");

</script>
<!-- FancyBox -->
<script src="<?=URL_ROOT . PUBLIC_FOLDER?>scripts/jquery.fancybox.js?v=2.1.5" type="text/javascript"></script>
<script type="text/javascript">
$("#trigger-estimate-shipping-cost").fancybox({
	'titlePosition'		: 'inside',
	'transitionIn'		: 'none',
	'transitionOut'		: 'none',
	helpers: {
			overlay: {
				locked: false
			}
		},
	 'beforeShow': function(){
		 //show only complete stuff ...
		 
		 if($("select[name=shipping_rates_select]").children().length == 0){
			 $("select[name=shipping_rates_select]").hide();
		 }
	 }
});
</script>
<!-- End FancyBox -->
<?php endblock() ?>
