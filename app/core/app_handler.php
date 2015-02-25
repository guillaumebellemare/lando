<?php

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

if(isset($_GET['param1']) && isset($_GET['param2']) && isset($_GET['param3'])) $page = $_GET['page'].'/'.$_GET['param1'].'/'.$_GET['param2'].'/'.$_GET['param3'];
elseif(isset($_GET['param1']) && isset($_GET['param2'])) $page = $_GET['page'].'/'.$_GET['param1'].'/'.$_GET['param2'];
elseif(isset($_GET['param1'])) $page = $_GET['page'].'/'.$_GET['param1'];
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
			if($debug==true && $debug_on==true) $app_messages[] = '<hr class="app-hr"><strong>Current controller: </strong>'.$current_controller.'@'.$current_function.'<br>';
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
		}elseif(isset($currentArrays) && $debug==true && $debug_on==true){
			$app_errors[] = "Vous devez retourner un array[] dans la fonction $current_function() de $current_controller.";
		}
		
		// View file handling
		if(file_exists('app/views/'.$current_route.'/'.$current_function.'.php') && !$view_loaded)
		{
			if($debug==true && $debug_on==true) $app_messages[] = '<hr class="app-hr"><strong>Current view:</strong> app/views/'.$current_route.'/'.$current_function.'.php';
			require_once('app/views/'.$current_route.'/'.$current_function.'.php');
			$view_loaded = true;
		}elseif($debug==true && $debug_on==true && !$view_loaded){
			$app_errors[] = "<hr class='app-hr'>Aucune view trouvée correspondant à $current_function dans app/views/$current_route/$current_function.php";	
			require_once('app/views/404/errors.php');	
			$view_loaded = true;		
		}

		$page_setted = 1;
	}
	next($routes);
}
// Error 404
if($page_setted==0) {
	if($debug==true && $debug_on==true) $app_messages[] = '<hr class="app-hr"><strong>Current view:</strong> app/views/404/index.php';
	require_once('app/views/404/index.php');			
}
