<?php 
/*
|--------------------------------------------------------------------------
| To-do list
|--------------------------------------------------------------------------
|
| 1. Ajouter une classe de login/logout
|
| 2. Ajouter le sitemap comme un controller/view par défaut (peut-être faire un controller
|    avec une fonction du genre $app->addToSitemap("activities", "product") pour les slug<br>
|    automatiques...
|
| 3. Meilleure gestion erreurs : http://php.net/manual/fr/language.exceptions.php<br>
|    http://www.w3schools.com/php/php_exception.asp<br>
|    ==> http://code.tutsplus.com/tutorials/php-exceptions--net-22274 <==
|
*/


/*
|--------------------------------------------------------------------------
| Debugging
|--------------------------------------------------------------------------
|
| Debugging options.
|
| Here is where the production mode is set.
| 0 = Online.
| 1 = Production.
|
*/
$debugging = true;
define("URL_ROOT", "/lando/");
define("WBR_FOLDER", "images/wbr/uploads/");
define("PRODUCTION_MODE", 0);


/*
|--------------------------------------------------------------------------
| Default classes and connection
|--------------------------------------------------------------------------
|
| This is where the connection is made to the database.
| The language files is included as well.
|
*/
require_once("includes/conn.inc.php");
require_once("app/helpers/lang.class.php");
require_once("app/helpers/compressor/compressorloader.class.php");


/*
|--------------------------------------------------------------------------
| Application Languages
|--------------------------------------------------------------------------
|
| Here is where the languages are set.
| fr/fre is default
|
*/
$lang = new Lang();
$lang2 = $lang->lang2;
$lang3 = $lang->lang3;
$lang2_trans = $lang->lang2_trans;

$compressor = new CompressorLoader(PRODUCTION_MODE, URL_ROOT);

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
require_once("app/controllers/AppController.php");
require_once("app/helpers/custom_methods/index.php");


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


/*
|--------------------------------------------------------------------------
| Languages
|--------------------------------------------------------------------------
|
| Here is where the langugages files are instantiated.
|
*/
foreach (glob("lang/".$lang2."/*.php") as $filename)
{
    require_once($filename);
}
require_once("routes.php");


/*
|--------------------------------------------------------------------------
| Debugging
|--------------------------------------------------------------------------
|
| Debugging start.
|
*/

if($debugging==true)
{
	$ip_address = getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR');
	
	if($ip_address==='65.92.227.46')
	{
		$app_messages[] = '<strong>IP address:</strong> '.$ip_address.'<br>';
		error_reporting(E_ALL);
		ini_set("display_errors", 1);
		$db->debug = true;
		$debug_on = true;
		//require_once("app/helpers/sitemap/sitemap-generator.php");
	}
}


/*
|--------------------------------------------------------------------------
| Application Views & Controllers
|--------------------------------------------------------------------------
|
| Here is where the views and controllers are included.
| The View file is mandatory.
| The Controller file is optional.
| The Arrays returned by the functions are extracted here.
|
*/
$page_setted = 0;
$view_loaded = false;
$controller_loaded = false;

if(isset($_GET['arga']) && isset($_GET['argb']) && isset($_GET['argc'])) $page = $_GET['page'].'/'.$_GET['arga'].'/'.$_GET['argb'].'/'.$_GET['argc'];
elseif(isset($_GET['arga']) && isset($_GET['argb'])) $page = $_GET['page'].'/'.$_GET['arga'].'/'.$_GET['argb'];
elseif(isset($_GET['arga'])) $page = $_GET['page'].'/'.$_GET['arga'];
else $page = $_GET['page'];

while ($currentRoute = current($routes)) {
	
	if ($page == $currentRoute && !$controller_loaded) {
		
		// Controller file handling
		if(file_exists('app/controllers/'.ucfirst(key($app_routes)).'Controller.php'))
		{
			$current_app_route = explode('@', $app_routes[key($routes)]);
			$current_controller = $current_app_route[0];
			$current_route = explode('Controller', $current_controller);
			$current_route = strtolower($current_route[0]);
			$current_function = $current_app_route[1];
			if($debugging==true && $debug_on==true) $app_messages[] = '<strong>Current controller: </strong>'.$current_controller.'@'.$current_function.'<br>';
			require_once('app/controllers/'.$current_controller.'.php');
			$controller = new $current_controller($db, $lang3);
			$controller_loaded = true;
		}
		
		// Extract the arrays returned by the function
		$currentArrays = $controller->$current_function();
		if(isset($currentArrays) && is_array($currentArrays))
		{
			while ($currentKey = current($currentArrays)) {
				${key($currentArrays)} = ($currentArrays[key($currentArrays)]);
			next($currentArrays);
			}
		}elseif(isset($currentArrays) && $debugging==true && $debug_on==true){
			$app_errors[] = "Vous devez retourner un array[] dans la fonction $current_function() de $current_controller.";
		}
		
	
		// View file handling
		if(file_exists('app/views/'.$current_route.'/'.$current_function.'.php') && !$view_loaded)
		{
			if($debugging==true && $debug_on==true) $app_messages[] = '<strong>Current view:</strong> app/views/'.$current_route.'/'.$current_function.'.php';
			require_once('app/views/'.$current_route.'/'.$current_function.'.php');
			$view_loaded = true;
		}elseif($debugging==true && $debug_on==true && !$view_loaded){
			$app_errors[] = "Aucune view trouvée correspondant à $current_function dans app/views/$current_route/$current_function.php";	
			require_once('app/views/404/errors.php');	
			$view_loaded = true;		
		}

		$page_setted = 1;
	}
	next($routes);
}
// Error 404
if($page_setted==0) {
	if($debugging==true && $debug_on==true) $app_messages[] = '<strong>Current view:</strong> app/views/404/index.php';
	require_once('app/views/404/index.php');			
}


?>