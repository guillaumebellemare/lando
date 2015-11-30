<!--[if lte IE 8]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
<script src="http://code.jquery.com/jquery-1.11.0.min.js" type="text/javascript"></script>
<script src="<?=URL_ROOT . PUBLIC_FOLDER?>scripts/selectivizr-min.js" type="text/javascript"></script>
<script src="<?=URL_ROOT . PUBLIC_FOLDER?>scripts/navigation.js" type="text/javascript"></script>
<script src="<?=URL_ROOT . PUBLIC_FOLDER?>scripts/jquery.smooth-scroll.js" type="text/javascript"></script>
<script src="<?=URL_ROOT . PUBLIC_FOLDER?>scripts/responsive-request-desktop-site.js" type="text/javascript"></script>
<script>
/* Animated navigation-trigger */
$(document).ready(function(){
    $('.navigation-trigger').click(function(){
        $(this).toggleClass('open');
    });
});
</script>
