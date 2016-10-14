<?php

class AppController extends App {	
	
	private static $title;
	private static $description;
	private static $keywords;
	private static $type = "website";
	private static $image;
	
	# __construct function
	# @access public
	# @param mixed $current_function - Reference to the function that will be executed. We keep the reference to be able to modify it later to show another view if necessary
	# @return void
	public function __construct(&$current_function=NULL, &$lang, &$db, &$helper, &$routes){
		
		$this->current_function = &$current_function;
		$this->db = $db;

		$lang = new Lang();
		$this->lang2 = $lang->lang2;
		$this->lang3 = $lang->lang3;
		$this->lang2_trans = $lang->lang2_trans;
		$this->lang3_trans = $lang->lang3_trans;
		$this->lang_trans_complete = $lang->lang_trans_complete;
		$this->possible_languages = $lang->possible_languages;
		$this->helper = $helper;
		$this->routes = $routes;

		/*foreach(glob("app/models/*.php") as $filename)
		{
			require_once($filename);
		}*/
		#$this->helper = new Helper();
	}

	# redirect()
	# @access public
	# @param $to_route
	# @return void
	public function redirect($to_route = NULL){
		if(!$to_route) $path = "http://$_SERVER[HTTP_HOST]".URL_ROOT.$this->lang2; else $path = "http://$_SERVER[HTTP_HOST]".URL_ROOT.$this->lang2.'/'.$this->routes[$to_route];
		#echo $path;
		header('Location: '.$path);
		exit;
	}

	# is_ajax()
	# @access public
	# @return true if it's an ajax request.
	function is_ajax(){
		return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
	}

	# getMeta()
	# @access public
	# @param $part
	# @return current_meta
	public function getMeta($part, $meta, $routes)
	{
		$current_meta = NULL;

		if(self::$title && $part == "title") $current_meta .= self::$title." | ";
		if(self::$description && $part == "description") $current_meta .= self::$description;
		if(self::$keywords && $part == "keywords") $current_meta .= self::$keywords;
		if(self::$image && $part == "image") $current_meta .= self::$image;
		if(self::$type && $part == "type") $current_meta .= self::$type;
		
		if(isset($_GET['param4']) && isset($meta["{$_GET['param4']}.{$part}"])) $current_meta .= $meta["{$_GET['param4']}.{$part}"]." | ";
		if(isset($_GET['param3']) && isset($meta["{$_GET['param3']}.{$part}"])) $current_meta .= $meta["{$_GET['param3']}.{$part}"]." | ";
		if(isset($_GET['param2']) && isset($meta["{$_GET['param2']}.{$part}"])) $current_meta .= $meta["{$_GET['param2']}.{$part}"]." | ";
		if(isset($_GET['param1']) && isset($meta["{$_GET['param1']}.{$part}"])) $current_meta .= $meta["{$_GET['param1']}.{$part}"]." | ";
		$current_meta .= $this->getMetaFromPage("{$part}", $meta, $routes);
		if(isset($_GET['param1'])) $current_meta .= $this->getMetaFromPageParam("{$part}", $meta, $routes);
		if(isset($meta["site.{$part}"])) $current_meta .= $meta["site.{$part}"];

		return $current_meta;
	}
	
