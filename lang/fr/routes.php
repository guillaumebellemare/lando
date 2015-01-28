<?php
	$routes = array(
		"index" => "index",
		"index_show" => "index/{$_GET['arga']}",
		"manage" => "manage",
		"manage_add" => "manage/save",
		"manage_form" => "manage/form",
		"manage_remove" => "manage/remove/{$_GET['argb']}",
	);
?>