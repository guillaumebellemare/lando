<?php
/*
|--------------------------------------------------------------------------
| Manage Controller
|--------------------------------------------------------------------------
|
| To access a function from the AppController, do something like that :
| $this->writePrettyDate("2014-02-11");
|
*/
class ManageController extends \AppController {

	/*
	|--------------------------------------------------------------------------
	| __construct
	|--------------------------------------------------------------------------
	|
	| Initialization of the Controller
	|
	*/
	function __construct($db, $lang3){
		$this->name = 'activity';
		parent::__construct($db, $lang3, $this->table);
	}

	/*
	|--------------------------------------------------------------------------
	| Data management
	|--------------------------------------------------------------------------
	|
	| Here is where the data is retrieved from the controller
	| and returned to the view via arrays
	|
	*/
	function index() {
		
		global $db, $lang3;
		
		/************************
		**** Slug management ****
		************************/
		$base = new Base($db, $lang3);
		$base->create_slug_field('activity', "name_$lang3", "slug_$lang3", "URL Slug");
		
		
		/*************
		**** Data ****
		**************/
		$activities = $this->get(Base::table(), "catact_id, rank", 1, Base::catacts());

		return array("activities" => $activities);
	}
	
	function form() {
	}

	function save() {
		$this->add(Base::table(), "name_fre");	
	}
	
	function remove() {
		$this->delete(Base::table(), $_GET['argb']);
	}
	
}
?>