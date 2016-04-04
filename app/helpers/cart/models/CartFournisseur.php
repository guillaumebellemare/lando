<?php

class CartFournisseur extends App {
	
	public function __construct() {
		$this->table = 'fournisseurs';
		$this->table_code = 'fournisseur';
		parent::__construct($this->table, $this->table_code);
	}


}
