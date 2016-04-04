// Dropdown
$(document).ready(function() {
	$('.shopping-cart-navigation li').hover(
		function () {
			$(this).find('ul').addClass("is-hover");
		},
		function () {
			if($(this).find('ul').hasClass('is-hover'))$(this).find('ul').removeClass("is-hover");
		}
	);
});

