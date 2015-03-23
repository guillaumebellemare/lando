<?php

class Base extends App {
	
	public function __construct() {
		$this->table = 'activities';
		parent::__construct($this->table);
	}
	
	public function getAllBar(){
		return $this->select($this->table)->left_join("catacts")->order_by("$this->table.rank ASC")->order_by("catacts.rank ASC")->where("$this->table.rank = 1")->where("catacts.rank = 1")->all();
	}

}