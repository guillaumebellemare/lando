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
	
	function getMeta($part)
	{
		global $meta;
		$current_meta = NULL;
		
		if(isset($_GET['param4']) && isset($meta["{$_GET['param4']}.{$part}"])) $current_meta .= $meta["{$_GET['param4']}.{$part}"]." | ";
		if(isset($_GET['param3']) && isset($meta["{$_GET['param3']}.{$part}"])) $current_meta .= $meta["{$_GET['param3']}.{$part}"]." | ";
		if(isset($_GET['param2']) && isset($meta["{$_GET['param2']}.{$part}"])) $current_meta .= $meta["{$_GET['param2']}.{$part}"]." | ";
		if(isset($_GET['param1']) && isset($meta["{$_GET['param1']}.{$part}"])) $current_meta .= $meta["{$_GET['param1']}.{$part}"]." | ";
		$current_meta .= $this->getMetaFromPage("{$part}");
		if(isset($meta["site.{$part}"])) $current_meta .= $meta["site.{$part}"];
		
		return $current_meta;
	}
	
	function getMetaURL()
	{
		global $meta, $lang2;
		$current_meta = "http://";
		if(isset($meta["site.url"])) $current_meta .= $meta["site.url"]."/".$lang2;
		if(isset($_GET["page"]) && $_GET["page"]!="index") $current_meta .= "/".$_GET['page'];
		if(isset($_GET['param1'])) $current_meta .=  "/".$_GET['param1'];
		if(isset($_GET['param2'])) $current_meta .=  "/".$_GET['param2'];
		if(isset($_GET['param3'])) $current_meta .=  "/".$_GET['param3'];
		if(isset($_GET['param4'])) $current_meta .=  "/".$_GET['param4'];
		
		return $current_meta;
	}

}
