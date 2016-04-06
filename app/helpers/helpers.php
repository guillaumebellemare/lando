<?php

class Helper extends App {
	
	public function __construct() {
		
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
					# French formatting
					
					# Two dates of the same year
					if($first_date_year == $second_date_year) $return_date = $first_date_day." ".$first_date_month." $word_link ".$second_date_day." ".$second_date_month." ".$second_date_year;
					# Two dates of the same month
					if($first_date_month === $second_date_month) $return_date = $first_date_day." $word_link ".$second_date_day." ".$second_date_month." ".$second_date_year;
					
					# Two dates of different year
					if($first_date_year != $second_date_year) $return_date = $first_date_day." ".$first_date_month." ".$first_date_year." $word_link ".$second_date_day." ".$second_date_month." ".$second_date_year;
					
					# Default
					if($return_date==NULL) $first_date_send." $word_link ".$second_dateSend;
					
				}elseif($this->lang3=="eng"){
					# English formatting
					
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
		global $lang3;
		
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
	
	# limitStringSize()
	# @access public
	# @param string $string, $size
	# @return cropped string
	public function limitStringSize($string, $size=200)
	{
		$pos = strpos($string, ' ', $size);
		$cropped_string = substr($string,0,$pos);
		if(strlen($cropped_string) >= $size) $string = $cropped_string;
		return $string;
	}
	
	# formatMoney()
	# @access public
	# @param string $number
	# @return formatted number
	public function formatMoney($number)
	{
		setlocale(LC_MONETARY, "fr_CA");
		if($this->lang3=="fre") $number = money_format('%!.0n', $number)." $";
		else $number = money_format('$ %!.0n', $number);
		return $number;
	}
	
	# getPicturePath()
	# @access public
	# @param string $string
	# @return picture path
	public function getPicturePath($string){
		$a = explode("::", $string);
		return $a[0];
	}
	
	# getPictureInfo()
	# @access public
	# @param string $string
	# @return array() of picture infos
	public function getPictureInfo($string){
		$a = explode('::', $string);
		return array('file' => $a[0], 'cropdata' => $a[1]);
	}
	
	# nl2p()
	# @access public
	# @param string $string, $line_breaks, $xml
	# @return trimmed string
	public function nl2p($string, $line_breaks = true, $xml = true) {

		$string = str_replace(array('<p>', '</p>', '<br>', '<br />'), '', $string);
		
		# It is conceivable that you might still want single line-breaks without breaking into a new paragraph.
		if ($line_breaks == true)
		    return '<p>'.preg_replace(array("/([\n]{2,})/i", "/([^>])\n([^<])/i"), array("</p>\n<p>", '$1<br'.($xml == true ? ' /' : '').'>$2'), trim($string)).'</p>';
		else 
		    return '<p>'.preg_replace(
		    array("/([\n]{2,})/i", "/([\r\n]{3,})/i","/([^>])\n([^<])/i"),
		    array("</p>\n<p>", "</p>\n<p>", '$1<br'.($xml == true ? ' /' : '').'>$2'),
		
		    trim($string)).'</p>'; 
	}
	
	# compact_list()
	# @access public
	# @param string $a, $field
	# @return $a_compact
	public function compact_list($a, $field){
		$a_compact = array();
		foreach($a as $row){
			$a_compact[] = $row[$field];
		}
		return $a_compact;
	}
	
	# readSecuredFile()
	# @access public
	# @param string $file
	# @return $secured_file
	public function readSecuredFile($file){

		global $routes;
		
		$secured_file_link = $this->lang2."/".$routes["download"].$file;
		return $secured_file_link;
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

}
