<?php 

class Lang {
	
	function __construct($possible_languages){
		
		session_start();
		$this->setLanguages($possible_languages);

		// If no lang defined ... 
		if(!isset($_GET['lang'])) $_GET['lang'] = $_SESSION['lang'];
			
			//If we switch between two languages
			if(count($this->possible_languages) == 2){
				if($_GET['lang'] == 'en'){
					$this->lang2 = 'en';
					$this->lang2_trans = 'fr';
					$this->lang3 = 'eng';
					$this->lang2_trans_complete = 'Français';
				}else{
					$this->lang2 = 'fr';
					$this->lang2_trans = 'en';
					$this->lang3 = 'fre';
					$this->lang2_trans_complete = 'English';
				}
			}else{
				foreach($this->possible_languages as $language => $code)
				{
					if($_GET['lang'] == $language)
					{
						$this->lang2 = $language;
						$this->lang3 = $code;
					}
				}
			}
			
		// Store in a session
		$_SESSION['lang'] = $this->lang2;
		
	}
	
	public function setLanguages($possible_languages){
		$this->possible_languages = $possible_languages;
	}
	
}


