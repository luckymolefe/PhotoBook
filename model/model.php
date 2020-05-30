<?php
//model
	$fullpath = $_SERVER['DOCUMENT_ROOT'].'/photobook';
	if(in_array('connection.php', scandir($fullpath.'/assets'))) {
		require_once($fullpath.'/assets/connection.php');
		// $conn = $db::getInstance();
	}
	date_default_timezone_set('Africa/Johannesburg'); //set the default timezone to match your country
	session_start();
	//Model interface
	interface person_Adapter {
		public static function signup($firstname, $lastname, $email, $password);
		public static function login($email, $password);
		public static function isActive($param);
		public static function isExists($param);
		public static function page_protected();
		public static function logout();
		public static function keepLoggedIn();
		public static function updateSettings($newpass, $uid);
		public static function updateProfile($firstname, $lastname, $username, $description, $uid);
		public static function readProfileData($param);
		public static function readOwner($param);
		public static function sanitize_data($params);
		public static function readAllUsers();
	}
	//Model abstract class
	/**
	* @author: Lucky Molefe
	* @description: 
	* @return defines methods to be accessed
	*/
	abstract class helper extends DB_Adapter {
		public static $data;
		abstract public static function login($email, $password);
		abstract public static function updateSettings($newpass, $uid);
		abstract public static function isActive($param);
		abstract public static function isExists($param);
		abstract public static function isOwner($param);
		abstract protected static function authenticateUser();
		abstract protected static function createAccount();
		
		// abstract public static function logout();
		// abstract protected static function tokenizer($data);

		public static function keepLoggedIn() { //check if user cookie data available
			if(!empty($_COOKIE['email']) && !empty($_COOKIE['password'])) {
				return true;
			}
			else {
				return false;
			}
		}

		public static function logout() {
			session_unset($_SESSION['auth']['token']);
			unset($_SESSION['auth']['token']);
			unset($_SESSION['auth']['id']);
			unset($_SESSION['auth']['loggedOn']);
			return true;
		}

		public static function page_protected() { //check if user session data available
			if(!isset($_SESSION['auth']['token'])) {
				header("Location: home");
			}
		}

		public static function sanitize_data($params) {
			$clean_data = array();
			foreach($params as $k => $v) {
				$clean_data[$k] = htmlentities(stripslashes(strip_tags(trim($v)))); 
			}
			return $clean_data;
		}

		public static function tokenizer($data) {
			self::$data = $data;
			return sha1(self::$data);
		}
	}
	/**
	* @author: Lucky Molefe
	* @return object User
	*/
	class User extends helper implements person_Adapter {
		private static $dbcon = null;
		public static $uid;
		public static $firstname;
		public static $lastname;
		public static $username;
		public static $email;
		public static $password;
		public static $description;
		public static $data = null;

		public function __construct() {
			// global $conn;
			// self::$dbcon = $conn;
			self::$dbcon = self::getInstance();
		}

		public static function login($email, $password) {
			self::$email = $email;
			self::$password = $password;
			if(self::isExists($email)) {
				if(self::isActive($email)) {
					return self::authenticateUser();
				} else {
					return "activate"; //account, pending activation
				}
			} else {
				return "invalid"; //invalid account, does not exists
			}
		}

		protected static function authenticateUser() {
			$stmt = self::$dbcon->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
			$stmt->bindValue(1, self::$email, PDO::PARAM_STR);
			$stmt->bindValue(2, self::tokenizer(self::$password), PDO::PARAM_STR);
			$stmt->execute();
			if($stmt->rowCount() > 0) {
				$row = $stmt->fetch(PDO::FETCH_OBJ);
				$_SESSION['auth']['uid'] = $row->id;
				$_SESSION['auth']['token'] = self::tokenizer($row->email);
				$_SESSION['auth']['loggedOn'] = true;
				return $row->username;
			} else {
				return false;
			}
		}

		public static function isActive($param) {
			self::$email = $param;
			self::$data = 1;
			$stmt = self::$dbcon->prepare("SELECT email, active FROM users WHERE email = :email AND active = :status");
			$stmt->bindValue(':email', self::$email, PDO::PARAM_STR);
			$stmt->bindValue(':status', self::$data, PDO::PARAM_INT);
			$stmt->execute();
			if($stmt->rowCount() > 0) {
				return true;
			} else {
				return false;
			}	
		}

		public static function isExists($email) {
			self::$email = $email;
			$stmt = self::$dbcon->prepare("SELECT email FROM users WHERE email = :email");
			$stmt->bindValue(':email', $email, PDO::PARAM_STR);
			$stmt->execute();
			if($stmt->rowCount() > 0) {
				return true;
			} else {
				return false;
			}		
		}

		public static function isOwner($uid) {
			self::$uid = (int)$uid;
			$stmt = self::$dbcon->prepare("SELECT id, username FROM users WHERE id  = :uid");
			$stmt->bindValue(':uid', self::$uid, PDO::PARAM_INT);
			$stmt->execute();
			if($stmt->rowCount() > 0) {
				return $stmt->fetch(PDO::FETCH_OBJ);
			} else  {
				return false;
			}
		}

		public static function signup($firstname, $lastname, $email, $password) {
			self::$firstname = $firstname;
			self::$lastname = $lastname;
			self::$username = strtolower($firstname.$lastname);
			self::$email = $email;
			self::$password = $password;
			self::$description = self::$username." Info about me here.";
			if(self::isExists(self::$email)) { //if returns TRUE means userAccount already exists, then do create new
				return false;
			} else {
				return self::createAccount(); //else create new if not exists
			}
		}

		protected static function createAccount() {
			$stmt = self::$dbcon->prepare("INSERT INTO users (firstname, lastname, username, email, password, description) VALUES (?, ?, ?, ?, ?, ?)");
			$stmt->bindValue(1, self::$firstname, PDO::PARAM_STR);
			$stmt->bindValue(2, self::$lastname, PDO::PARAM_STR);
			$stmt->bindValue(3, self::$username, PDO::PARAM_STR);
			$stmt->bindValue(4, self::$email, PDO::PARAM_STR);
			$stmt->bindValue(5, self::tokenizer(self::$password), PDO::PARAM_STR);
			$stmt->bindValue(6, self::$description, PDO::PARAM_STR);
			try {
				self::$dbcon->beginTransaction();
				$stmt->execute();
				$lastInsertId = self::$dbcon->lastInsertId();
				self::$dbcon->commit();
				if($lastInsertId > 0) {
					self::$uid = $lastInsertId;
					self::$data = "avatar.png"; //set default profile image
					copy("images/avatar.png", "images/profile_thumbnails/avatar.png"); //copy new file to directory
					$stmt = self::$dbcon->prepare("INSERT INTO thumbnails (profile_id, profile_url) VALUES (:uid, :urlpath)");
					$stmt->bindValue(':uid', self::$uid, PDO::PARAM_INT);
					$stmt->bindValue(':urlpath', self::$data, PDO::PARAM_STR);
					if($stmt->execute()) {
						return true;
					} else {
						return false;
					}
				} else {
					return false;
				}
			}
			catch(PDOException $e) {
				self::$dbcon->rollBack();
				echo "DB Error: ".$e->getMessage();
			}
		}

		public static function readProfileData($param) {
			self::$data = $param;
			$stmt = self::$dbcon->prepare("SELECT a.id, a.firstname, a.lastname, CONCAT(a.firstname,' ',a.lastname) as fullname, a.username, a.email, a.description, b.profile_id, b.profile_url
										   FROM users a LEFT JOIN thumbnails b
										   ON a.id = b.profile_id
										   WHERE a.id = ? OR a.email = ?");
			$stmt->bindValue(1, self::$data, PDO::PARAM_STR);
			$stmt->bindValue(2, self::$data, PDO::PARAM_STR);
			$stmt->execute();
			if($stmt->rowCount() > 0) {
				return $stmt->fetch(PDO::FETCH_OBJ);
			} else {
				return false;
			}
		}

		public static function updateProfile($firstname, $lastname, $username, $description, $uid) {
			self::$firstname = $firstname;
			self::$lastname = $lastname;
			self::$username = $username;
			self::$description = $description;
			self::$uid = (int)$uid;
			$stmt = self::$dbcon->prepare("UPDATE users SET firstname = ?, lastname = ?, username = ?, description = ? WHERE id = ?");
			$stmt->bindValue(1, self::$firstname, PDO::PARAM_STR);
			$stmt->bindValue(2, self::$lastname, PDO::PARAM_STR);
			$stmt->bindValue(3, self::$username, PDO::PARAM_STR);
			$stmt->bindValue(4, self::$description, PDO::PARAM_STR);
			$stmt->bindValue(5, self::$uid, PDO::PARAM_INT);
			if($stmt->execute()) {
				return true;
			} else {
				return false;
			}
		}

		public static function updateThumbnail($path_url, $uid) {
			self::$uid = (int)$uid;
			self::$data = $path_url;
			$stmt = self::$dbcon->prepare("UPDATE thumbnails SET profile_url = :path_url WHERE profile_id = :uid");
			$stmt->bindValue(':path_url', self::$data, PDO::PARAM_STR);
			$stmt->bindValue(':uid', self::$uid, PDO::PARAM_INT);
			if($stmt->execute()) {
				return true;
			} else {
				return false;
			}
		}

		public static function updateSettings($newpassword, $uid) {
			self::$password = $newpassword;
			self::$uid = (int)$uid;
			$stmt = self::$dbcon->prepare("UPDATE users SET password = :newpassword WHERE id = :uid");
			$stmt->bindValue(':newpassword', self::tokenizer(self::$password), PDO::PARAM_STR);
			$stmt->bindValue(':uid', self::$uid, PDO::PARAM_INT);
			if($stmt->execute()) {
				return true;
			} else {
				return false;
			}
		}

		public static function readOwner($username) {
			self::$username = $username;
			$stmt = self::$dbcon->prepare("SELECT a.id, a.firstname, a.lastname, CONCAT(a.firstname,' ',a.lastname) as fullname, a.username, a.email, a.description, b.profile_id, b.profile_url
										   FROM users a LEFT JOIN thumbnails b
										   ON a.id = b.profile_id
										   WHERE a.username = ?");
			$stmt->bindValue(1, self::$username, PDO::PARAM_STR);
			$stmt->execute();
			if($stmt->rowCount() > 0) {
				return $stmt->fetch(PDO::FETCH_ASSOC);
			} else {
				return false;
			}
		}

		public static function readAllUsers() {
			$stmt = self::$dbcon->prepare("SELECT a.id, a.firstname, a.lastname, CONCAT(a.firstname,' ',a.lastname) as fullname, a.username, a.email, b.profile_id, b.profile_url
										   FROM users a LEFT JOIN thumbnails b
										   ON a.id = b.profile_id");
			$stmt->execute();
			if($stmt->rowCount() > 0) {
				return $stmt->fetchAll(PDO::FETCH_OBJ);
			} else {
				return false;
			}
		}

	}
	if(class_exists('User')) {
		$user = new User();
	}
	/*$response = $user::signup($firsname="Lucky", $lastname="Molefe", $email="luckmolf@company.com", $password="Luckys");
	$response=($response==true) ? 'registered': 'failed';
	echo $response;*/

 ?>