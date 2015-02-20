<?php

/*
|--------------------------------------------------------------------------
| App Controller
|--------------------------------------------------------------------------
*/

class AppController extends SluggedRecord {	
	
	function writePrettyDate($date){
		
		$returnDate = NULL;
		
		$date = explode(',', $date);
		
		$firstDate = explode('-', $date[0]);
		$firstDateMonth = $this->writePrettyMonth($firstDate[1]);
		$firstDateSend = $firstDate[2].' '.$firstDateMonth.' '.$firstDate[0];
		if(count($date) == 1){
			$returnDate = '' . $firstDateSend;
		}else {
			$secondDate = explode('-', $date[1]);
			$secondDateMonth = $this->writePrettyMonth($secondDate[1]);
			$secondDateSend = $secondDate[2].' '.$secondDateMonth.' '.$secondDate[0];
						
			# Only one date
			if($firstDate==$secondDate) $returnDate = '' . $firstDateSend;
			else {
				# Two dates of the same year
				if($firstDate[0]==$secondDate[0]) $returnDate = $firstDate[2].' '.$firstDateMonth.' au '.$secondDate[2].' '.$secondDateMonth.' '.$secondDate[0];
				# Two dates of the same month
				if($firstDate[1]==$secondDate[1]) $returnDate = $firstDate[2].' au '.$secondDate[2].' '.$secondDateMonth.' '.$secondDate[0];
				# DEFAULT 
				if($returnDate==NULL) $firstDateSend.' au '.$secondDateSend;	 
			}
			
		}
		
		return $returnDate;
	}
	
	function writePrettyMonth($month)
	{
		switch ($month) {
			case '01':
				$month = 'janvier';
			break;
			case '02':
				$month = 'février';
			break;
			case '03':
				$month = 'mars';
			break;
			case '04':
				$month = 'avril';
			break;
			case '05':
				$month = 'mai';
			break;
			case '06':
				$month = 'juin';
			break;
			case '07':
				$month = 'juillet';
			break;
			case '08':
				$month = 'août';
			break;
			case '09':
				$month = 'septembre';
			break;
			case '10':
				$month = 'octobre';
			break;
			case '11':
				$month = 'novembre';
			break;
			case '12':
				$month = 'décembre';
			break;
		}
		
		return $month;	
	}
	
}
