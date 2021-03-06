<?php

$cart_app_routes = array(
	"product" => "CartController@product",
	"product_show" => "CartController@product_show",
	"cart" => "CartController@index",
	"cart_add" => "CartController@add",
	"cart_update" => "CartController@update",
	"cart_remove" => "CartController@remove",
	"cart_estimate_shipping_rates" => "CartController@estimate_shipping_rates",
	"checkout_shipping-address" => "CartController@shipping_address",
	"checkout_login" => "CartController@login",
	"checkout_logout" => "CartController@logout",
	"checkout_billing-address" => "CartController@billing_address",
	"checkout_order-confirmation" => "CartController@order_confirmation",
	"checkout_completed-transaction" => "CartController@completed_transaction",
	"checkout_guest" => "CartController@guest",
	"forgot" => "CartController@forgot",
	"recover_valid" => "CartController@recover",
	"user_profile" => "CartController@profile",
	"pp_redirect" => "CartController@paypal_redirect",
	"pp_back" => "CartController@paypal_back",
	"pp_cancel" => "CartController@paypal_cancel",
	"pp_cancel_complete" => "CartController@paypal_cancel",
	"pp_routine_check" => "CartController@paypal_routine_check",
	"pp_ipn_route" => "CartController@paypal_ipn",
	//"pp_ipn" => "CartController@paypal_ipn",
);
