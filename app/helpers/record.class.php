<?php

class Record {

	protected $table;
	protected $lang3;

	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param mixed $db La référence à la base de donnée (adodbphp).
	 * @param mixed $lang3 Le code à trois lettres de la langue selectionnée.
	 * @param mixed $table Le nom de la table associé à l'instance de cette classe.
	 * @return void
	 */
	function __construct($db=NULL, $lang3=NULL, $table=NULL){
		$this->db = $db;
		$this->lang3 = $lang3;
		$this->table = $table;
		$this->sortBy = " ORDER BY {$this->table}.rank";
	}
	

}
