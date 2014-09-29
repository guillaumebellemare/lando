<?php 
class Lang{
	function __construct(){
		session_start();
		//if no lang defined ... 
		if(!isset($_GET['lang'])) $_GET['lang'] = $_SESSION['lang'];
		//
			if($_GET['lang'] == 'en'){
				$this->lang2 = 'en';
				$this->lang2_trans = 'fr';
				$this->lang3 = 'eng';
				$this->lang2_trans_complete = 'Français';
			} else {
				$this->lang2 = 'fr';
				$this->lang2_trans = 'en';
				$this->lang3 = 'fre';
				$this->lang2_trans_complete = 'English';
			}
		//store in a session
		$_SESSION['lang'] = $this->lang2;
	}
	
	
}

?>