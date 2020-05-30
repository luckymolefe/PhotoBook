<?php
  	//photobook handle db connection
	$fullpath = $_SERVER['DOCUMENT_ROOT'].'/photobook';
	require_once($fullpath.'/assets/credentials.php');
	/**
	* @define interface class: describe class interface with methods to be used
	*/
	interface connection_Adapter {
		public static function getInstance();
		// protected static function connect();
	}
	/**
	* @define abstract class: describe the abstract class methods.
	*/
	abstract class resource_Adapter {
		// abstract protected static function connect();
		private static $conn = null;
		protected static function connect() {
			try {
				self::$conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USERNAME, DB_PASSWORD);
			}
			catch(PDOException $e) {
				echo "DB Error: ".$e->getMessage();
			}
			return self::$conn;
		}
	}
	/**
	* DB connection class
	* @return object conn
	*/
	class DB_Adapter extends resource_Adapter implements connection_Adapter {
		// private static $conn = null;

		/*public static function __construct($conn) {
			self::$conn = $conn;
		}*/

		public static function getInstance() {
			return self::connect();
		}
		public function __destruct() {
			self::connect();
		}
		/*protected static function connect() {
			try {
				self::$conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USERNAME, DB_PASSWORD);
			}
			catch(PDOException $e) {
				echo "Error: ".$e->getMessage();
			}
			return self::$conn;
		}*/
	}

	// $db = new DB_Adapter();
	
	/*if($db::getInstance()) {
		echo "connected";
	} else {
		echo "Could not establish connection!.";
	}*/
?>