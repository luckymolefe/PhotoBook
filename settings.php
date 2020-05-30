<?php
	require_once('controller.php');

	if(!isset($_REQUEST['settings'])) {
		$user->page_protected();
		exit();
	}
?>

<style type="text/css">
	.container-edit {
		position: relative;
		left: 340px; /*700*/
		top: 35px; /*76*/
		width: 350px;
		max-width: 100%;
		margin: 0 auto;
		padding: 10px;
		border-radius: 5px;
		background-color: rgba(245, 245, 245, 0.9);
		box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
		font-family: arial, helvetica;
		color: #3F729B;
		z-index: 1;
	}
	.container-edit::after {
		position: relative;
		left: 190px;
		bottom: 261px;
		content: ' ';
		width: 0;
		margin-right: -100px;
		margin-top: -382px;
		border: 18px solid;
		border-color: transparent transparent rgba(255, 255, 255, 0.6) transparent; /*rgba(225, 225, 225, 0.6)*/
	}
	.heading {
		font-size: 25px;
		text-align: center;
		padding-bottom: 10px;
	}
	.heading > i {
		float: right;
		margin: -8px -8px 0 0;
		cursor: pointer;
		font-size: 15px;
		background-color: #f5f5f5;
		color: #e42;
		padding: 3px 6px;
		border-radius: 5px;
	}
	.heading > i:hover {
		background-color: #e42;
		color: #f5f5f5;
	}
	.icon {
		position: absolute;
		background: -webkit-linear-gradient(#f5f5f5, #d7d7d7);
		border: thin solid #bbb;
		color: #3F729B;
		padding: 5px 10px;
		border-radius: 5px;
		font-size: 20px;
		margin-left: -5px;
	}
	.edit-input {
		max-width: 100%;
		margin: 0 auto;
		border: thin solid #ccc;
		border-radius: 5px;
		margin-left: 35px;
		padding: 10px;
		font-size: 16px;
		font-weight: bold;
		margin-bottom: 5px;
	}
	.edit-control {
		width: 100%;
		max-width: 100%;
		border-radius: 5px;
		resize: none;
		border: thin solid #ccc;
		padding: 8px 0;
		text-indent: 5px;
		color: #777;
		margin-bottom: 5px;
	}
	.btn-save {
		position: relative;
		left: 230px;
		width: 100px;
		padding: 8px 2px;
		background: -webkit-linear-gradient(#0F7DC2, #3F729B); /*-webkit-linear-gradient(#f5f5f5, #d7d7d7);*/
		border: thin solid #3F729B;
		color: #fff; /*#777;*/
		border-radius: 5px;
		font-weight: bold;
		cursor: pointer;
		transition: 0.55s;
	}
	.btn-save:hover {
		background: -webkit-linear-gradient(#f5f5f5, #d7d7d7);
		color: #3F729B;
	}
	.btn-save:active {
		background: -webkit-linear-gradient(#d7d7d7 ,#f5f5f5);
	}
	@media screen and (max-width: 900px) {
		.container-edit {
			left: 240px;
		}
	}
	@media screen and (max-width: 768px) {
		.container-edit {
			left: 30px;
		}
	}
	@media screen and (max-width: 480px) {
		.container-edit {
			width: 250px;
			left: -30px;
		}
		.container-edit::after {
			left: 100px;
		}
		.btn-save {
			left: 150px;
		}
	}
</style>

<div class="container-edit">
	<div class="heading">Settings <i class="fa fa-close" title="Close" onclick="closeEdit();"></i></div>
	<div class="myaccount">
		<i class="fa fa-lock icon"></i>
		<div class="edit-input">
			<div>New password: 
				<input type="password" class="edit-control" id="newpassword" name="new_password" placeholder="Enter new password">
			</div>
			<div>Confirm New password: 
				<input type="password" class="edit-control" id="passwordConfirm" name="password_confirm" placeholder="Confirm new password">
			</div>
		</div>
		<i class="fa fa-"></i>
	</div>
	<button type="button" class="btn-save" onclick="changePassword()"><i class="fa fa-lock"></i> Change</button>
</div>