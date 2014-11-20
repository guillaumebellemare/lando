<?php
/*
|--------------------------------------------------------------------------
| Index Controller
|--------------------------------------------------------------------------
*/

class IndexController extends \AppController {


	function index() {
		
		global $db, $lang3;
		
		# Slug management
		$base = new Base($db, $lang3);
		$base->create_slug_field('activity', "name_$lang3", "slug_$lang3", "URL Slug");
		
		
		# Data
		$activities2 = $this->get(Base::table());
		$activities = $this->get(Base::table(), "catact_id, rank", 1, Base::catacts());

		return array("activities" => $activities, "activities2" => $activities2);
	}
	
	
	function show() {
		
		global $db, $lang3;
		
		# Data
		$q = "SELECT activities.id AS activity_id, activities.name_{$lang3} AS activity_name, activities.description_fre AS activity_description, activities.test AS activity_test, activities.slug_{$lang3}, catacts.name_{$lang3} AS category_name FROM activities";
		$q .= " LEFT JOIN catacts ON catacts.id = activities.catact_id WHERE activities.slug_{$lang3} = '{$_GET['arga']}'";
		$q .= " AND activities.active = 1";
		
		$activities = $this->custom_get($q);
	
		return array("activities" => $activities);
	}
	
}
?>