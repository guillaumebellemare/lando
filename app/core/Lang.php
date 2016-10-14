<?php

class Lang {
	
	public $lang2 = NULL;
	public $lang3 = NULL;
	public $lang2_trans = NULL;
	public $lang3_trans = NULL;
	public $lang_trans_complete = NULL;
	public $possible_languages = NULL;
	
	public function __construct() {
		global $possible_languages;
		$this->possible_languages = $possible_languages;

		# If we only switch between two languages
		if(count($this->possible_languages) == 2){
			if($_GET['lang'] == 'en'){
				$this->lang2 = 'en';
				$this->lang3 = 'eng';
				$this->lang2_trans = 'fr';
				$this->lang3_trans = 'fre';
				$this->lang_trans_complete = 'Français';
			}else{
				$this->lang2 = 'fr';
				$this->lang3 = 'fre';
				$this->lang2_trans = 'en';
				$this->lang3_trans = 'eng';
				$this->lang_trans_complete = 'English';
			}
		}else{
			foreach($this->possible_languages as $language => $code)
			{
				if($_GET['lang'] == $language)
				{
					$this->lang2 = $language;
					$this->lang3 = $code;
					$this->lang = array($this->lang2, $this->lang3);
				}
			}
		}
		
	}
	
}

