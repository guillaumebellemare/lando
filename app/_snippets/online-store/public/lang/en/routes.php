<?php

$routes = array(
	"cart" => "shopping-cart",
	"cart_upload-logo" => "shopping-cart/upload-logo/{$_GET['param2']}",
	"cart_delete-logo" => "shopping-cart/delete-logo",
	"cart_update" => "shopping-cart/update",
	"cart_remove" => "shopping-cart/remove",
	"cart_add" => "shopping-cart/add",
	"login" => "login",
	"checkout" => "checkout/shipping-address",
	"checkout_login" => "checkout/login",
	"checkout_billing-address" => "checkout/billing-address",
	"checkout_order-confirmation" => "checkout/order-confirmation",
	"checkout_completed-transaction" => "checkout/completed-transaction",
	"forgot" => "recover-your-password",
	"recover" => "reinitialize-your-password",
	"recover_valid" => "reinitialize-your-password/{$_GET['param1']}/{$_GET['param2']}",
);