	public function getMetaFromPage($part, $meta, $routes)
	{
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
	
	function getMetaFromPageParam($part, $meta, $routes)
	{
		$key = array_search("{$_GET['page']}/{$_GET['param1']}", $this->routes);

		if($meta["$key.$part"])
		{
			$string = $meta["$key.$part"];
			if($part == 'title') $string .= " | ";
			return $string;
		}else{
			return false;
		}
	}
	
	# getMetaURL()
	# @access public
	# @param void
	# @return current_meta url
	public function getMetaURL()
	{
		global $meta;
		$current_meta = "http://";
		if(isset($meta["site.url"])) $current_meta .= $meta["site.url"]."/".$this->lang2;
		if(isset($_GET["page"]) && $_GET["page"]!="index") $current_meta .= "/".$_GET['page'];
		if(isset($_GET['param1'])) $current_meta .=  "/".$_GET['param1'];
		if(isset($_GET['param2'])) $current_meta .=  "/".$_GET['param2'];
		if(isset($_GET['param3'])) $current_meta .=  "/".$_GET['param3'];
		if(isset($_GET['param4'])) $current_meta .=  "/".$_GET['param4'];
		
		return $current_meta;
	}
	
	# getPageType()
	# @access public
	# @param void
	# @return page type
	public function getPageType()
	{
		return self::$type;
	}
	
	# getImage()
	# @access public
	# @param void
	# @return image
	public function getImage()
	{
		return self::$image;
	}
	
	# setTitle()
	# @access public
	# @param $string
	# @return void
	public function setTitle($string)
	{
		self::$title = $string;
	}
	
	# setDescription()
	# @access public
	# @param $string
	# @return void
	public function setDescription($string)
	{
		self::$description = $string;
	}
	
	# setKeywords()
	# @access public
	# @param $string
	# @return void
	public function setKeywords($string)
	{
		self::$keywords = $string;
	}
	
	# setPageType()
	# @access public
	# @param $string
	# @return void
	public function setPageType($string)
	{
		self::$type = $string;
	}
	
	# setImage()
	# @access public
	# @param $string, $from_zap
	# @return void
	public function setImage($string, $from_zap = true)
	{
		if($from_zap) self::$image = "http://$_SERVER[HTTP_HOST]" . URL_ROOT . PUBLIC_FOLDER . WBR_FOLDER;
		self::$image .= $string;
	}
		
	# translateSlugOLD()
	# @access public
	# @param $slug, $paramNbr
	# @return void
	public function translateSlugOLD($slug, $paramNbr = NULL)
	{
		global $routes;
		
		if($slug!=$_GET["param{$paramNbr}"])
		{
			$path =  "http://$_SERVER[HTTP_HOST]".URL_ROOT.$this->lang2."/".$_GET["page"];
			if($paramNbr)
			{
				if($paramNbr==1) $path .= "/".$slug;
				if($paramNbr==2) $path .= "/".$_GET["param1"]."/".$slug;
				if($paramNbr==3) $path .= "/".$_GET["param1"]."/".$_GET["param2"]."/".$slug;
				if($paramNbr==4) $path .= "/".$_GET["param1"]."/".$_GET["param2"]."/".$_GET["param3"]."/".$slug;
				if($paramNbr==5) $path .= "/".$_GET["param1"]."/".$_GET["param2"]."/".$_GET["param3"]."/".$_GET["param4"]."/".$slug;
			}else{
				if(isset($_GET["param5"])) $path .= "/".$_GET["param1"]."/".$_GET["param2"]."/".$_GET["param3"]."/".$_GET["param4"]."/".$slug;
				elseif(isset($_GET["param4"])) $path .= "/".$_GET["param1"]."/".$_GET["param2"]."/".$_GET["param3"]."/".$slug;
				elseif(isset($_GET["param3"])) $path .= "/".$_GET["param1"]."/".$_GET["param2"]."/".$slug;
				elseif(isset($_GET["param2"])) $path .= "/".$_GET["param1"]."/".$slug;
				
				else $path .= "/".$slug;
			}
			header('Location: '.$path);
		}else return false;
	}
	
	# translateSlug()
	# @access public
	# @param $slug, $paramNbr
	# @return void
	public function translateSlug($param1, $param2 = NULL, $param3 = NULL, $param4 = NULL, $param5 = NULL)
	{
		
		$path =  "http://$_SERVER[HTTP_HOST]".URL_ROOT.$this->lang2."/".$_GET["page"];
		
		if($param1) $path .= "/".$param1;
		if($param2) $path .= "/".$param2;
		if($param3) $path .= "/".$param3;
		if($param4) $path .= "/".$param4;
		if($param5) $path .= "/".$param5;
		
		if(isset($_GET["param1"]) && $param1!=$_GET["param1"] && !isset($_GET["param2"]))
		{
			header('Location: '.$path);
		}elseif(isset($_GET["param1"]) && $param1!=$_GET["param1"] && isset($_GET["param2"]) && $param2!=$_GET["param2"] && !isset($_GET["param3"]))
		{
			header('Location: '.$path);
		}elseif(isset($_GET["param1"]) && $param1!=$_GET["param1"] && isset($_GET["param2"]) && $param1!=$_GET["param2"] && isset($_GET["param3"]) && $param3!=$_GET["param3"] && !isset($_GET["param4"]))
		{
			header('Location: '.$path);
		}elseif(isset($_GET["param1"]) && $param1!=$_GET["param1"] && isset($_GET["param2"]) && $param1!=$_GET["param2"] && isset($_GET["param3"]) && $param3!=$_GET["param3"] && isset($_GET["param4"]) && $param4!=$_GET["param4"] && !isset($_GET["param5"]))
		{
			header('Location: '.$path);
		}elseif(isset($_GET["param1"]) && $param1!=$_GET["param1"] && isset($_GET["param2"]) && $param1!=$_GET["param2"] && isset($_GET["param3"]) && $param3!=$_GET["param3"] && isset($_GET["param4"]) && $param4!=$_GET["param4"] && isset($_GET["param5"]) && $param4!=$_GET["param5"])
		{
			header('Location: '.$path);
		}else{
			return false;
		}
		
		return false;

	}

}
