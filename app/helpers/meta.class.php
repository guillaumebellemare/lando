<?php 

class Meta {
	
	function getMetaFromPage($part)
	{
		global $routes, $meta;
		$key = array_search("{$_GET['page']}", $routes);

		if($meta["$key.$part"])
		{
			$string = $meta["$key.$part"];
			if($part == 'title') $string .= " | ";
			return $string;
		}else{
			return false;
		}
	}
	
	function getMetaFromPageParam($part)
	{
		global $routes, $meta;
		$key = array_search("{$_GET['page']}/{$_GET['param1']}", $routes);

		if($meta["$key.$part"])
		{
			$string = $meta["$key.$part"];
			if($part == 'title') $string .= " | ";
			return $string;
		}else{
			return false;
		}
	}

}
