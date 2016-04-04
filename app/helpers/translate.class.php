<?php 

class Translate {
	
	function translateFromPage($lang=NULL, $global_routes)
	{
		global $lang2_trans, $lang2;

		if(SHOPPING_CART)
		{
			include(COMPLETE_URL_ROOT."app/helpers/cart/lang/".$lang2."/routes.php");
			$global_routes = array_merge($global_routes, $cart_routes);
		}
			
		if(isset($_GET['param1']) && isset($_GET['param2']) && isset($_GET['param3']) && isset($_GET['param4'])) $key = array_search("{$_GET['page']}/{$_GET['param1']}/{$_GET['param2']}/{$_GET['param3']}/{$_GET['param4']}", $global_routes);
		elseif(isset($_GET['param1']) && isset($_GET['param2']) && isset($_GET['param3'])) $key = array_search("{$_GET['page']}/{$_GET['param1']}/{$_GET['param2']}/{$_GET['param3']}", $global_routes);
		elseif(isset($_GET['param1']) && isset($_GET['param2'])) $key = array_search("{$_GET['page']}/{$_GET['param1']}/{$_GET['param2']}", $global_routes);
		elseif(isset($_GET['param1'])) $key = array_search("{$_GET['page']}/{$_GET['param1']}", $global_routes);
		elseif(!$key) $key = array_search("{$_GET['page']}", $global_routes);
		
		if($global_routes["$key"] && $global_routes["$key"] != 'index')
		{
			include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'lang/'.$lang2_trans.'/routes.php');
			if(SHOPPING_CART)
			{
				include(COMPLETE_URL_ROOT."app/helpers/cart/lang/".$lang2_trans."/routes.php");
				$routes = array_merge($global_routes, $cart_routes);
			}
			
			$translated_route .= $routes["$key"] ;

			if($translated_route) return '/'.$translated_route; else return false;
		}else{
			return false;
		}

	}

}
