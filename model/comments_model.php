<?php
	//Comment Post Model
	$fullpath = $_SERVER['DOCUMENT_ROOT'].'/photobook/model/';
	require_once($fullpath.'post_model.php');
	/**
	* 
	*/
	class Comments extends Posts {
		private static $dbcon = null;
		
		public function __construct() {
			parent::__construct();
			// global $conn;
			// self::$dbcon = $conn;
			self::$dbcon = self::getInstance();
		}

		public static function postComment($uid, $media_id, $comment) {
			self::$uid = (int)$uid;
			self::$mid = (int)$media_id;
			self::$data = $comment;
			return self::createComment();
		}

		protected static function createComment() {
			$stmt = self::$dbcon->prepare("INSERT INTO comments (user_id, media_id, comment) VALUES(?, ?, ?)");
			$stmt->bindValue(1, self::$uid, PDO::PARAM_INT);
			$stmt->bindValue(2, self::$mid, PDO::PARAM_INT);
			$stmt->bindValue(3, self::$data, PDO::PARAM_STR);
			if($stmt->execute()) {
				return true;
			} else {
				return false;
			}
		}

		public static function readOneComment($uid, $media_id) {
			self::$uid = (int)$uid;
			self::$mid = (int)$media_id;
			$stmt = self::$dbcon->prepare("SELECT * FROM comments WHERE media_id = ? AND user_id = ?");
			$stmt->bindValue(1, self::$mid, PDO::PARAM_INT);
			$stmt->bindValue(2, self::$uid, PDO::PARAM_INT);
			$stmt->execute();
			if($stmt->rowCount() > 0) {
				return $stmt->fetch(PDO::FETCH_OBJ);
			} else {
				return false;
			}
		}

		public static function readAllComments($media_id) {
			self::$mid = (int)$media_id;
			$stmt = self::$dbcon->prepare("SELECT * FROM comments WHERE media_id = ?");
			$stmt->bindValue(1, self::$mid, PDO::PARAM_INT);
			$stmt->execute();
			if($stmt->rowCount() > 0) {
				return $stmt->fetchAll(PDO::FETCH_OBJ);
			} else {
				return false;
			}
		}

		public static function addLike($uid, $media_id) {
			self::$uid = (int)$uid;
			self::$mid = (int)$media_id;
			$stmt = self::$dbcon->prepare("INSERT INTO likes (user_id, media_id) VALUES(?, ?)");
			$stmt->bindValue(1, self::$uid, PDO::PARAM_INT);
			$stmt->bindValue(2, self::$mid, PDO::PARAM_INT);
			if($stmt->execute()) {
				return true;
			} else {
				return false;
			}
		}

		public static function readLikes($uid, $media_id) {
			self::$uid = (int)$uid;
			self::$mid = (int)$media_id;
			$stmt = self::$dbcon->prepare("SELECT COUNT(*) AS totalLikes FROM likes WHERE media_id = ? AND user_id = ?");
			$stmt->bindValue(1, self::$mid, PDO::PARAM_INT);
			$stmt->bindValue(2, self::$uid, PDO::PARAM_INT);
			$stmt->execute();
			if($stmt->rowCount() > 0) {
				return $stmt->fetch(PDO::FETCH_OBJ);
			} else {
				return false;
			}
		}

		public static function getAllLikes($media_id) {
			self::$mid = (int)$media_id;
			$stmt = self::$dbcon->prepare("SELECT * FROM likes WHERE media_id = ?");
			$stmt->bindValue(1, self::$mid, PDO::PARAM_INT);
			$stmt->execute();
			if($stmt->rowCount() > 0) {
				return $stmt->fetchAll(PDO::FETCH_OBJ);
			} else {
				return false;
			}
		}

	}
	if(class_exists('Comments')) {
		$comment = new Comments();
	}
?>