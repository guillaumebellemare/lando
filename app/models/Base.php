<?php

class Base extends \App {
	
	public function __construct() {
		global $db, $lang3;
		$this->table = 'activities';
		parent::__construct($db, $lang3, $this->table);
	}
	
	public function getAllActivities(){
		return $this->get($this->table);
	}

}