<?php 
// Debug mode (set to 1 if you want to debug
if(!defined('DEBUG_MODE'))define('DEBUG_MODE', 0);
// Debug mode for DB, set to 1 if you want to log all insert and update queries, set to 2 if you want to output every queries
if(!defined('DEBUG_MODE_DB'))define('DEBUG_MODE_DB',0);
// Enable caching or not
if(!defined('ENABLE_CACHING'))define('ENABLE_CACHING',0);
// Default helpers to load in every controllers
if(!defined('DEFAULT_HELPERS'))define('DEFAULT_HELPERS', "html,form,theme");
// SITE_NAME
if(!defined('SITE_NAME'))define('SITE_NAME', 'Le Canard Huppé');
// VERSION_UPDATE
if(!defined('VERSION_UPDATE'))define('VERSION_UPDATE', 'v4');
// URL to the main admin server. Updates are downloaded from there.
if(!defined('UPDATE_SERVER_URL'))define('UPDATE_SERVER_URL', "http://za-propa.legrandroux.com/");
// ARCHIVE_TYPE
if(!defined('ARCHIVE_TYPE'))define('ARCHIVE_TYPE', 'zip');
// Support email address
if(!defined('MAIL_SUPPORT'))define('MAIL_SUPPORT', "support@devcreative.net");
// Support name
if(!defined('NAME_SUPPORT'))define('NAME_SUPPORT', "Support Propaganda-Design");
// Mail server type, can be mail, gmail_smtp, local_smtp or N/A
if(!defined('MAIL_SERVER_TYPE'))define('MAIL_SERVER_TYPE', "N/A");
// COPYRIGHT
if(!defined('COPYRIGHT'))define('COPYRIGHT', 'Zone Administrative');
// XML_FOLDER
if(!defined('XML_FOLDER'))define('XML_FOLDER', '../images/wbr//xml');
// TMP_FOLDER
if(!defined('TMP_FOLDER'))define('TMP_FOLDER', '../images/wbr//tmp');
// UPLOAD_FOLDER
if(!defined('UPLOAD_FOLDER'))define('UPLOAD_FOLDER', '../images/wbr//uploads');
// MAX_UPLOAD_FILE_SIZE
if(!defined('MAX_UPLOAD_FILE_SIZE'))define('MAX_UPLOAD_FILE_SIZE', 32);
// Uploadify path, it has to be outside of a Basic Auth folder
if(!defined('UPLOADIFY_PATH'))define('UPLOADIFY_PATH', "lib/php/");
// Jquery Lib version
if(!defined('JQUERY_VERSION'))define('JQUERY_VERSION', "1.7.1");
// Jquery UI Lib version
if(!defined('JQUERYUI_VERSION'))define('JQUERYUI_VERSION', "1.8.16.custom");

// Path to the root, dynamically extracted from __FILE__
if(!defined('ROOT_FOLDER'))define('ROOT_FOLDER', substr(dirname(__FILE__),0, -10));

// Should be unique to the server, is used to encode stuff
if(!defined('UNIQUE_SALT'))define('UNIQUE_SALT', "x0ZHCLNrb0!aZzu6XsQBp%");

// Should determine if passwords have already been encoded according to most recent method.
if(!defined('PASSWORD_ENCODE'))define('PASSWORD_ENCODE', true);
?>