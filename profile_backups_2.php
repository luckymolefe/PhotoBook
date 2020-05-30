<?php

	if(file_exists('controller.php') && is_file('controller.php')) {
		require_once('controller.php');
		require_once('post_controller.php');
	}
	//explode('/', $_SERVER['REQUEST_URI']);
	$_SERVER['REQUEST_URI'] = isset($_SERVER['REQUEST_URI']) ?  str_ireplace('/photobook/', '', $_SERVER['REQUEST_URI']) : null; //string uri request
	if($user->readOwner($_SERVER['REQUEST_URI'])==false) { //if user profile is not available then, redirect back to home
		header("Location: home");
		exit();
	} else {
		$row = $user->readOwner($_SERVER['REQUEST_URI']); //else... show user profile details
	}

	if(!isset($_SESSION['auth']['token'])) { //only if user session not active then,
		$url_direct = ($user->keepLoggedIn()) ? 'javascript:login()' : 'javascript:void(0)'; //then redirect to stored session login
	}

	$logged = (isset($_SESSION['auth']['token'])) ? true : false; //if user not loggedin set to TRUE...else...FALSE
	$uid = (isset($_SESSION['auth']['uid'])) ? (int)$_SESSION['auth']['uid'] : (int)$row['id'];
	$isOwner = $user->isOwner($uid);
	$owners = $user->readAllUsers();
	$mediaposts = $post->getAllMedia($row['id']); //all posted media

	$stats = $post->postsStatistics($row['id']);
	if(isset($_SESSION['auth']['token'])) {
		$isFollowed = $post->getFollowers($_SESSION['auth']['uid'], $row['id']);
	}
	#include_once('account/auth_forms.php');
?>
<!DOCTYPE html>
<html>
<head>
	<title>Photobook :: Welcome <?php echo $_SERVER['REQUEST_URI']; ?></title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="styles/styles.css">
	<link rel="stylesheet" type="text/css" href="../boostrap3/font-awesome/css/font-awesome.min.css">
	<script type="text/javascript" src="../boostrap3/jquery/jquery-1.12.4.min.js"></script>
	<script type="text/javascript" src="js/photobook.js"></script>
	<link rel="icon" type="image/png" href="images/logos/logo_1_icon.png"/>
	<link rel="shortcut icon" type="image/x-icon" href="images/logos/logo_1_icon.png"/>
	
