// Attach a submit handler to the form
$("#addToCart").submit(function(event) {
 
  // Stop form from submitting normally
  event.preventDefault();
 
  // Get some values from elements on the page:
  var $form = $(this),
  url = $form.attr("action");
  // Send the data using post
  var posting = $.post(url, $(this).serialize(), 'html');
  $("button[type=submit]").prop("disabled", true);
  $("input[name*=qty]").val("");
    //console.log(posting);

  // Put the results in a div
  posting.done(function(data) {
    var msg = $(data).find("#content .msg");
    var item_count = $(data).find("#item_count");
    var cart_quickview = $(data).find(".cart-quickview");

	if(item_count.html() > 0){
		if(!$("span.cart-counter").length){
			$(".icon-cart").append("<span class='cart-counter'></span>");
			$(".icon-cart").parent("li").append("<div class='cart-quickview'></div>");
		}
		$("span.cart-counter").html(item_count.html());
		$("div.cart-quickview").html(cart_quickview.html());
    }

    $("#message").empty().append(msg).fadeIn().delay(5000).fadeOut();
    $("button[type=submit]").prop("disabled", false);
  });
  
});