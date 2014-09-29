<?php
require_once("app/models/App.php");
	

/*
|--------------------------------------------------------------------------
| Base Model
|--------------------------------------------------------------------------
|
| To access a function from the App Model, do something
| like that : parent::writePrettyDate("2014-02-11");
|
*/
class Base extends \App {
	
	function __construct($db, $lang3){
		$this->table = 'activities';
		$this->name = 'activity';
		parent::__construct($db, $lang3, $this->table);
	}
	
	public static function table(){
		return 'activities';	
	}
	
	public function catacts(){
		return parent::joinTable("catacts", "catacts.id = activities.catact_id", "LEFT JOIN");
	}


}
?>