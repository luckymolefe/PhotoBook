<?php
	//
	$fullpath = $_SERVER['DOCUMENT_ROOT'].'/photobook/';
	require_once($fullpath.'post_controller.php');

	if(isset($_SESSION['auth']['token']) && !empty($_SESSION['auth']['token'])) {
		$uri = (isset($_REQUEST['uri_path'])) ? $_REQUEST['uri_path'] : null; //return profileUsername
		if(isset($_REQUEST['recommend']) && $_REQUEST['recommend'] == "true") {
			$uid = (int)$_REQUEST['uid'];
			$resrc = $user->readProfileData($uid); //read user profile details reference by ID
		}
		else {
			$user->page_protected();
			exit();
		}
		if( $uri === $resrc->username ) {
			$css_display = "none"; 
		} else {
			$css_display = "block";
		}
	}
	else {
		$css_display = "none"; //if no sessionSet hide suggestions
	}

?>
<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		.container-fluid {
			position: absolute;
			top: 100px;
			right: 10px;
			width: 250px;
			max-width: 100%;
			margin: 0 auto;
			padding: 10px;
			border-radius: 5px;
			background-color: rgba(255, 255, 255, 0.6);
			color: #777;
			box-shadow: 1px 1px 6px rgba(0, 0, 0, 0.4);
			text-align: center;
			font-family: helvetica, arial;
			display: <?php echo $css_display; ?>;
		}
		@media screen and (max-width: 1280px) {
			.container-fluid { 
				display: none;
			}
		}
		.image_profile > img {
			width: 130px;
			max-width: 100%;
			border-radius: 50%;
			margin-bottom: 10px;
		}
		.recommend-head {
			font-size: 1.4em;
			margin-bottom: 15px;
			color: #3F729B;
		}
		.recommend-names {
			font-size: 18px;
			color: #10527B;
			margin-bottom: 10px;
		}
		.recommend-description {
			margin-bottom: 15px;
		}
		.recommend-link {
			margin-bottom: 10px;
		}
		.recommend-link > a {
			color: #f90;
			text-decoration: none;
		}
		.recommend-link > a:hover {
			text-decoration: underline;
			color: #46b8da;
		}
		.popIn {
	    	animation: popIn .3s ease forwards;
		}
		@keyframes popIn {
		  	0%   { transform: scale(0); opacity: 0; }
		  	90%  { transform: scale(1.1);  opacity: 0.85; }
		  	100% { transform: scale(1); opacity: 1; }
		}
		.fade_out {
			animation: fade 1s forwards;
		}
		@keyframes fade {
			0%	  { opacity: 1; }
			50%	  { opacity: 0.50; }
			100%  { opacity: 0; }
		}

		.slideOut {
		    animation: slide 2s ease forwards;
		}
		@keyframes slide {
		  	0%  { transform: translateX(0px) }
		  	100%{ transform: translateX(280px); opacity:0; }
		}
	</style>
</head>
<body>
	<div class="container-fluid popIn">
		<div class="recommend-head">
			People you may know
		</div>
		<div class="image_profile">
			<img src="images/profile_thumbnails/<?php echo $resrc->profile_url; ?>" class=""/>
		</div>
		<div class="recommend-names">
			<?php echo $resrc->fullname; ?>
		</div>
		<div class="recommend-description">
			<?php echo $resrc->description; ?>
		</div>
		<div class="recommend-link">
			<a href="<?php echo $resrc->username; ?>">View profile</a>
		</div>
	</div>
</body>
</html>