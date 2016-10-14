<?php

class IndexController extends AppController {
	
	public function index() {

		# Model declaration
		$model = new Model();

		# Slug creation
		
		# Data
		$foo = $model->getAllBar();
		
		# Meta

		# Returns
		return array("foo" => $foo);
	}
	
}