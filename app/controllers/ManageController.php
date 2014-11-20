<?php
/*
|--------------------------------------------------------------------------
| Manage Controller
|--------------------------------------------------------------------------
*/

class ManageController extends \AppController {


	function index() {
		
		global $db, $lang3;
		
		# Slug management
		$base = new Base($db, $lang3);
		$base->create_slug_field('activity', "name_$lang3", "slug_$lang3", "URL Slug");
		
		
		# Data
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