<!-- FancyBox -->
<script src="<?=URL_ROOT . PUBLIC_FOLDER?>scripts/jquery.fancybox.js?v=2.1.5" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
	$('.fancybox').fancybox({
		padding:0,
		maxWidth:960,
		helpers: {
			overlay: {
				locked: false
			}
		},		
	});
});
</script>
<!-- End FancyBox -->
