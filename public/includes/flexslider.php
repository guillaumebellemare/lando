<!-- FlexSlider -->
<script defer src="<?=URL_ROOT . PUBLIC_FOLDER?>scripts/jquery.flexslider-min.js"></script>
<script type="text/javascript">
$(window).load(function(){
	$('.flexslider').flexslider({
		animation: "fade",
		controlNav: false,
		directionNav: false,
		prevText: "",
		nextText: "",
		start: function(slider){
			$('body').removeClass('loading');
		}
	});
});
</script>
<!-- End FlexSlider -->
