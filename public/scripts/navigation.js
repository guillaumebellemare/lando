// Dropdown
$(document).ready(function() {
	$('.nav li').hover(
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
	$('.nav').addClass('hide');
	$('.languages').addClass('hide');
	$(".nav-trigger").click(function () {
		$('.nav').toggleClass('show').toggleClass('hide');
		$('.languages').toggleClass('show').toggleClass('hide');
		return false;
	});
});


/* Animated navigation-trigger */
$(document).ready(function(){
    $('.nav-trigger').click(function(){
        $(this).toggleClass('open');
    });
});
