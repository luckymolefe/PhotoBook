<?php
	//friends recommendation model
	$fullpath = $_SERVER['DOCUMENT_ROOT'].'/photobook';
	if(in_array('connection.php', scandir($fullpath.'/assets'))) {
		require_once($fullpath.'/assets/connection.php');
		// $conn = $db::getInstance();
		require_once($fullpath.'/model/model.php');
	}
	date_default_timezone_set('Africa/Johannesburg'); //set the default timezone to match your country
	/**
	* 
	*/

	class Recommendation extends DB_Adapter {
		private static $dbcon = null;
		
		public function __construct() {
			// global $conn;
			// self::$dbcon = $conn;
			self::$dbcon = self::getInstance();
		}

		public static function getAllAccounts() {
			if(class_exists('User')) {
				$userInstance = new User();
			}
			return $userInstance->readAllUsers();
		}

		public static function recommendFollowers() {
			$sess_uid = $_SESSION['auth']['uid']; //get recommendation for loggedIn user
			// $stmt = self::$dbcon->prepare("SELECT follower_id, followed_id, created FROM followers");
			$stmt = self::$dbcon->prepare("SELECT follower_id FROM followers WHERE followed_id = :ownerId");
			$stmt->bindValue(':ownerId', $sess_uid, PDO::PARAM_INT);
			$stmt->execute();
			if($stmt->rowCount() > 0) { //check if user has followed or has followers
				$rows = $stmt->fetchAll(PDO::FETCH_OBJ);
				foreach(self::getAllAccounts() as $account) { //retrieve all registered accounts
					foreach($rows as $row) { //retrieve all accounts who followed the current user
						/*if($account->id == $row->follower_id || $account->id == $sess_uid && $row->follower_id == $sess_uid) { //then compare all users with ID
							continue; //if user IDs match then skip them
						} else {
							$recommendedUsers[] = $account->id; //else ones which are not skipped assign them to an array
							break;
						}*/
						if($account->id == $row->follower_id) {
							continue;
						} elseif($account->id == $sess_uid) {
							continue;
						} else {
							$recommendedUsers[] = $account->id;
						}
					}
				}
				return $recommendedUsers; //then return all id's in array
			} 
			else { 
				return false;
			}
		}
	}

	if(class_exists('Recommendation')) {
		$recommend = new Recommendation();
	}

	/*$response = $recommend->recommendFollowers();
	print_r($response);*/

?>