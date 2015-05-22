<script>
jQuery.validator.addMethod("password", function( value, element ) {
	var result = this.optional(element) || value.length >= 6 && /\d/.test(value) && /[a-z]/i.test(value);
	if (!result) {
		var validator = this;
		setTimeout(function() {
			validator.blockFocusCleanup = true;
			element.focus();
			validator.blockFocusCleanup = false;
		}, 1);
	}
	return result;
}, "<?=$validate['password'];?>");
</script>
