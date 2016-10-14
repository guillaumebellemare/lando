<?php

class Model extends AppModel {
	
	public function __construct() {
		$this->table = 'tables';
		$this->table_code = 'table';

		parent::__construct($this->table, $this->table_code);
	}
	
	public function getAllBar() {
		//return $this->select($this->table)->left_join("catacts")->order_by("$this->table.rank ASC")->where("catacts.rank = 1")->all();
	}

}
