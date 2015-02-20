<?php

define("ADMIN_PATH", 'zap/');
# Required file, you might have to modify the paths to work with your configuration
require(ADMIN_PATH . "lib/php/adodb5/adodb.inc.php");
require(ADMIN_PATH . "app/config/db.php");

# Create ADO object & connect to the database
$db = ADONewConnection(DB_TYPE);
$db->Connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);

# Allow MySQL to query in utf8 encoding
$db->Execute("SET NAMES utf8");
