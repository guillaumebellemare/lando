<?php

class App {
	
	public $db = NULL;
	public $lang2 = NULL;
	public $lang3 = NULL;
	private $controller_loaded = false;
	private $view_loaded = false;
	private $view_from_cart = false;
	
	public $routes;
	private $app_routes;
	private $current_route;
	private $current_function;
	
	public function __construct() {
		
		# Load languages
		$this->loadLanguages();
			
		require_once('app/core/AppConnection.php');
		require_once('app/core/AppController.php');
		require_once('app/core/AppModel.php');
		require_once('app/core/CustomException.php');
		require_once("app/helpers/templace_inheritance/ti.php");

		# Init
		$this->loadApplication();
	}

	private function loadApplication() {
		
		try {
			
			# Load routes
			require_once('routes.php');
			
			# Routes
			require_once('public/lang/'.$this->lang2.'/routes.php');
			$this->app_routes = $app_routes;
			$this->routes = $routes;
			
			if(SHOPPING_CART) require_once('app/helpers/cart/app_routes.php');
			if(SHOPPING_CART) require_once('app/helpers/cart/lang/'.$this->lang2.'/routes.php');
			if(SHOPPING_CART) $this->cart_app_routes = $cart_app_routes;
			if(SHOPPING_CART) $this->app_routes = array_merge($this->app_routes, $this->cart_app_routes);
			if(SHOPPING_CART) $this->cart_routes = $cart_routes;
			if(SHOPPING_CART) $this->routes = array_merge($this->routes, $this->cart_routes);
			
			# Languages files
			foreach(glob("public/lang/".$this->lang2."/*.php") as $filename) require_once($filename);
			if(SHOPPING_CART) foreach(glob("app/helpers/cart/lang/".$this->lang2."/*.php") as $filename) require_once($filename);
			
			# Metas
			$this->meta = $meta;
			
			# Helper Class
			require_once('app/helpers/Helper.php');
			$helper = new Helper();
			$this->helper = new Helper();
			
			# Initialize application
			$this->loadModels();
			$this->loadController();
			$this->loadView();
			
			# Close DB connection
			AppConnection::closeConnection();
			
			# Extract arrays from Controller
			if(isset($this->current_arrays)) extract($this->current_arrays);
			
			# Get view
			if(!$this->view_from_cart) require_once('app/views/'.$this->view.'.php'); else require_once('app/helpers/cart/views/'.$this->view.'.php');
		
		}catch(CustomException $e) {
			if(DEBUG) echo $e->errorMessage($e), "\n";
		}

	}	
	
	private function loadLanguages() {
		$lang = new Lang();
		$this->lang2 = $lang->lang2;
		$this->lang3 = $lang->lang3;
		$this->lang2_trans = $lang->lang2_trans;
		$this->lang3_trans = $lang->lang3_trans;
		$this->lang_trans_complete = $lang->lang_trans_complete;
		$this->possible_languages = $lang->possible_languages;
	}
	
	private function loadModels() {
		foreach(glob('app/models/*.php') as $filename)
		{
			require_once($filename);
		}
		
		if(SHOPPING_CART)
		{
			foreach(glob('app/helpers/cart/models/*.php') as $filename)
			{
				require_once($filename);
			}
		}
	}
	
	private function loadController() {
	
		# Get current page path
		$param_number = count($_GET)-2;
		$page = $_GET['page'];
		for ($i = 1; $i <= $param_number; $i++) {
			$page .= '/'.$_GET['param'.$i.''];
		}
		
		$this->current_route = array_search($page, $this->routes);

		if(array_search($page, $this->routes))
		{
			$this->current_route = array_search($page, $this->routes);
		}else{
			$asked_route = NULL;
			for ($i = 1; $i <= $param_number; $i++) {
				$asked_route .= '/$_GET["param'.$i.'"]';
			}
			
			if(DEBUG) throw new CustomException('Veuillez v&eacute;rifier que la route <em>'.$_GET['page'].$asked_route.'</em> existe bien');
		}
		
		# Broke the route into tokens
		$token = strtok($this->app_routes[$this->current_route], '@::');
		while ($token !== false) {
			$current_app_route[] = $token;
			$token = strtok('@::');
		}
		
		$current_controller = $current_app_route[0];
		$this->current_route = explode('Controller', $current_controller);
		$this->current_route = strtolower($this->current_route[0]);

		$condition = file_exists('app/controllers/'.ucfirst($this->current_route).'Controller.php');
		if(SHOPPING_CART) $condition .= " | ".file_exists('app/helpers/cart/controllers/'.ucfirst($this->current_route).'Controller.php');
		if($condition)
		{
			$this->current_function = $current_app_route[1];
	
			# If the route if protected, authenticate the user and redirect him to the login page if necessary
			if(isset($current_app_route[2]) && $current_app_route[2]=='protected')
			{
				$user = new User();
				if(!$user->check()) header('Location: '.URL_ROOT.$lang2.'/'.$this->routes['login']);
			}
			
			#if(DEBUG==true && $debug_on==true) $app_messages[] = '<hr class="app-hr"><strong>Current controller: </strong>'.$current_controller.'@'.$this->current_function.'<br>';
			
			if(file_exists('app/controllers/'.$current_controller.'.php'))
			{
				require_once('app/controllers/'.$current_controller.'.php');
			}elseif(SHOPPING_CART){
				require_once('app/helpers/cart/controllers/'.$current_controller.'.php');
				#$cart_controller = new $current_controller();
			}
			
			if(class_exists($current_controller))
			{
				$this->current_controller = new $current_controller($this->current_function, $this->lang, $this->db, $this->helper, $this->routes);
				$this->controller_loaded = true;					
			}else{
				if(DEBUG) throw new CustomException('Le nom de votre classe Controller doit correspondre &agrave; son nom de fichier.');	
			}
	
			# If the method exist, extract the arrays returned by the function
			if(method_exists($this->current_controller, $this->current_function))
			{
				$f = $this->current_function;
				$this->current_arrays = $this->current_controller->$f();
				if(isset($this->current_arrays) && is_array($this->current_arrays)) extract($this->current_arrays); elseif(isset($this->current_arrays) && !is_array($this->current_arrays) && DEBUG) throw new CustomException('Vous devez retourner un array[] dans la fonction'.$this->current_function.'() de '.$current_controller.'.');
			}else{
				if(DEBUG) throw new CustomException('Fonction <strong>'.$this->current_function.'()</strong> introuvable pour <strong>'.ucfirst($_GET["page"]).'Controller</strong><br>V&eacute;rifiez que vos routes sont bien cr&eacute;&eacute;s.');
			}
			
		}else{
			if(DEBUG) throw new CustomException('Le fichier <strong>'.$this->current_route.'Controller.php</strong> est introuvable dans <em>app/controllers/'.ucfirst($this->current_route).'Controller.php</em>.<br>V&eacute;rifiez que le fichier existe.');
		}	
	}
	
