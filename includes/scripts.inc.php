<!--[if lte IE 8]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
<!--<script src="http://code.jquery.com/jquery-1.9.1.min.js" type="text/javascript"></script>-->
<script src="http://code.jquery.com/jquery-1.11.0.min.js" type="text/javascript"></script>
<script src="<?php echo URL_ROOT; ?>scripts/selectivizr-min.js" type="text/javascript"></script>

<!-- FancyBox -->
<!-- <script src="<?php echo URL_ROOT; ?>scripts/jquery.fancybox.js?v=2.1.5" type="text/javascript"></script> -->
<?php echo $compressor->load('js', array('?v=2.1.5' => 'scripts/jquery.fancybox.js'), null, false); ?>

<script type="text/javascript">
$(document).ready(function() {
	$('.fancybox').fancybox({
		padding:0,
		maxWidth:960	
	});
});
</script>
<!-- End FancyBox -->
<!-- FlexSlider -->
<script defer src="<?php echo URL_ROOT; ?>scripts/jquery.flexslider-min.js"></script>
<script type="text/javascript">
$(window).load(function(){
	$('.flexslider').flexslider({
		animation: "fade",
		controlNav: false,
		prevText: "",
		nextText: "",
		directionNav: true,
		start: function(slider){
			$('body').removeClass('loading');
		}
	});
});
</script>
<!-- End FlexSlider -->
<?php echo $compressor->load('js', 'scripts/navigation.js'); ?>