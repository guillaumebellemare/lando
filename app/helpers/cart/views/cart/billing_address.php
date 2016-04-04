<?php include('app/views/layouts/one-column.php') ?>
<?php startblock('content') ?>
<h1><?=$cart["step_billing"]?></h1>
<?php if(@include(COMPLETE_URL_ROOT . 'app/helpers/cart/includes/cart-sequence.php')); ?>
<form action="<?=URL_ROOT.$lang2."/".$routes["checkout_billing-address"]?>" method="POST" id="fShipping" name="fShipping">
  <div>
   <?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/messages.php')); ?>
    <div class="l-grid l-row-2">
      <div class="l-grid-50">
        <label for="payment_first_name">
          <?=$cart["form_first_name"]?>
        </label>
        <input name="payment_first_name" id="payment_first_name" type="text" value="<?php if(isset($_SESSION['customer']['payment_first_name'])) echo $_SESSION['customer']['payment_first_name']; ?>" >
      </div>
      <div class="l-grid-50">
        <label for="payment_last_name">
          <?=$cart["form_last_name"]?>
        </label>
        <input name="payment_last_name" id="payment_last_name"type="text" value="<?php if(isset($_SESSION['customer']['payment_last_name'])) echo $_SESSION['customer']['payment_last_name']; ?>" >
      </div>
      <div class="l-grid-50">
        <label for="email">
          <?=$cart["form_email"]?>
        </label>
        <input name="email" id="email" type="text" value="<?php if(isset($_SESSION['customer']['email'])) echo $_SESSION['customer']['email']; ?>" >
      </div>
      <div class="l-grid-50">
        <label for="phone">
          <?=$cart["form_phone"]?>
        </label>
        <input name="phone" id="phone" type="text" value="<?php if(isset($_SESSION['customer']['phone'])) echo $_SESSION['customer']['phone']; ?>" >
      </div>
    </div>
    <hr>
    <label for="payment_company">
      <?=$cart["form_company"]?>
    </label>
    <input name="payment_company" id="payment_company" type="text" value="<?php if(isset($_SESSION['customer']['payment_company'])) echo $_SESSION['customer']['payment_company']; ?>" >
    <div class="l-grid l-row-2">
      <div class="l-grid-50">
        <label for="payment_address_1">
          <?=$cart["form_address"]." (".$cart["line"]." 1)"?></label>
        <input name="payment_address_1" id="payment_address_1" type="text" value="<?php if(isset($_SESSION['customer']['payment_address_1'])) echo $_SESSION['customer']['payment_address_1']; ?>" >
      </div>
      <div class="l-grid-50">
        <label for="payment_address_2">
          <?=$cart["form_address"]." (".$cart["line"]." 2)"?></label>
        <input name="payment_address_2" id="payment_address_2" type="text" value="<?php if(isset($_SESSION['customer']['payment_address_2'])) echo $_SESSION['customer']['payment_address_2']; ?>" >
      </div>
      <div class="l-grid-50">
        <label for="payment_city">
          <?=$cart["form_city"]?>
        </label>
        <input name="payment_city" id="payment_city" type="text" value="<?php if(isset($_SESSION['customer']['payment_city'])) echo $_SESSION['customer']['payment_city']; ?>" >
      </div>
      <div class="l-grid-50">
        <label for="payment_zip">
          <?=$cart["form_zip"]?>
        </label>
        <input name="payment_zip" type="text" value="<?php if(isset($_SESSION['customer']['payment_zip'])) echo $_SESSION['customer']['payment_zip']; ?>" >
      </div>
      <div class="l-grid-50">
        <label for="payment_province_id">Province</label>
        <select class="has-white-bg" name="payment_province_id" id="payment_province_id">
          <?php foreach($provinces as $province) : ?>
          <option value="<?=$province["provinces.id"]?>" <?=(isset($_SESSION['customer']['payment_province_id']) && $_SESSION['customer']['payment_province_id'] == $province["provinces.id"] ? ' selected="selected"' : '')?>>
          <?=$province["provinces.name_$lang3"]?>
          </option>
          <?php endforeach; ?>
        </select>
        <input name="payment_country" id="payment_country" type="hidden" value="<?php if(isset($_SESSION['customer']['payment_country'])) echo $_SESSION['customer']['payment_country']; ?>" >
      </div>
    </div>
  </div>
  <hr>
  <div class="l-grid l-row-2">
    <div class="l-grid-50"></div>
    <div class="l-grid-50">
      <button type="submit" class="btn is-full-width" name="continue"><?=$cart["form_continue"]?></button>
    </div>
  </div>
</form>
<?php endblock() ?>
<?php startblock('extended-scripts') ?>
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
	$("#fShipping").validate({
		rules: {
			payment_first_name: "required",
			payment_last_name: "required",
			email: {
				required: true,
				email: true
			},
			phone: {
				required: true,
				phoneUS: true
			},
			payment_zip: {
				required: true,
				cdnPostal: true
			},
			payment_city: "required",
			payment_address_1: "required",
		},
		messages: {
			payment_first_name: "<?=$validate['first_name_required'];?>",
			payment_last_name: "<?=$validate['last_name_required'];?>",
			email: {
				required: "<?=$validate['email_required'];?>",
				email: "<?=$validate['email'];?>"
			},
			phone: {
				required: "<?=$validate['phone_required'];?>",
				phoneUS: "<?=$validate['phone'];?>"
			},
			payment_zip: {
				required: "<?=$validate['zip_required'];?>",
				cdnPostal: "<?=$validate['zip'];?>"
			},
			payment_city: "<?=$validate['city_required'];?>",
			payment_address_1: "<?=$validate['address_required'];?>",
		}
	});

});
</script>
<?php endblock() ?>