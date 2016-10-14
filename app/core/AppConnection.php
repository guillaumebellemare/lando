<?php

class AppConnection {
	
	private static $instance = NULL;
	
	private function __construct() {}
	
	private function __clone() {}
	
	public static function getInstance() {
		
		if(!isset(self::$instance)) {
			try {
				# Required file, you might have to modify the paths to work with your configuration
				require(ADMIN_PATH . 'lib/php/adodb5/adodb.inc.php');
				require(ADMIN_PATH . 'app/config/db.php');

				# Create ADO object & connect to the database
				if(!ADONewConnection(DB_TYPE)) throw new CustomException('Veuillez v&eacute;rifier que le type de base de donn&eacute;es corresponde'); else self::$instance = ADONewConnection(DB_TYPE);
				if(!self::$instance->Connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME)) throw new CustomException('Un probl&egrave;me est survenu lors de la connection &agrave; la base de donn&eacute;es.'); else self::$instance->Connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);
				
				# Allow MySQL to query in UTF8 encoding
				self::$instance->Execute('SET NAMES utf8');

			}catch(CustomException $e) {
				if(DEBUG) echo $e->errorMessage($e), "\n";
			}
			
      	}
		return self::$instance;

	}
	
	public static function closeConnection() {
		if(isset(self::$instance)) {
			self::$instance->Close();
			self::$instance = NULL;
		}
	}
}