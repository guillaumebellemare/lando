<?php

class Base extends App {
	
	public function __construct() {
		$this->table = 'activities';
		parent::__construct($this->table);
	}
	
	public function getAllBar(){
		return $this->select($this->table)->left_join("catacts")->order_by("$this->table.rank ASC")->where("catacts.rank = 1")->all();
	}

}
