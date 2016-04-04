<?php

# Cart Routes
require_once("app/helpers/cart/lang/".$_GET['lang']."/routes.php");
$routes = array_merge($routes, $cart_routes);

# Cart App Routes
require_once("app/helpers/cart/app_routes.php");
$app_routes = array_merge($app_routes, $cart_app_routes);

# Cart Strings
require_once("app/helpers/cart/lang/".$_GET['lang']."/cart.php");

# Cart Models
foreach (glob("app/helpers/cart/models/*.php") as $filename)
{
	require_once($filename);
}
