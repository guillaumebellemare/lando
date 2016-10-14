<?php

class CustomException extends Exception {
 
	public function errorMessage($message) {
		# Error message
		/* $errorMsg = 'Error on line '.$this->getLine().' in '.$this->getFile()
		.': <b>'.$this->getMessage().'</b> is not a valid E-Mail address';*/
		echo "<style>";
		if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'styles/app.css'));
		echo "</style>";

		return '<div class="exceptions">'.$this->getMessage().'</div>';
	}
  
}