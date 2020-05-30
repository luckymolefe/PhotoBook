<?php
	//Messaging and Notification Service Model
	$fullpath = $_SERVER['DOCUMENT_ROOT'].'/photobook/model/';
	require_once($fullpath.'comments_model.php');
	/**
	* 
	*/
	class Messaging extends Comments {
		private static $dbcon = null;
		public static $recipient;
		public static $sender;
		public static $message;
		public static $status;
		
		public function __construct() {
			parent::__construct();
			// global $conn;
			// self::$dbcon = $conn;
			self::$dbcon = self::getInstance();
		}

		public static function sendMessage($receiver, $sender, $message) {
			self::$recipient = (int)$receiver;
			self::$sender = (int)$sender;
			self::$message = nl2br(trim($message));
			return self::createNew();
		}

		private static function createNew() {
			$stmt = self::$dbcon->prepare("INSERT INTO messages (receipient_id, sender_id, message) VALUES (?, ?, ?)");
			$stmt->bindValue(1, self::$recipient, PDO::PARAM_INT);
			$stmt->bindValue(2, self::$sender, PDO::PARAM_INT);
			$stmt->bindValue(3, self::$message, PDO::PARAM_STR);
			if($stmt->execute()) {
				return true;
			} else {
				return false;
			}
		}

		public static function getMessages($uid) {
			self::$recipient = (int)$uid;
			self::$status = 0;
			$stmt = self::$dbcon->prepare("SELECT * FROM messages WHERE receipient_id = :uid AND status = :statusCode");
			$stmt->bindValue(':uid', self::$recipient, PDO::PARAM_INT);
			$stmt->bindValue(':statusCode', self::$status, PDO::PARAM_INT);
			$stmt->execute();
			if($stmt->rowCount() > 0) {
				return $stmt->fetch(PDO::FETCH_OBJ);
			} else {
				return false;
			}
		}

		public static function updateMessage($uid, $sent_date) {
			self::$recipient = (int)$uid;
			self::$data = $sent_date;
			self::$status = 1;
			$stmt = self::$dbcon->prepare("UPDATE messages SET status = ? WHERE receipient_id = ? AND created = ?");
			$stmt->bindValue(1, self::$status, PDO::PARAM_INT);
			$stmt->bindValue(2, self::$recipient, PDO::PARAM_INT);
			$stmt->bindValue(3, self::$data, PDO::PARAM_STR);
			if($stmt->execute()) {
				return true;
			} else {
				return false;
			}
		}

	}
	if(class_exists('Messaging')) {
		$service = new Messaging();
	}
?>