<?php
	//login and register forms
	/*if(isset($_REQUEST['login_register']) && $_REQUEST['login_register']=="true") { //forms should hide until request is set
		return false;
		exit();
	}*/
?>
<div id="login-form">
	<div class="form-header">Login</div>
	<form action="" method="POST" enctype="application/x-forms-urlencoded">
		<div class="form-group">
			<input type="email" class="form-input auth-forms" id="email1" name="email1" placeholder="Enter email" autofocus>
		</div>
		<div class="form-group">
			<input type="password" class="form-input auth-forms" id="password1" name="password1" placeholder="Enter password">
		</div>
		<div class="form-group">
			<label class="checkbox-inline">
				<input type="checkbox" name="remember" id="remember"> Remember Me
			</label>
		</div>
		<div class="form-group">
			<button type="button" id="login" class="btn">Login</button>
		</div>
	</form>
	<div class="responseMsg"></div>
</div>

<div id="register-form">
	<div class="form-header">Register</div>
	<form action="" method="POST" enctype="application/x-forms-urlencoded">
		<div class="form-group">
			<input type="text" class="form-input auth-forms" id="firstname" name="firstname" placeholder="Enter firstname" autocomplete="off">
		</div>
		<div class="form-group">
			<input type="text" class="form-input auth-forms" id="lastname" name="lastname" placeholder="Enter lastname" autocomplete="off">
		</div>
		<div class="form-group">
			<input type="email" class="form-input auth-forms" id="email2" name="email2" placeholder="Enter email" autocomplete="off">
		</div>
		<div class="form-group">
			<input type="password" class="form-input auth-forms" id="password2" name="password2" placeholder="Enter password">
		</div>
		<div class="form-group">
			<input type="password" class="form-input auth-forms" id="password_confirm" name="password_confirm" placeholder="Confirm password">
		</div>
		<div class="form-group">
			<button type="button" id="register" class="btn">Register</button>
		</div>
	</form>
	<div class="responseMsg"></div>
</div>
