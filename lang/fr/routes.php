<?php
	$routes = array(
		"index" => "index",
		"index_show" => "index/{$_GET['arga']}.html",
		"manage" => "manage",
		"manage_add" => "manage/save.html",
		"manage_form" => "manage/form.html",
		"manage_remove" => "manage/remove/{$_GET['argb']}.html",
	);
?>