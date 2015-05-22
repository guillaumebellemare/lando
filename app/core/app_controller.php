<?php

class AppController extends SluggedRecord {	
	
	# __construct function
	# @access public
	# @param mixed $current_function - Reference to the function that will be executed. We keep the reference to be able to modify it later to show another view if necessary
	# @return void
	public function __construct(&$current_function=NULL){
		$this->current_function = &$current_function;
	}
	
	# invalidateAndRedirect()
	# @access public
	# @param $new_action - Allows us to redirect to another action while keeping the same variables
	# @return new action
	public function invalidateAndRedirect($new_action){
		$this->current_function = $new_action;
		return $this->$new_action();
	}
	
	# redirect()
	# @access public
	# @param $to_route
	# @return void
	public function redirect($to_route){
		global $lang2, $routes;
		header('Location: '."http://$_SERVER[HTTP_HOST]".URL_ROOT.$lang2.'/'.$routes[$to_route]);
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
	
}
