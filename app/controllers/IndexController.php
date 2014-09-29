<?php
/*
|--------------------------------------------------------------------------
| Index Controller
|--------------------------------------------------------------------------
|
| To access a function from the AppController, do something like that :
| $this->writePrettyDate("2014-02-11");
|
*/
class IndexController extends \AppController {

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
		
		/***************************************
		**** Call models that you need here ****
		****************************************/
		require_once('app/models/Base.php');
		
		
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
	
	
	function show() {
		
		global $db, $lang3;
		
		/*************
		**** Data ****
		**************/
		$q = "SELECT activities.id AS activity_id, activities.name_{$lang3} AS activity_name, activities.description_fre AS activity_description, activities.test AS activity_test, activities.slug_{$lang3}, catacts.name_{$lang3} AS category_name FROM activities";
		$q .= " LEFT JOIN catacts ON catacts.id = activities.catact_id WHERE activities.slug_{$lang3} = '{$_GET['arga']}'";
		$q .= " AND activities.active = 1";
		
		$activities = $this->custom_get($q);
	
		return array("activities" => $activities);
	}
}
?>