	private function loadView() {
	
		# View file handling
		try{
			if(file_exists('app/views/'.$this->current_route.'/'.$this->current_function.'.php') && !$this->view_loaded)
			{
				#if(DEBUG==true && $debug_on==true) $app_messages[] = '<hr class="app-hr"><strong>Current view:</strong> app/views/'.$this->current_route.'/'.$this->current_function.'.php';
				$this->view = $this->current_route.'/'.$this->current_function;
				$this->view_loaded = true;
			}elseif(SHOPPING_CART && file_exists('app/helpers/cart/views/'.$this->current_route.'/'.$this->current_function.'.php') && !$this->view_loaded){
				#if(DEBUG==true && $debug_on==true) $app_messages[] = '<hr class="app-hr"><strong>Current view:</strong> app/views/'.$this->current_route.'/'.$this->current_function.'.php';
				$this->view = $this->current_route.'/'.$this->current_function;
				$this->view_loaded = true;
				$this->view_from_cart = true;
			}elseif(!$this->view_loaded){
				
				if($_GET["page"]!="404" && $_GET["page"]!="index" && $_GET["page"]!="") {
					header("Location: http://".$_SERVER[HTTP_HOST].URL_ROOT.$this->lang2."/404");
				}
				$this->view = "404/index";
				$this->view_loaded = true;
				if(DEBUG) throw new CustomException('La vue <strong>'.$this->current_function.'.html</strong> introuvable dans <em>app/views/'.$this->current_route.'/'.$this->current_function.'.html</em>');
			
			}
		}catch(CustomException $e) {
			if(DEBUG) echo $e->errorMessage($e), "\n";
		}

		$page_setted = 1;

	}
	
	
	function translateFromPage()
	{
		
		$key = NULL;
		
		if(SHOPPING_CART)
		{
			include(COMPLETE_URL_ROOT."app/helpers/cart/lang/".$this->lang2."/routes.php");
			$global_routes = array_merge($this->cart_routes, $this->routes);
		}else{
			$global_routes = $this->routes;	
		}
		if(isset($_GET['param1']) && isset($_GET['param2']) && isset($_GET['param3']) && isset($_GET['param4'])) $key = array_search("{$_GET['page']}/{$_GET['param1']}/{$_GET['param2']}/{$_GET['param3']}/{$_GET['param4']}", $global_routes);
		elseif(isset($_GET['param1']) && isset($_GET['param2']) && isset($_GET['param3'])) $key = array_search("{$_GET['page']}/{$_GET['param1']}/{$_GET['param2']}/{$_GET['param3']}", $global_routes);
		elseif(isset($_GET['param1']) && isset($_GET['param2'])) $key = array_search("{$_GET['page']}/{$_GET['param1']}/{$_GET['param2']}", $global_routes);
		elseif(isset($_GET['param1'])) $key = array_search("{$_GET['page']}/{$_GET['param1']}", $global_routes);
		elseif(!$key) $key = array_search("{$_GET['page']}", $global_routes);

		if($global_routes["$key"] && $global_routes["$key"] != 'index')
		{
			include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'lang/'.$this->lang2_trans.'/routes.php');
			if(SHOPPING_CART)
			{
				include(COMPLETE_URL_ROOT."app/helpers/cart/lang/".$this->lang2_trans."/routes.php");
				$routes = array_merge($global_routes, $cart_routes);
			}
			
			$translated_route .= $routes["$key"];

			if($translated_route) return '/'.$translated_route; else return false;
		}else{
			return false;
		}

	}
	
	# getMeta()
	# @access public
	# @param $part
	# @return current_meta
	public function getMeta($part)
	{
		return $this->current_controller->getMeta($part, $this->meta, $this->routes);
	}
	

}