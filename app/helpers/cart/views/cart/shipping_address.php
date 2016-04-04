<?php include('app/views/layouts/one-column.php') ?>
<?php startblock('content') ?>
<h1><?=$cart["step_checkout"]?></h1>
<?php if(@include(COMPLETE_URL_ROOT . 'app/helpers/cart/includes/cart-sequence.php')); ?>
<form action="<?=URL_ROOT . $lang2 . "/" . $routes["checkout_shipping-address"]?>" method="post" id="fATC" name="fATC">
  <div>
    <?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/messages.php')); ?>
    <div class="l-grid l-row-2">
    <div class="l-grid-50"><input type="checkbox" name="sameAsBillingInfo" <?php if(isset($_SESSION['customer']['sameAsBillingInfo']) && ($_SESSION['customer']['sameAsBillingInfo'])) echo 'checked'; ?>  id="sameAsBillingInfo" value="yes">
    <label for="sameAsBillingInfo"><?=$cart["use_expedition_address"]?></label></div>
      </div>
      <hr>
      <div class="l-grid l-row-2">
          <div class="l-grid-50">
            <label for="shipping_first_name"><?=$cart["form_first_name"]?></label>
            <input name="shipping_first_name" id="shipping_first_name" type="text" value="<?php if(isset($_SESSION['customer']['shipping_first_name'])) echo $_SESSION['customer']['shipping_first_name']; ?>" >
            <input name="payment_first_name" type="hidden" value="<?php if(isset($_SESSION['customer']['payment_first_name'])) echo $_SESSION['customer']['payment_first_name']; ?>" >
          </div>
          <div class="l-grid-50">
            <label for="shipping_last_name"><?=$cart["form_last_name"]?></label>
            <input name="shipping_last_name" id="shipping_last_name" type="text" value="<?php if(isset($_SESSION['customer']['shipping_last_name'])) echo $_SESSION['customer']['shipping_last_name']; ?>" >
            <input name="payment_last_name" type="hidden" value="<?php if(isset($_SESSION['customer']['payment_last_name'])) echo $_SESSION['customer']['payment_last_name']; ?>" >
          </div>
      </div>
      <hr>
        <label for="shipping_company"><?=$cart["form_company"]?></label>
        <input name="shipping_company" id="shipping_company" type="text" value="<?php if(isset($_SESSION['customer']['shipping_company'])) echo $_SESSION['customer']['shipping_company']; ?>" >
        <input name="payment_company" type="hidden" value="<?php if(isset($_SESSION['customer']['payment_company'])) echo $_SESSION['customer']['payment_company']; ?>" >
      <div class="l-grid l-row-2">
      <div class="l-grid-50">
        <label for="shipping_address_1"><?=$cart["form_address"]." (".$cart["line"]." 1)"?></label>
        <input name="shipping_address_1" id="shipping_address_1" type="text" value="<?php if(isset($_SESSION['customer']['shipping_address_1'])) echo $_SESSION['customer']['shipping_address_1']; ?>" >
        <input name="payment_address_1" type="hidden" value="<?php if(isset($_SESSION['customer']['payment_address_1'])) echo $_SESSION['customer']['payment_address_1']; ?>" >
      </div>
      <div class="l-grid-50">
        <label for="shipping_address_2"><?=$cart["form_address"]." (".$cart["line"]." 2)"?></label>
        <input name="shipping_address_2" id="shipping_address_2" type="text" value="<?php if(isset($_SESSION['customer']['shipping_address_2'])) echo $_SESSION['customer']['shipping_address_2']; ?>" >
        <input name="payment_address_2" type="hidden" value="<?php if(isset($_SESSION['customer']['payment_address_2'])) echo $_SESSION['customer']['payment_address_2']; ?>" >
      </div>
      <div class="l-grid-50">
        <label for="shipping_city"><?=$cart["form_city"]?></label>
        <input name="shipping_city" id="shipping_city" type="text" value="<?php if(isset($_SESSION['customer']['shipping_city'])) echo $_SESSION['customer']['shipping_city']; ?>" >
        <input name="payment_city" type="hidden" value="<?php if(isset($_SESSION['customer']['payment_city'])) echo $_SESSION['customer']['payment_city']; ?>" >
      </div>
      <div class="l-grid-50">
        <label for="shipping_zip"><?=$cart["form_zip"]?></label>
        <input name="shipping_zip" id="shipping_zip" type="text" value="<?php if(isset($_SESSION['customer']['shipping_zip'])) echo $_SESSION['customer']['shipping_zip']; ?>" >
        <input name="payment_zip" type="hidden" value="<?php if(isset($_SESSION['customer']['payment_zip'])) echo $_SESSION['customer']['payment_zip']; ?>" >
      </div>

      <div class="l-grid-50">
        <label for="shipping_province_id">Province</label>
        <select class="has-white-bg" name="shipping_province_id" id="shipping_province_id">
            <?php foreach($provinces as $province) : ?>
            	<option value="<?=$province["provinces.id"]?>" <?=(isset($_SESSION['customer']['shipping_province_id']) && $_SESSION['customer']['shipping_province_id'] == $province["provinces.id"] ? ' selected="selected"' : '')?>><?=$province["provinces.name_$lang3"]?></option>
            <?php endforeach; ?>
          </select>
		<input name="payment_province_id" type="hidden" value="<?php if(isset($_SESSION['customer']['payment_province_id'])) echo $_SESSION['customer']['payment_province_id']; ?>" >
      	<input name="shipping_country" type="hidden" value="<?php if(isset($_SESSION['customer']['shipping_country'])) echo $_SESSION['customer']['shipping_country']; ?>" >
      </div>
      </div>
  </div>
  <hr>
  <div class="l-grid l-row-2">
  	<div class="l-grid-50"></div>
  	<div class="l-grid-50"><button type="submit" class="btn is-full-width" name="continue"><?=$cart["form_continue"]?></button></div>
  </div>
