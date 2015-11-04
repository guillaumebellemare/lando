<?php

class AppController extends SluggedRecord {	
	
	private static $title;
	private static $description;
	private static $keywords;
	private static $type = "website";
	private static $image;
	
	# __construct function
	# @access public
	# @param mixed $current_function - Reference to the function that will be executed. We keep the reference to be able to modify it later to show another view if necessary
	# @return void
	public function __construct(&$current_function=NULL){
		
		global $lang2, $lang3;
		
		$this->lang3 = $lang3;
		$this->lang2 = $lang2;
		if($lang3=="fre") $this->lang3_trans = "eng"; else $this->lang3_trans = "fre";
		$this->current_function = &$current_function;
	}
	
	# redirect()
	# @access public
	# @param $to_route
	# @return void
	public function redirect($to_route = NULL){
		global $routes;
		if(!$to_route) $path = "http://$_SERVER[HTTP_HOST]".URL_ROOT.$this->lang2; else $path = "http://$_SERVER[HTTP_HOST]".URL_ROOT.$this->lang2.'/'.$routes[$to_route];
		header('Location: '.$path);
		exit;
	}

	# writePrettyDate()
	# @access public
	# @param string $date
	# @return readable date
	public function writePrettyDate($date){
		
		if($this->lang3=="fre") $word_link = "au"; else $word_link = "to";
		$return_date = NULL;
		
		$date = explode(',', $date);
		
		$first_date = explode('-', $date[0]);
		$first_date_day = (int)$first_date[2];
		$first_date_month = $this->writePrettyMonth($first_date[1]);
		$first_date_year = $first_date[0];
		
		if($this->lang3=="fre" && $first_date_day==1) $first_date_day = $first_date_day."<sup>er</sup>";
		
		$first_date_send = $first_date_day." ".$first_date_month." ".$first_date_year;
		
		if(count($date) == 1){
			$return_date = $first_date_send;
		}else {
			$second_date = explode('-', $date[1]);
			$second_date_day = (int)$second_date[2];
			$second_date_month = $this->writePrettyMonth($second_date[1]);
			$second_date_year = $second_date[0];
			$second_dateSend = $second_date_day." ".$second_date_month." ".$second_date_year;
			
			if($this->lang3=="fre" && $second_date_day==1) $second_date_day = $second_date_day."<sup>er</sup>";
			
			# Only one date
			if($first_date==$second_date)
			{
				$return_date = $first_date_send;
			}else {
				if($this->lang3=="fre")
				{
					#######################
					## French formatting ##
					#######################
					
					# Two dates of the same year
					if($first_date_year == $second_date_year) $return_date = $first_date_day." ".$first_date_month." $word_link ".$second_date_day." ".$second_date_month." ".$second_date_year;
					# Two dates of the same month
					if($first_date_month === $second_date_month) $return_date = $first_date_day." $word_link ".$second_date_day." ".$second_date_month." ".$second_date_year;
					
					# Two dates of different year
					if($first_date_year != $second_date_year) $return_date = $first_date_day." ".$first_date_month." ".$first_date_year." $word_link ".$second_date_day." ".$second_date_month." ".$second_date_year;
					
					# Default
					if($return_date==NULL) $first_date_send." $word_link ".$second_dateSend;
					
				}elseif($this->lang3=="eng"){
					########################
					## English formatting ##
					########################
					
					# Two dates of the same year
					if($first_date_year == $second_date_year) $return_date = $first_date_month." ".$first_date_day." $word_link ".$second_date_month." ".$second_date_day.", ".$second_date_year;
					# Two dates of the same month
					if($first_date_month === $second_date_month) $return_date = $second_date_month." ".$first_date_day." $word_link ".$second_date_day.", ".$second_date_year;
					
					# Two dates of different year
					if($first_date_year != $second_date_year) $return_date = $first_date_month." ".$first_date_day.", ".$first_date_year." $word_link ".$second_date_month." ".$second_date_day.", ".$second_date_year;
					
					# Default 
					if($return_date==NULL) $first_date_send." $word_link ".$second_dateSend;	 
				}
			}
		}
		
		return $return_date;
	}

	# writePrettyMonth()
	# @access public
	# @param string $month
	# @return readable montb
	public function writePrettyMonth($month)
	{
		if($this->lang3=="fre")
		{
			switch ($month) {
				case "01":
					$month = "janvier";
				break;
				case "02":
					$month = "février";
				break;
				case "03":
					$month = "mars";
				break;
				case "04":
					$month = "avril";
				break;
				case "05":
					$month = "mai";
				break;
				case "06":
					$month = "juin";
				break;
				case "07":
					$month = "juillet";
				break;
				case "08":
					$month = "août";
				break;
				case "09":
					$month = "septembre";
				break;
				case "10":
					$month = "octobre";
				break;
				case "11":
					$month = "novembre";
				break;
				case "12":
					$month = "décembre";
				break;
			}
		}elseif($this->lang3=="eng"){
			switch ($month) {
				case "01":
					$month = "January";
				break;
				case "02":
					$month = "February";
				break;
				case "03":
					$month = "March";
				break;
				case "04":
					$month = "April";
				break;
				case "05":
					$month = "May";
				break;
				case "06":
					$month = "June";
				break;
				case "07":
					$month = "July";
				break;
				case "08":
					$month = "August";
				break;
				case "09":
					$month = "September";
				break;
				case "10":
					$month = "October";
				break;
				case "11":
					$month = "November";
				break;
				case "12":
					$month = "December";
				break;
			}
		}
		
		return $month;	
	}
	
	public function getMetaFromPage($part)
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

	# getMeta()
	# @access public
	# @param $part
	# @return current_meta
	public function getMeta($part)
	{
		global $meta;
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
		$current_meta .= $this->getMetaFromPage("{$part}");
		$current_meta .= $this->getMetaFromPageParam("{$part}");
		if(isset($meta["site.{$part}"])) $current_meta .= $meta["site.{$part}"];
		
		return $current_meta;
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
