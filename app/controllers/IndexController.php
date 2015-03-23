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
		
		global $lang3;
		
		# Model declaration
		$base = new Base();
		
		# Slug creation
		
		# Data
		$foo = $base->getAllBar();
		
		//return array("foo" => $foo);
	}
	
}
