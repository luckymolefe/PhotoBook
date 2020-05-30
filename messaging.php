<?php
//Messaging service
	if(file_exists('post_controller.php') && is_file('post_controller.php')) {
		require_once('post_controller.php');
	}
	if(!isset($_POST['sendingOptions'])) {
		return false;
		exit();
	}
	if(!empty($_POST['id'])) {
		$id = (int)$_POST['id'];
		$row = $user->readProfileData($id); //else... show user profile details
	}

?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<style type="text/css">
		.message-container {
			/*position: absolute;*/
			/*top: 50px;*/
			width: 500px;
			max-width: 100%;
			margin: 0 auto;
			background-color: #f5f5f5;
			padding: 10px;
			margin-top: 70px;
			border-radius: 5px;
			text-align: center;
			font-family: arial;
			color: #777;
		}
		.close-item {
			color: #e42;
			border-radius: 2px;
			padding: 2px 3px 2px 3px;
			font-size: 15px;
			cursor: pointer;
			float: right;
			margin-top: -5px;
			margin-right: -5px;
		}
		.close-item:hover {
			background-color: #e42;
			color: #fff;
		}
		.message-control {
			width: 450px;
			max-width: 100%;
			border: thin solid #bbb;
			border-radius: 5px;
			padding: 5px 5px;
			font-family: arial;
			color: #777;
			resize: none;
		}
		.message-control:focus {
			border-color: #66afe9;
			outline: 0;
			-webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(63, 114, 115, 0.6);
		}
		.message-head {
			color: #3F729B;
			font-size: 1.5em;
		}
		.btn.btn-send:disabled {
			background-color: #e8e8e8;
			color: #999;
			border-color: #bbb;
		}
		.popIn {
    	animation: popIn .3s ease forwards;
		}
		@keyframes popIn {
		  	0%  { transform: scale(0); opacity: 0; }
		  	90%  { transform: scale(1.1);  opacity: 0.85; }
		  	100%{ transform: scale(1); opacity: 1; }
		}
	</style>
</head>
<body>
	<div class="message-container popIn">
		<i class="fa fa-close close-item" title="Close" onclick="closeUploadItem();"></i>
		<div class="message-head">Write Message <i class="fa fa-edit"></i></div>
		<div><input type="text" class="message-control" name="to_reciver" disabled value="To: <?php echo $row->username; ?>"></div>
		<p>
			<textarea class="message-control" name="message" id="message" rows="8" placeholder="Type your message here..."></textarea>
		</p>
		<p>
			<button type="button" class="btn btn-send" id="send" data-receiver="<?php echo $id; ?>" onclick="sendMessage(this)">Send Message</button>
		</p>
		<div class="msg_response"></div>
	</div>
</body>
</html>