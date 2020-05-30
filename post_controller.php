<?php
	//Post Controller
	$fullpath = $_SERVER['DOCUMENT_ROOT'].'/photobook';

	if(file_exists($fullpath.'/model/messaging_model.php') && is_file($fullpath.'/model/messaging_model.php')) {
		require_once($fullpath.'/model/messaging_model.php');
		// require_once($fullpath.'/model/recommend_model.php');
	} else {
		echo "Failed to include neccessary file!.";
	}


	//Post Media on your wall
	if(isset($_REQUEST['posttowall']) && $_REQUEST['posttowall'] == "true") {
		sleep(1);
		//if image file posted is empty show this message
		if(empty($_FILES['mediaUpload']['name'])) { 
			$response['message'] = "Please select file to upload!";
		}

		$_REQUEST['contentTitle'] = nl2br($_REQUEST['contentTitle']); //format text data from textarea

		$params = array(
			'uid' => $_SESSION['auth']['uid'],
			'postTitle' => $_REQUEST['contentTitle'],
			'filename' => $_FILES['mediaUpload']['name']
		);
		$sanitized = $user->sanitize_data($params); //clean data

		$file_info = explode('.', $sanitized['filename']);
		$file_ext = end($file_info);
		$file_ext = strtolower($file_ext);
		$sanitized['filename'] = "IMG_".date('Ymd')."_".rand().".".$file_ext;

		//validate file extension
		$allowed_file_ext = array('jpg', 'jpeg', 'png', 'gif');
		if(!in_array($file_ext, $allowed_file_ext)) {
			unlink($_FILES['mediaUpload']['tmp_name']);
			$response['message'] = "<i class='fa fa-warning'></i> Invalid file extension. <br>Allowed file types (JPG, JPEG, PNG, GIF)";
			echo json_encode($response);
			exit();
		}

		//route upload directory according, evoked event/
		if(!empty($_REQUEST['uploading_uri']) && $_REQUEST['uploading_uri'] == "wallpost") {
			$upfilepath = "images/wallposts/".$sanitized['filename'];
		}
		else {
			$upfilepath = "images/profile_thumbnails/".$sanitized['filename'];
		}
		//then upload the file
		if(is_uploaded_file($_FILES['mediaUpload']['tmp_name'])) {
			if($_REQUEST['uploading_uri'] == "wallpost") {
				$results = $post->postMedia($sanitized['uid'], $sanitized['filename'], $sanitized['postTitle']);
			} else {
				$results = $user->updateThumbnail($sanitized['filename'], $sanitized['uid']);
				//if update profile image get current image an remove old image
				$row = $user->readProfileData($_SESSION['auth']['uid']);
				(file_exists("images/profile_thumbnails/".$row->profile_url)) ? unlink('images/profile_thumbnails/'.$row->profile_url) : '';
			}
			//test response
			if($results) {
			    $response['message'] =  "OK";
		    } else {
		    	$response['message'] = "<i class='fa fa-warning'></i> Failed to upload media!.";
		    }
		}
		//if failed to upload show message
		if (!move_uploaded_file($_FILES['mediaUpload']['tmp_name'], $upfilepath)) {
			unlink($_FILES['mediaUpload']['tmp_name']);
			$response['message'] = "<i class='fa fa-warning'></i> Sorry could not upload file to destination directory.";
		}
		echo json_encode($response);
		exit();
	} /*END of upload file*/

	//Posting comments for specific media
	if(isset($_REQUEST['postcomment']) && $_REQUEST['postcomment'] == "true") {
		sleep(2);
		if(!isset($_SESSION['auth']['token'])) {
			$response['message'] = "Please login or register first!";
		} else {
			$_REQUEST['comment'] = nl2br($_REQUEST['comment']);
			$params = array(
				'uid' => $_SESSION['auth']['uid'],
				'media_id' => $_REQUEST['media_id'],
				'comment' => $_REQUEST['comment']
			);
			$sanitized = $user->sanitize_data($params); //cleaned data
			if($comment->postComment($sanitized['uid'], $sanitized['media_id'], $sanitized['comment'])) {
				$response['message'] = "Comment submitted!";
			} else {
				$response['message'] = "Sorry failed to post your Comment!";
			}
		}
		// echo $_REQUEST['comment']." Data saved!";
		echo json_encode($response);
		exit();
	}
	//Add like reaction for specified media posted
	if(isset($_REQUEST['addlike']) && $_REQUEST['addlike'] == "true") {
		if(!isset($_SESSION['auth']['token'])) {
			$response['message'] = "Please login or register first!";
		} else {
			$uid = (int)$_SESSION['auth']['uid'];
			$media_id = (int)$_REQUEST['media_id'];
			if($comment->readLikes($uid, $media_id) != false) { //check if user already liked the post, exit to prevent dublicates
				$response['message'] = 'Already liked this';
			} else {
				if($comment->addLike($uid, $media_id)) { //..else if not liked yet, then new add like to post
					$response['message'] = "Liked!";
				} else {
					$response['message'] = "Sorry failed to like this!";
				}
			}
		}
		echo json_encode($response);
		exit();
	}
	//process requests for following user
	if(isset($_REQUEST['following']) && $_REQUEST['following'] == "true") {
		sleep(1);
		if(!isset($_SESSION['auth']['token'])) {
			$response['message'] = "Please login or register first!";
		} else {
			$sess_follower_id = (int)$_SESSION['auth']['uid'];
			$follow_user_id = (int)$_REQUEST['follow_id'];
			if($post->followUser($sess_follower_id, $follow_user_id)) {
				$response['response'] = "OK";
				$response['message'] = "Followed!";

				$sender = $sess_follower_id;
				$receiver = $follow_user_id;
				$message = "has followed you!"; //prepare message to send
				$service->sendMessage($receiver, $sender, $message); //call message service to send notification
			} else {
				$response['message'] = "Sorry failed to follow this user!";
			}
		}
		echo json_encode($response);
		exit();
	}
	//process requests for searching
	if(isset($_REQUEST['search']) && $_REQUEST['search'] == "true") {
		sleep(1);
		$params = array('query' => $_REQUEST['query']);
		$sanitized = $user->sanitize_data($params);
		$rows = $post->searchPosts($sanitized['query']);
		if($rows != false) {
			echo "<div class='search-heading'>Searched for: <em>".$sanitized['query']."</em></div>";
			foreach($rows as $datasearch) :
				$sourceOwner = $user->readProfileData($datasearch->user_id);
		?>
			<li class="search-result">
				<div class="search-item">
					<a href="<?php echo $sourceOwner->username; ?>"><img src="images/wallposts/<?php echo $datasearch->urlpath; ?>"/>
					<?php echo $datasearch->media_title; ?></a>
				</div>
				<div class="search-item-comments">
					<?php
						$datasearch->comment = preg_replace('/(\[)/', '<img src="images/emoji/', $datasearch->comment);
						$datasearch->comment = preg_replace('/(\])/', '.png" class="emoji_icon" />', $datasearch->comment);
						echo $datasearch->comment; 
					?>
				</div>
			</li>
		<?php
			endforeach;
		} else {
		?>
			<div class="message-item"><i class="fa fa-info-circle"></i> No match found!</div>
		<?php
		}
		// echo json_encode();
		exit();
	}

	//user sending message
	if(isset($_REQUEST['sending']) && $_REQUEST['sending'] == "true") {
		sleep(1);
		if(!isset($_SESSION['auth']['token'])) {
			$response['message'] = "Please login or register first!";
		} else {
			$params = array(
				'receipient' => $_REQUEST['receiverId'],
				'senderId' => $_SESSION['auth']['uid'],
				'messageData' => $_REQUEST['messageText']
			);
			$sanitized = $post->sanitize_data($params);
			if($service->sendMessage($sanitized['receipient'], $sanitized['senderId'], $sanitized['messageData'])) {
				$response['message'] = "<i class='fa fa-info-circle'></i>  Message sent!";
			} else {
				$response['message'] = "<i class='fa fa-warning'></i> Sorry failed to send message!";
			}
		}
		echo json_encode($response);
		exit();
	}
	//automation for check user messages
	if(isset($_REQUEST['checkMessages']) && $_REQUEST['checkMessages']=="true") {
		$params = array('uid'=>$_REQUEST['requesterId']);
		$sanitized = $post->sanitize_data($params);
		$msg = $service->getMessages($sanitized['uid']);
		if($msg!=false && $msg != null) {
			$uame=$user->readProfileData($msg->sender_id);
			$response['response'] = "OK";
			$response['message'] = '<i class="fa fa-close message-close" onclick="closeMessagePop(this,\''.$msg->created.'\')"></i> <strong><a href='.$uame->username.' title="View this profile">@'.$uame->username.'</a>:</strong> '.$msg->message;
		} else {
			$response['message'] = "Failed to retrieve message!";
		}
		echo json_encode($response);
		exit();
	}

	if (isset($_REQUEST['unsetMessage']) && $_REQUEST['unsetMessage'] == "true") {
		$params = array(
				'uid'=> $_SESSION['auth']['uid'],
				'dated'=> $_REQUEST['dated']
					);
		$sanitized = $post->sanitize_data($params);
		if($service->updateMessage($sanitized['uid'], $sanitized['dated'])) {
			$response['response'] = "OK";
		} else {
			$response['response'] = "failed";
		}
		echo json_encode($response);
		exit();
	}
	// $_REQUEST['getrecommends'] = true;
	if(isset($_REQUEST['getrecommends']) && $_REQUEST['getrecommends'] =="true") {
		require_once($fullpath.'/model/recommend_model.php');
		$usersRec = $recommend->recommendFollowers();
		// echo random_int(1, count($usersRec));
		if($usersRec != false && count($usersRec) > 0) {
			$index = rand(0, count($usersRec)-1);
			$response['message'] = "OK";
			$response['randomUser'] = $usersRec[$index];
			/*foreach($usersRec as $record) :
				$response['randomUser'] = $record; //rand(1, $len);
			endforeach;*/
		}
		else {
			$response['message'] = "No recommendations for you.";
		}
		echo json_encode($response);
		exit();
	}

?>