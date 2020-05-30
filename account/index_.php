<?php
	
	$fullpath = $_SERVER['DOCUMENT_ROOT'].'/photobook';
	require_once($fullpath.'/model.php');

	if(!isset($_SESSION['auth']['uid'])) {
		$user->page_protected(); //call to check if user loggedin
		exit();
	}
	
	$logged = (isset($_SESSION['auth']['token'])) ? true : false; //if user not loggedin set to TRUE...else...FALSE
	$row = $user->readProfileData($_SESSION['auth']['uid']); //read user profile details
	$media = $user->getAllMedia($_SESSION['auth']['uid']); //all posted media
	$owners = $user->readAll();
	if(isset($_REQUEST['profile'])) {
		//display name here
	}
	include_once('auth_forms.php');
?>
<!DOCTYPE html>
<html>
<head>
	<title>Photobook :: Profile</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="../styles/styles.css">
	<link rel="stylesheet" type="text/css" href="../../boostrap3/font-awesome/css/font-awesome.min.css">
	<script type="text/javascript" src="../../boostrap3/jquery/jquery-1.12.4.min.js"></script>
	<script type="text/javascript" src="../js/photobook.js"></script>
</head>
<body>
	<div class="container-menu">
		<nav class="navbar">
			<ul>
				<li><a href="../"><img src="../images/logo_2.png" class="logo"/></a></li>
				<li><input type="search" class="form-input search" name="search" placeholder="Search..."></li>
				<?php if($logged) { ?>
					<li><a href="javascript:void(0)" id="settings" class="nav-link">Settings</a></li>
					<li><a href="../signout/true" id="logout" class="nav-link" onclick="return logout();">Logout</a></li>
				<?php } else { ?>
					<li><a href="javascript:void(0)" id="show-login" class="nav-link">Login</a></li>
					<li><a href="javascript:void(0)" id="show-register" class="nav-link">Register</a></li>
				<?php } ?>
			</ul>
		</nav>
	</div>

	<div class="container-item">

		<img src="../images/<?php echo $row->profile_url; ?>" class="profile-image">
		<div class="username">
			<?php echo $row->fullname; ?>
			<?php if($logged) { ?>
				<button type="button" id="edit-profile" class="btn btn-small">EDIT PROFILE</button>
			<?php } ?>
		</div>
			<div class="description"><?php echo $row->username; ?> some small description here about the user.</div>
			<span class="user-stats">
				<li>66 posts</li>
				<li>214 followers</li>
				<li>110 following</li>
			</span>
		<div class="user-settings">
			<?php if(!empty($owners)) { //if current owner of profile the show settings option. Else follow/message
					foreach($owners as $owner) :
						if($row->id === $owner->id) {
			?>
			<!-- <button type="button" id="Settings" class="btn">Settings</button> -->
			<button type="button" id="upload" class="btn">Upload</button>
			<?php
						}
					endforeach;
				 } 
				  else {
				?>
			<button type="button" id="follow" class="btn">Follow</button>
			<button type="button" id="message" class="btn">Message</button>
			<?php } ?>
		</div>
	</div>

	<div class="profile-contents">
		<div class="owner-posts">
			<li class="post-resource"><img src="../images/breik1.jpg"></li>
			<li class="post-resource"><img src="../images/breik2.jpg"></li>
			<li class="post-resource"><img src="../images/breik3.jpg"></li>
			<li class="post-resource"><img src="../images/breik4.jpg"></li>
			<li class="post-resource"><img src="../images/breik5.jpg"></li>
			<li class="post-resource"><img src="../images/girlcover.jpg"></li>
		</div>
	</div>
</body>
</html>