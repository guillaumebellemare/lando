<?php

/*
|--------------------------------------------------------------------------
| Initialization
|--------------------------------------------------------------------------
|
| This is where the initialization is made
|
*/
define("COMPLETE_FOLDER", dirname(__FILE__));
define("COMPLETE_URL_ROOT", $_SERVER['DOCUMENT_ROOT'].URL_ROOT);
define("PUBLIC_FOLDER", "public/");
define("WBR_FOLDER", "images/wbr/uploads/");


/*
|--------------------------------------------------------------------------
| Disabling Magic Quotes
|--------------------------------------------------------------------------
|
|
*/
if(get_magic_quotes_gpc()) {
    $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    while (list($key, $val) = each($process)) {
        foreach ($val as $k => $v) {
            unset($process[$key][$k]);
            if (is_array($v)) {
                $process[$key][stripslashes($k)] = $v;
                $process[] = &$process[$key][stripslashes($k)];
            } else {
                $process[$key][stripslashes($k)] = stripslashes($v);
            }
        }
    }
    unset($process);
}


/*
|--------------------------------------------------------------------------
| Database informations
|--------------------------------------------------------------------------
|
| This is where the database info is included.
|
*/
require_once("app/core/AppDatabase.php");


/*
|--------------------------------------------------------------------------
| Application Languages
|--------------------------------------------------------------------------
|
| Here is where the languages are set.
| fr/fre is default
|
*/
require_once("app/helpers/lang.class.php");
$lang = new Lang($possible_languages);
$lang2 = $lang->lang2;
$lang3 = $lang->lang3;
$lang2_trans = $lang->lang2_trans;

require_once("app/helpers/sluggedrecord.class.php");
if(@require_once("app/helpers/translate.class.php"));
if(@require_once(COMPLETE_URL_ROOT . 'app/helpers/meta.class.php'));
require_once("app/core/AppRoutes.php");


/*
|--------------------------------------------------------------------------
| Error handlers
|--------------------------------------------------------------------------
|
| This is where the errors handlers are declared
|
*/
$app_messages = array();
$app_errors = array();

$messages = array();
$errors = array();


/*
|--------------------------------------------------------------------------
| Template Inheritance
|--------------------------------------------------------------------------
|
| Here is where the templates inheritance is declared.
|
*/
require_once("app/helpers/ti/ti.php");


/*
|--------------------------------------------------------------------------
| App Controller
|--------------------------------------------------------------------------
|
| Here is where the custom methods are declared.
|
*/
require_once("app/core/AppController.php");


/*
|--------------------------------------------------------------------------
| Model classes
|--------------------------------------------------------------------------
|
| Here is where all the models classes are called.
|
*/
require_once("app/core/App.php");
require_once("app/helpers/Helper.php");
$app = new App();
$helper = new Helper();

foreach (glob("app/models/*.php") as $filename)
{
	require_once($filename);
}
$user = new User();
$app_controller = new AppController();


/*
|--------------------------------------------------------------------------
| Custom methods
|--------------------------------------------------------------------------
|
| Here is where the custom methods are declared.
|
*/
require_once("app/helpers/custom_methods/index.php");


/*
|--------------------------------------------------------------------------
| Languages
|--------------------------------------------------------------------------
|
| Here is where the langugages files are instantiated.
|
*/
foreach (glob("public/lang/".$lang2."/*.php") as $filename)
{
    require_once($filename);
}


/*
|--------------------------------------------------------------------------
| Shopping Cart
|--------------------------------------------------------------------------
|
| Here is where the langugages files are instantiated.
|
*/
if(SHOPPING_CART) require_once("app/helpers/cart/cart_core.php");


/*
|--------------------------------------------------------------------------
| Debugging
|--------------------------------------------------------------------------
|
| Debugging options.
|
|
*/
if(DEBUG==true || DEBUG_ALL==true)
{
	if(CHECK_MOD_REWRITE)
	{
		if(function_exists('apache_get_modules'))
		{
			$modules = apache_get_modules();
			$mod_rewrite = in_array('mod_rewrite', $modules);
		}else{
			$mod_rewrite =  getenv('HTTP_MOD_REWRITE')=='On' ? true : false ;
		}
		
		if ($mod_rewrite)
		{
			$app_messages[] = '<strong>Mod_rewrite est activé</strong><hr class="app-hr">';
		}else{
			$app_messages[] = '<strong>Mod_rewrite est désactivé</strong><hr class="app-hr">';
		}
	}
	
	if($_SERVER['REMOTE_ADDR']===IP_ADDRESS)
	{
		$app_messages[] = '<strong>IP address: </strong>'.$_SERVER['REMOTE_ADDR'].'<br>';
		$debug_on = true;
	}
	
}

if(SHOPPING_CART && (CANADA_POST_SANDBOX_MODE || PAYPAL_SANDBOX_MODE))
{
	$app_floating_messages[] = 'Vous &ecirc;tes en <strong>mode sandbox</strong>';
	if(CANADA_POST_SANDBOX_MODE) $app_floating_messages[] = ' pour <strong>Poste Canada</strong>';
	if(CANADA_POST_SANDBOX_MODE && PAYPAL_SANDBOX_MODE) $app_floating_messages[] = ' et';
	if(PAYPAL_SANDBOX_MODE) $app_floating_messages[] = ' pour <strong>PayPal</strong>';
}

require_once("app/core/AppHandler.php");
