<?php 
/*
|--------------------------------------------------------------------------
| To-do list
|--------------------------------------------------------------------------
|
| 1. Rendre les fonctions de base (create, read, update, delete) dans le AppModel
|    par défaut pour pouvoir caller get($table, $order, $active, $join) avec ::parent
|    La table pourrait être optionnelle comme elle est déclarée dans le Model
|
| 2. Transformer le dossier classes en helpers (/php, /js, etc.)  
|
| 3. Save fait, Get fait, remove fait.
|
| 4. Ajouter une classe de login/logout
|
*/


/*
|--------------------------------------------------------------------------
| Debugging
|--------------------------------------------------------------------------
|
| Debugging options.
|
*/
$debugging = true;

	error_reporting(E_ALL);
	ini_set("display_errors", 1);
	$db->debug = true;

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
| Application Production Mode
|--------------------------------------------------------------------------
|
| Here is where the production mode is set.
| 0 = Online.
| 1 = Production.
|
*/
define("PRODUCTION_MODE", 0);
define("URL_ROOT", "/lando/");
define("WBR_FOLDER", "images/wbr/uploads/");

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
	error_reporting(E_ALL);
	ini_set("display_errors", 1);
	$db->debug = true;
}


/*
|--------------------------------------------------------------------------
| Application Views & Controllers
|--------------------------------------------------------------------------
|
| Here is where the views and controllers are included.
| The View file is mandatory.
| The Controller file is optional.
| The arrays returned by the functions are extracted here.
|
*/
$pageSet = 0;
$view_loaded = false;
$controller_loaded = false;

if(isset($_GET['arga']) && isset($_GET['argb']) && isset($_GET['argc'])) $page = $_GET['page'].'/'.$_GET['arga'].'/'.$_GET['argb'].'/'.$_GET['argc'].'.html';
elseif(isset($_GET['arga']) && isset($_GET['argb'])) $page = $_GET['page'].'/'.$_GET['arga'].'/'.$_GET['argb'].'.html';
elseif(isset($_GET['arga'])) $page = $_GET['page'].'/'.$_GET['arga'].'.html';
else $page = $_GET['page'];

while ($currentRoute = current($routes)) {
	
	if ($page == $currentRoute && !$controller_loaded) {
		
		if(file_exists('app/controllers/'.ucfirst(key($app_routes)).'Controller.php'))
		{
			$current_app_route = explode('@', $app_routes[key($routes)]);
			$current_controller = $current_app_route[0];
			$current_route = explode('Controller', $current_controller);
			$current_route = strtolower($current_route[0]);
			$current_function = $current_app_route[1];
			if($debugging==true) $app_messages[] = '<strong>Current controller:</strong> '.$current_controller.'@'.$current_function.'<br>';
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
		}elseif(isset($currentArrays) && $debugging==true){
			$app_errors[] = "Vous devez retourner un array dans la fonction $current_function() de $current_controller.";
		}
		
		// View files handling
		if(file_exists('app/views/'.$current_route.'/'.$current_function.'.php') && !$view_loaded)
		{
			if($debugging==true) $app_messages[] = '<strong>Current view:</strong> app/views/'.$current_route.'/'.$current_function.'.php';
			require_once('app/views/'.$current_route.'/'.$current_function.'.php');
			$view_loaded = true;
		}elseif($debugging==true && !$view_loaded){
			$app_errors[] = "Aucune view trouvée correspondant à $current_function dans app/views/$current_route/$current_function.php";	
			require_once('app/views/404/errors.php');	
			$view_loaded = true;		
		}

		$pageSet = 1;
	}
	next($routes);
}

// Error 404
if($pageSet==0) {
	require_once('app/views/404/index.php');			
}


?>