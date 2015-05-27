<?php

class Base extends App {
	
	public function __construct() {
		$this->table = 'activities';
		$this->table_code = 'activity';
		parent::__construct($this->table, $this->table_code);
	}
	
	public function getAllBar(){
		return $this->select($this->table)->left_join("catacts")->order_by("$this->table.rank ASC")->where("catacts.rank = 1")->all();
	}

}
