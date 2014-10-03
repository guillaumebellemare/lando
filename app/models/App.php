<?php
require_once("app/helpers/sluggedrecord.class.php");

class App extends SluggedRecord {
	
	var $db = null;	
	var $name;
	
	function __construct($db, $lang3){
		$this->name = 'app';
		parent::__construct($db, $lang3, $this->table);
	}
	
	function joinTable($table, $on, $type)
	{
		return array("table" => $table, "on" => $on, "type" => $type);
	}
}
?>