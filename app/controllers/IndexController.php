<?php

/*
|--------------------------------------------------------------------------
| IndexController
|--------------------------------------------------------------------------
|
|
|
*/

class IndexController extends \AppController {

	function index() {
		
		global $db, $lang3;
		
		# Model declaration
		$base = new Base($db, $lang3);
		
		# Slug creation
		
		# Data
		// $foo = $base->getAllBar();
		
		// return array("foo" => $foo);
	}
	
}
