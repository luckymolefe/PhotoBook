<?php
	if(file_exists('controller.php') && is_file('controller.php')) {
		require_once('controller.php');
	}
	
	if($user->keepLoggedIn()) { //if cookie data available, get user profile details
		$email = (!empty($_COOKIE['email'])) ? $_COOKIE['email'] : '';
		$row = $user->readProfileData($email);
	} else {
		$user->page_protected(); //check if user is Authenticated
	}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Photobook :: Login</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="../boostrap3/font-awesome/css/font-awesome.min.css">
	<style type="text/css">
		body {
			background-color: #e6e6e6;
		}
		.container {
			width: 350px;
			max-width: 100%;
			margin: 0 auto;
			background-color: #f5f5f5;
			padding: 20px;
			border: thin solid #ccc;
			border-radius: 5px;
			margin-top: 100px;
			text-align: center;
			font-family: helvetica, arial, verdana;
			box-shadow: 1px 1px 6px rgba(0, 0, 0, 0.1);
		}
		.logo {
			width: 150px;
			max-width: 100%;
			margin-bottom: 15px;
		}
		.user-profile {
			width: 120px;
			max-width: 100%;
			border-radius: 50%;
			margin-bottom: 15px;
		}
		.btn-login {
			width: 180px;
			max-width: 100%;
			background-color: #82b74b;
			color: #fff;
			padding: 8px 2px;
			border-radius: 5px;
			border: thin solid #82b74b;
			font-weight: bold;
			margin-bottom: 15px;
		}
		.btn-login:hover {
			background-color: #A4C639;
			border: thin solid #A4C639;
			cursor: pointer;
		}
		.btn-login:active {
			background-color: #eee;
			color: #777;
			border: thin solid #777;
		}
		.signup {
			position: absolute;
			margin-left: 100px;
			bottom: 35px;
		}
		.account {
			color: #555;
		}
		.account > a {
			color: #0F7DC2;
			font-weight: bold;
		}
		.signup > a {
			color: #444;
		}
		a {
			text-decoration: none;
			color: blue;
		}
		.remember {
			display: none;
		}
		/*login form css*/
		#login-form {
			position: absolute;
			top: 100px;
			width: 350px;
			height: 280px;
			max-width: 100%;
			margin: 0 auto;
			margin-left: 380px;
			padding: 15px 20px;
			text-align: center;
			background-color: #f5f5f5;
			color: #777;
			border-radius: 5px;
			box-shadow: 1px 1px 5px rgba(0, 0, 0, 0.5);
			display: none;
			z-index: 1;
		}
		.form-group {
			margin-top: 15px;
		}
		.form-input {
			width: 250px;
			max-width: 100%;
			margin: 0 auto;
			border-radius: 2px;
			padding: 10px;
			border: thin solid #ccc; /*#3F729B;*/
			color: #777;
			outline: 0 none;
		}
		.form-header {
			width: 390px;
			font-family: helvetica, arial;
			font-size: 20px;
			background-color: #3F729B;
			border-radius: 5px 5px 0 0;
			color: #fff;
			margin-top: -15px;
			margin-left: -20px;
			padding: 8px 0px;
		}
		.btn {
			width: 120px;
			background-color: #3F729B;
			color: #fff;
			padding: 10px 2px;
			border-radius: 2px;
			border: thin solid #ccc;
			font-weight: bold;
		}

		.btn:hover {
			background-color: #0F7DC2;
			color: #fff;
			cursor: pointer;
		}
		.btn:focus {
			outline: 0;
		}
		.btn:active {
			background-color: #7B0099;
			color: #fff;
			cursor: pointer;
			outline: 0;
		}
		.responseMsg {
			/*position: absolute;*/
			font-family: helvetica, arial;
			display: none;
			color: #3F729B;
			margin-top: 10px;
			/*text-align: center;*/
			/*bottom: 275px;*/
			/*margin-left: 130px;*/
		}
		.toAccount {
			position: absolute;
			top: 5px;
			left: 10px;
			color: #fff;
			padding: 5px 10px;
			border-radius: 50%;
			cursor: pointer;
		}
		.toAccount:hover {
			background-color: #fff;
			color: #3F729B;
		}
		.reset-link {
			color: #0F7DC2;
		}
		.reset-link:hover {
			color: #3F729B;
		}
		.reset-link:active {
			color: #7B0099;
		}
		.flash {
			animation: flash .55s infinite forwards;
		}
		@-webkit-keyframes flash {
			0% {
				background-color: #3F729B;
				text-shadow: none;
				border-color: #3F729B;
			}
			90% {
				background-color: #82b74b;
				border-color: #82b74b;
				text-shadow: none;
			}
			100% {
				background-color: #3F729B;
				text-shadow: none;
				border-color: #3F729B;
			}
		}
	</style>
	<script type="text/javascript" src="../boostrap3/jquery/jquery-1.12.4.min.js"></script>
	<script type="text/javascript" src="js/photobook.js"></script>
</head>
<body>
	<div class="container">
		<!-- session cookie state form to prelogin to account -->
		<div class="prelogin">
			<p><img src="images/logos/logo_2.png" class="logo"/></p>
			<p><img src="images/profile_thumbnails/<?php echo $row->profile_url; ?>" class="user-profile"></p>
			<p>
				<input type="hidden" name="email" id="email1" value="<?php (!empty($_COOKIE['email'])) ? print $_COOKIE['email'] : print''; ?>">
				<input type="hidden" name="password" id="password1" value="<?php (!empty($_COOKIE['password'])) ? print $_COOKIE['password'] : print''; ?>">
				<div class="remember"><input type="checkbox" name="remember" checked id="remember"> Remember Me</div>
				<button type="button" id="prelogin" class="btn-login">Login as <?php echo $row->username; ?></button>
			</p>
			<p class="account">Not <?php echo $row->username; ?>? <a href="javascript:switchAccount(1)">Switch accounts</a></p>
			<!-- <span class="responseMsg"></span> -->
			<p class="signup"><strong>Need an account?</strong> <br> <a href="home">Signup now</a></p>
		</div>
		<!-- login form shown when switching account -->
		<div id="login-form">
			<span class="toAccount" title="back to account" onclick="switchAccount(2)"><i class="fa fa-arrow-left"></i></span>
			<div class="form-header">Login</div>
			<form action="" method="POST" enctype="application/x-forms-urlencoded">
				<div class="form-group">
					<input type="email" class="form-input" id="email" name="email"  value="<?php (!empty($_COOKIE['email'])) ? print $_COOKIE['email'] : print''; ?>" placeholder="Enter email">
				</div>
				<div class="form-group">
					<input type="password" class="form-input" id="password" name="password"  value="<?php (!empty($_COOKIE['password'])) ? print $_COOKIE['password'] : print''; ?>" placeholder="Enter password">
				</div>
				<div class="form-group">
					<label class="checkbox-inline">
						<input type="checkbox" name="remember" <?php if(!empty($_COOKIE['email'])) { ?> checked <?php } ?> id="keep_logged"> Keep me logged in
					</label>
				</div>
				<div class="form-group">
					<button type="button" class="btn" onclick="switchAccount(3)">Continue</button>
				</div>
				<div class="form-group">
					<a href="javascript:void(0)" class="reset-link" onclick="switchAccount(4)">Logout now</a><!--  onclick="switchAccount(4)" -->
				</div>
			</form>
			<div class="responseMsg"></div>
		</div>

	</div>
</body>
</html>