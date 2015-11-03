<?php

$routes = array(
	"index" => "index",
	"download" => "download/{$_GET['param1']}",
	"download_path" => "download/{$_GET['param1']}/{$_GET['param2']}",
);
