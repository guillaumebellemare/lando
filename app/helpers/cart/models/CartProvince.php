<?php

class CartProvince extends App {
	
	public function __construct() {
		$this->table = 'provinces';
		$this->table_code = 'province';
		parent::__construct($this->table, $this->table_code);
	}

	function getIdFromName($name){
		$provinces = $this->select($this->table)->where("{$this->table}.name_fre = '$name' OR  {$this->table}.name_eng = '$name'")->all(false);
		$current_province = current($provinces);
		return $current_province['provinces.id'];
	}


}
