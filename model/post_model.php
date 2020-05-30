<?php
	//Media post model
	$fullpath = $_SERVER['DOCUMENT_ROOT'].'/photobook/model/';
	require_once($fullpath.'model.php');
	/**
	* Interface adapter to define required classes
	*/
	interface media_Adapter {
		public static function postMedia($uid, $mediaUrl, $mediaTitle);
		// protected static function createPost();
		public static function readOnePost($media_id);
		public static function readWallPosts();
		public static function getAllMedia($param);
	}

	class Posts extends User implements media_Adapter { #
		private static $dbcon = null;
		public static $urlpath;
		public static $title;
		public static $uid;
		public static $mid;
		
		public function __construct() {
			parent::__construct();
			// global $conn;
			// self::$dbcon = $conn;
			self::$dbcon = self::getInstance();
		}

		public static function postMedia($uid, $mediaUrl, $mediaTitle) {
			self::$uid = (int)$uid;
			self::$urlpath = $mediaUrl;
			self::$title = $mediaTitle;
			return self::createPost();
		}

		protected static function createPost() {
			$stmt = self::$dbcon->prepare("INSERT INTO wallposts (user_id, urlpath, media_title) VALUES (?, ?, ?)");
			$stmt->bindValue(1, self::$uid, PDO::PARAM_INT);
			$stmt->bindValue(2, self::$urlpath, PDO::PARAM_STR);
			$stmt->bindValue(3, self::$title, PDO::PARAM_STR);
			try{
				self::$dbcon->beginTransaction();
				$stmt->execute();
				$lastId = self::$dbcon->lastInsertId(); 
				self::$dbcon->commit();
				if($lastId > 0) {
					return true;
				} else {
					return false;
				}
			}
			catch(PDOException $e) {
				self::$dbcon->rollBack();
				echo "Error: ".$e->getMessage();
			}
		}

		public static function readOnePost($media_id) {
			self::$mid = (int)$media_id;
			$stmt = self::$dbcon->prepare("SELECT * FROM wallposts WHERE media_id = ?");
			$stmt->bindValue(1, self::$mid, PDO::PARAM_INT);
			$stmt->execute();
			if($stmt->rowCount() > 0) {
				return $stmt->fetch(PDO::FETCH_ASSOC);
			} else {
				return false;
			}
		}

		public static function readWallPosts() {
			$stmt = self::$dbcon->prepare("SELECT * FROM wallposts ORDER BY created DESC");
			$stmt->execute();
			if($stmt->rowCount() > 0) {
				return $stmt->fetchAll(PDO::FETCH_OBJ);
			} else {
				return false;
			}
		}

		public static function getAllMedia($param) {
			self::$uid = (int)$param;
			$stmt = self::$dbcon->prepare("SELECT * FROM wallposts WHERE user_id = ?");
			$stmt->bindValue(1, self::$uid, PDO::PARAM_INT);
			$stmt->execute();
			if($stmt->rowCount() > 0) {
				return $stmt->fetchAll(PDO::FETCH_OBJ);
			} else {
				return false;
			}
		}

		public static function searchPosts($query) {
			self::$data = "%".$query."%";
			$stmt = self::$dbcon->prepare("SELECT a.media_id, a.user_id, a.media_title, a.urlpath, b.media_id, b.comment
										   FROM wallposts a LEFT JOIN comments b
										   ON a.media_id = b.media_id
										   WHERE a.media_title LIKE ? OR b.comment LIKE ?");
			$stmt->bindValue(1, self::$data, PDO::PARAM_STR);
			$stmt->bindValue(2, self::$data, PDO::PARAM_STR);
			$stmt->execute();
			if($stmt->rowCount() > 0) {
				return $stmt->fetchAll(PDO::FETCH_OBJ);
			} else {
				return false;
			}
		}

		public static function postsStatistics($uid) {
			self::$uid = (int)$uid; //(isset($_SESSION['auth']['uid'])) ? $_SESSION['auth']['uid'] : 
			$statistics = array(
				'total_posts' => self::countPosts(self::$uid),
				'total_followers' => self::countFollowers(self::$uid),
				'total_followed' => self::countFollowed(self::$uid)
			);
			return $statistics;
		}

		public static function followUser($sess_follower_id, $followedUser) { //adding/following user
			self::$uid = $sess_follower_id;
			self::$data = $followedUser;
			$stmt = self::$dbcon->prepare("INSERT INTO followers (follower_id, followed_id) VALUES (:follower, :followed)");
			$stmt->bindValue(':follower', self::$uid, PDO::PARAM_INT);
			$stmt->bindValue(':followed', self::$data, PDO::PARAM_STR);
			if($stmt->execute()) {
				return true;
			} else {
				return false;
			}
		}

		public static function getFollowers($sess_uid, $followed_id) { //retirieve user who followed
			self::$uid = (int)$sess_uid;
			self::$data = (int)$followed_id;
			$stmt = self::$dbcon->prepare("SELECT follower_id, followed_id, created FROM followers WHERE follower_id = ? AND followed_id = ?");
			$stmt->bindValue(1, self::$uid, PDO::PARAM_INT);
			$stmt->bindValue(2, self::$data, PDO::PARAM_INT);
			$stmt->execute();
			if($stmt->rowCount() > 0) {
				return true; //$stmt->fetchAll(PDO::FETCH_ASSOC);
			} else {
				return false;
			}
		}

		public static function countPosts($uid) {
			self::$uid = $uid;
			$stmt = self::$dbcon->prepare("SELECT media_id FROM wallposts WHERE user_id = ?");
			$stmt->bindValue(1, self::$uid, PDO::PARAM_INT);
			$stmt->execute();
			if($stmt->rowCount() > 0) {
				return $stmt->rowCount();
			} else {
				return 0;
			}
		}
		public static function countLikes($media_id) {
			self::$mid = (int)$media_id;
			$stmt = self::$dbcon->prepare("SELECT media_id FROM likes WHERE media_id = ?");
			$stmt->bindValue(1, self::$mid, PDO::PARAM_INT);
			$stmt->execute();
			if($stmt->rowCount() > 0) {
				return $stmt->rowCount();
			} else {
				return 0;
			}
		}
		public static function countFollowers($uid) {
			self::$uid = (int)$uid;
			$stmt = self::$dbcon->prepare("SELECT followed_id FROM followers WHERE followed_id = ?");
			$stmt->bindValue(1, self::$uid, PDO::PARAM_INT);
			$stmt->execute();
			if($stmt->rowCount() > 0) {
				return $stmt->rowCount();
			} else {
				return 0;
			}	
		}
		public static function countFollowed($uid) {
			self::$uid = (int)$uid;
			$stmt = self::$dbcon->prepare("SELECT follower_id FROM followers WHERE follower_id = ?");
			$stmt->bindValue(1, self::$uid, PDO::PARAM_INT);
			$stmt->execute();
			if($stmt->rowCount() > 0) {;
				return $stmt->rowCount();
			} else {
				return 0;
			}	
		}
		public static function countComments($media_id) {
			self::$mid = (int)$media_id;
			$stmt = self::$dbcon->prepare("SELECT comment FROM comments WHERE media_id = ?");
			$stmt->bindValue(1, self::$mid, PDO::PARAM_INT);
			$stmt->execute();
			if($stmt->rowCount() > 0) {
				return $stmt->rowCount();
			} else {
				return 0;
			}	
		}

		public static function timeDiff($old_time) { //formats time stamps on posted items
			$difference = strtotime(date('Y-m-d')) - strtotime($old_time);
			$minutes_past = floor($difference / (60)); //get number of minutes
			$hours_past = floor($difference / (60 * 60)); //get number of hours
			$days_past = floor($difference / (60 * 60 * 24)); //get number of days
			/*calculating days*/
			if($days_past <= 0) {
				$day = "Today";
			}
			else if( $days_past == 1) {
				$day = "Yesterday";
			}
			else if( $days_past > 1 && $days_past < 7) {
				$day = $days_past." Days ago";
			}
			else if( $days_past >= 7 && $days_past <= 13) {
				$day = "1W";
			}
			else if( $days_past == 14) {
				$day = "2W";
			}
			else if( $days_past > 14 && $days_past < 30) {
				$day = "Weeks ago";
			}
			else if( $days_past == 30 || $days_past == 31) {
				$day = "1M";
			}
			else if( $days_past > 31) {
				$day = "Months ago";
			}
			else if( $days_past > 365) {
				$day = "Years ago";
			}
			return $day;
		}
	}
	if(class_exists('Posts')) {
		$post = new Posts();
	}
?>