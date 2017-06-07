<?php

define("DEBUG", 0);
define("IP_ADDRESS", "65.94.73.25");
define("URL_ROOT", '/lando/');
define("ADMIN_PATH", 'zap/');

if((!isset($_GET["lang"]) && $_GET["page"]!="404") || $_GET["page"]=="") header('Location: '.URL_ROOT.'fr');

define("COMPLETE_FOLDER", dirname(__FILE__));
define("PUBLIC_FOLDER", "public/");
define("WBR_FOLDER", "images/wbr/uploads/");
define("COMPLETE_URL_ROOT", $_SERVER['DOCUMENT_ROOT'].URL_ROOT);

# Define Cart & PayPal Constants
define("SHOPPING_CART", 0);
define("CANADA_POST_SANDBOX_MODE", 1);
define('PAYPAL_SANDBOX_MODE', 1);

# Define the Email Manager
define("EMAIL_MANAGER", "lando@propagandadesign.com");
define("EMAIL_MANAGER_NAME", "Lando");

if(DEBUG)
{
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL & ~E_NOTICE);
}

# Paypal Related Information
if(PAYPAL_SANDBOX_MODE == 0)
{
	# Live account
	define('PAYPAL_ACCOUNT', '');
	define('PAYPAL_URL', 'https://www.paypal.com/cgi-bin/webscr');
	
	define('PAYPAL_CLIENT_ID', '');
	define('PAYPAL_SECRET', '');

}else{
	# Sandbox account
	define('PAYPAL_ACCOUNT', 'genevieve-facilitator@lrdi.ca');
	define('PAYPAL_URL', 'https://www.sandbox.paypal.com/cgi-bin/webscr');
	
	define('PAYPAL_CLIENT_ID', 'AbfpyxzN0SIpRvjIWX2H3UQPy0dx-qUNibGCgRivAltX7pM8aCuNEW_sTsp2Hvny2hj4-yI1NkvWF4C5');
	define('PAYPAL_SECRET', 'EAGwytahAMW9w2aZXn8lVHlLbGBJB3a2ZXCTdCCQprGenrmmf3YDdBFRJwk5WgMPHIdsbyTb7i2poqUh');
}


# Canada Post Information
if(CANADA_POST_SANDBOX_MODE == 0)
{
	define("CANADA_POST_USERNAME", "");
	define("CANADA_POST_PASSWORD", "");
	define("CANADA_POST_MAILED_BY", "");
	define("CANADA_POST_ORIGIN_POSTAL_CODE", "G1J3B9");
}else{
	define("CANADA_POST_USERNAME", "95885d0fc5a54431");
	define("CANADA_POST_PASSWORD", "29de072b142ef8612152e1");
	define("CANADA_POST_MAILED_BY", "9379295");
	define("CANADA_POST_ORIGIN_POSTAL_CODE", "G1J3B9");
}


$possible_languages = array('fr' => 'fre', 'en' => 'eng');
require_once("app/core/Lang.php");
require_once("app/core/App.php");
$lang = new Lang();
$app = new App();
