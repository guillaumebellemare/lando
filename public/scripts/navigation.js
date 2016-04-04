// Dropdown
$(document).ready(function() {
	$('.navigation li').hover(
		function () {
			$(this).find('ul').addClass("is-hover");
		},
		function () {
			if($(this).find('ul').hasClass('is-hover'))$(this).find('ul').removeClass("is-hover");
		}
	);
});

// Responsive navigation
$(document).ready(function() {
	$('.navigation').addClass('hide');
	$(".navigation-trigger").click(function () {
		$('.navigation').toggleClass('show');
		$('.navigation').toggleClass('hide');

		return false;
	});
});


/* Animated navigation-trigger */
$(document).ready(function(){
    $('.navigation-trigger').click(function(){
        $(this).toggleClass('open');
    });
});
