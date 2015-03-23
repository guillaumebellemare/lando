<?php

/*
|--------------------------------------------------------------------------
| Database connection
|--------------------------------------------------------------------------
|
| This is where the connection is made to the database.
| The language files is included as well.
|
*/
require_once("app/core/app_connect.php");


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
$lang = new Lang();
$lang2 = $lang->lang2;
$lang3 = $lang->lang3;
$lang2_trans = $lang->lang2_trans;

require_once("app/helpers/sluggedrecord.class.php");


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
| Custom methods
|--------------------------------------------------------------------------
|
| Here is where the custom methods are declared.
|
*/
require_once("app/core/app_controller.php");
require_once("app/helpers/custom_methods/index.php");
if(@require_once("app/helpers/meta.class.php"));
if(@require_once("app/helpers/translate.class.php"));

/*
|--------------------------------------------------------------------------
| Model classes
|--------------------------------------------------------------------------
|
| Here is where all the models classes are called.
|
*/
foreach (glob("app/models/*.php") as $filename)
{
	require_once($filename);
}
$user = new User();


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

require_once("app/core/app_routes.php");


/*
|--------------------------------------------------------------------------
| Debugging
|--------------------------------------------------------------------------
|
| Debugging options.
|
|
*/
 

if($debug==true)
{
	if($_SERVER['REMOTE_ADDR']===IP_ADDRESS)
	{
		$app_messages[] = '<strong>IP address: </strong>'.$_SERVER['REMOTE_ADDR'].'<br>';
		error_reporting(E_ALL);
		ini_set("display_errors", 1);
		$db->debug = true;
		$debug_on = true;
	}
}

require_once("app/core/app_handler.php");
