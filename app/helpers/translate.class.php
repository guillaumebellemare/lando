<?php 

class Translate {
	
	function translateFromPage()
	{
		global $routes, $lang2_trans, $lang2;
		
		$key = array_search("{$_GET['page']}", $routes);
		
		if($routes["$key"] && $routes["$key"] != 'index')
		{
			include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'lang/'.$lang2_trans.'/routes.php');
			$translated_route .= $routes["$key"] ;
			include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'lang/'.$lang2.'/routes.php');
			return '/'.$translated_route;
		}else{
			return false;
		}

	}

}