</head>
<body onload="checkActiveMessage('<?php echo $_SESSION['auth']['uid']; ?>'); loadRecommend('<?php echo $_SERVER['REQUEST_URI']; ?>');"> <!--  -->
	<div class="recommendations"></div>
	<?php include_once('account/auth_forms.php'); ?>
	<div class="layer"></div>
	<div class="container-menu">
		<nav class="navbar">
			<ul>
				<li><a href="home"><img src="images/logos/logo_2.png" class="logo"/></a></li>
				<li><input type="search" class="form-input search" name="search" placeholder="Search..."><i class="fa fa-arrow-right btn-search" title="submit query" onclick="doSearch()"></i></li>
				<?php if($logged) { ?>
					<?php if($isOwner->id == $row['id']) { ?>
						<li><a href="javascript:openSettings()" id="settings" class="nav-link">Settings</a></li>
					<?php } else { ?>
						<li><a href="<?php echo $isOwner->username; ?>" id="profile" class="nav-link">View Profile</a></li>
					<?php } ?>
					<li><a href="signout/true" id="logout" class="nav-link" onclick="return logout();">Logout</a></li>
					<li><i class="profile-settings"></i></li>
				<?php } else { ?>
					<li><a href="<?php echo $url_direct; ?>" class="show-login nav-link">Login</a></li>
					<li><a href="javascript:void(0)" class="show-register nav-link">Register</a></li>
				<?php } ?>
			</ul>
			<div class="fa fa-bars menu-bar" title="Toggle Menu"></div>
		</nav>
	</div><!-- hidden top menu -->
	<div class="hidden-menu"> 
		<?php if($logged) { ?>
			<li><i class="fa fa-cog"></i> <a href="javascript:openSettings()" id="settings">Settings</a></li>
			<li><i class="fa fa-user"></i> <a href="javascript:editPopup()">Edit</a></li>
			<li><i class="fa fa-power-off"></i> <a href="signout/true" id="logout" onclick="return logout();">Logout</a></li>
		<?php } else { ?>
			<li><i class="fa fa-sign-in"></i> <a href="<?php echo $url_direct; ?>" class="show-login">Login</a></li>
			<li><i class="fa fa-user-plus"></i> <a href="javascript:void(0)" class="show-register">Register</a></li>
		<?php } ?>
	</div>
	<div class="notifyPop"><i class="fa fa-close message-close"></i></div>
	
	<div class="container-profile">
		<div class="top-color">&nbsp;</div>
		<div class="upload-thumbnail" onclick="show_upload('thumbnail');"><i class="fa fa-camera"></i></div>
		<img src="images/profile_thumbnails/<?php echo $row['profile_url']; ?>" class="side-profile-image">
		<div class="username">
			<span title="<?php echo $row['fullname']; ?>"><?php (strlen($row['fullname']) >= 15) ? print substr($row['fullname'], 0, 12).'...' : print $row['fullname']; ?></span>
		</div>
		<div class="description">
			<div>
				<?php $descr = (strlen($row['description']) > 0) ? "<strong>@".$row['username']."</strong><br/>".$row['description'] : '<br/>&nbsp;'; ?>
				<?php (strlen($descr) >= 25) ? print substr($descr, 0, 55).'...' : print $descr;?>
			</div>
		</div>
		<div class="user-stats">
			<li>Posts<br/><div class="stats-counter"><?php echo $stats['total_posts']; ?></div></li>
			<li>Following<br/><div class="stats-counter"><?php echo $stats['total_followed']; ?></div></li>
			<li>Followers<br/><div class="stats-counter"><?php echo $stats['total_followers']; ?></div></li>
		</div>
	</div>

	<div class="container-item">
		<div>
			<form action="post_controller.php" method="post" name="multiple_upload_form" id="multiple_upload_form" enctype="multipart/form-data">
				<img src="images/profile_thumbnails/<?php echo $row['profile_url']; ?>" class="post-image">
				<textarea class="post-input" id="contentTitle" rows="5" placeholder="Add your image caption..." autofocus></textarea>
				<input type="file" class="file-control" name="images" id="fileUpload" />
				<input type="hidden" name="upload_uri_obj" id="upload_uri" value="wallpost">
				<div><button type="button" class="btn btn-post" onclick="uploadImage()">Post</button></div>
			</form>
			<div class="resposeMsg"></div>
		</div>
		<!-- <img src="images/profile_thumbnails/<?php echo $row['profile_url']; ?>" class="profile-image">
		<?php if($logged && $isOwner->id == $row['id']) { ?><span class="upload-thumbnail" onclick="show_upload('thumbnail');"><i class="fa fa-camera"></i></span><?php } ?>
		<div class="username">
			<?php echo $row['fullname']; ?>
			<?php if($logged && $isOwner->id == $row['id']) { ?>
				<button type="button" id="edit-profile" class="btn btn-small" onclick="editPopup()">EDIT PROFILE <i class="fa fa-pencil"></i></button>
				<i class="profile-edit"></i>
			<?php } ?>
		</div>
		<div class="description">
			<?php if(strlen($row['description']) > 0) { echo "<strong>".$row['username']."</strong>&nbsp;".$row['description']; } ?>
		</div>
		<span class="user-stats">
			<li><?php echo $stats['total_posts']; ?> posts</li>
			<li><?php echo $stats['total_followers']; ?> followers</li>
			<li><?php echo $stats['total_followed']; ?> following</li>
		</span>
		<div class="user-settings">
			<?php if($logged && $isOwner->id == $row['id']) { ?>
					<button type="button" id="upload" class="btn" onclick="show_upload('wallpost')">Upload <i class="fa fa-camera"></i></button>
			<?php } elseif($logged && $isOwner->id != $row['id']) {
					if($isFollowed == true) { ?>
						<button type="button" class="btn btn-followed">Followed</button>
			  <?php } else { ?>
						<button type="button" id="follow" class="btn" data-follow="<?php echo $row['id']; ?>" onclick="followUser(this)">Follow</button>
			  <?php } ?>
					<button type="button" id="message" class="btn" onclick="openMessaging('<?php echo $row['id']; ?>');">Message</button>
			  <?php } else { ?>
					<button type="button" class="btn" onclick="alert('Please login or create account!')">Follow</button>
					<button type="button" class="btn sendMessage" onclick="alert('Please login or create account!');">Message</button>
			  <?php } ?>
		</div> -->
	</div>

	<div class="profile-contents">
		<div class="owner-posts">
			<?php if($stats['total_posts'] > 0) { 
					foreach($mediaposts as $posts): ?>
						<li class="post-resource" onclick="zoomInImage('<?php echo $posts->urlpath; ?>')"><img src="images/wallposts/<?php echo $posts->urlpath; ?>"></li>
			<?php 	endforeach; 
				  } else { ?>
				<div class="message-item"><i class="fa fa-info-circle"></i> <?php echo $row['username']; ?> haven't posted anything yet...</div>
			<?php } ?>
		</div>
	</div>

</body>
</html>