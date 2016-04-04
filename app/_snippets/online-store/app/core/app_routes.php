<?php

$app_routes = array(
	"cart" => "CartController@index",
	"cart_upload-logo" => "CartController@upload_logo",
	"cart_delete-logo" => "CartController@delete_logo",
	"cart_add" => "CartController@add",
	"cart_update" => "CartController@update",
	"cart_remove" => "CartController@remove",
	"checkout" => "CartController@checkout",
	"checkout_login" => "CartController@login",
	"checkout_billing-address" => "CartController@billing_address",
	"checkout_order-confirmation" => "CartController@order_confirmation",
	"checkout_completed-transaction" => "CartController@completed_transaction",
	"checkout_guest" => "CartController@guest",
	"forgot" => "CartController@forgot",
	"recover_valid" => "CartController@recover",
);