</form>
<?php endblock() ?>
<?php startblock('extended-scripts') ?>
<script>
$('input[name=sameAsBillingInfo]').on('change', function (e){
	if($(this).is(":checked")){
		$("input[name*=shipping], select[name*=shipping]").each(function(){
			if($(this).parent("div").find("input[name*=payment]").length){

				$(this).val($("[name=" + $(this).attr("name").replace("shipping_", "payment_") + "]").val());	
				$(this).prop("disabled", true);
			}
		})
	}else {
		$("input[name*=shipping], select[name*=shipping]").each(function(){
			$(this).prop("disabled", false);
		})
	}
}).trigger("change");

$('select[name=payment_mode_id]').on('change', function (e){
	if($(this).val() == 'check'){
		$("input[name=payment_other_information]").val("check");
		$("input[name=payment_other_information]").parent("div").hide();
	}else {
		$("input[name=payment_other_information]").parent("div").show();
		$("label[for=payment_other_information]").text($(this).children("option[value="+$(this).val()+"]").text());
		if($("input[name=payment_other_information]").val() == 'check')$("input[name=payment_other_information]").val("");
	}
}).trigger("change");
</script>
<script src="<?=URL_ROOT . PUBLIC_FOLDER?>scripts/jquery.validate.min.js"></script> 
<script src="<?=URL_ROOT . PUBLIC_FOLDER?>scripts/jquery.validate.additional-methods.js"></script> 
<script>
jQuery.validator.addMethod("cdnPostal", function(postal, element) {
    return this.optional(element) || 
    postal.match(/[a-zA-Z][0-9][a-zA-Z](-| |)[0-9][a-zA-Z][0-9]/);
}, "<?=$validate['zip'];?>");
</script> 
<script>
$(document).ready(function() {
// Validate signup form on keyup and submit
$("#fATC").validate({
	rules: {
		shipping_first_name: "required",
		shipping_last_name: "required",
		shipping_date: {
			required: true,
		},
		shipping_email: {
			required: true,
			email: true
		},
		shipping_phone: {
			required: true,
			phoneUS: true
		},
		shipping_zip: {
			required: true,
			cdnPostal: true
		},
		shipping_city: "required",
		shipping_address_1: "required",
	},
	messages: {
		shipping_first_name: "<?=$validate['first_name_required'];?>",
		shipping_last_name: "<?=$validate['last_name_required'];?>",
		shipping_date: {
			required: "<?=$validate['date_required'];?>",
			dateISO: "<?=$validate['date'];?>"
		},
		shipping_email: {
			required: "<?=$validate['email_required'];?>",
			email: "<?=$validate['email'];?>"
		},
		shipping_phone: {
			required: "<?=$validate['phone_required'];?>",
			phoneUS: "<?=$validate['phone'];?>"
		},
		shipping_zip: {
			required: "<?=$validate['zip_required'];?>",
			cdnPostal: "<?=$validate['zip'];?>"
		},
		shipping_city: "<?=$validate['city_required'];?>",
		shipping_address_1: "<?=$validate['address_required'];?>",
	}
});

});
</script>

<?php endblock() ?>
