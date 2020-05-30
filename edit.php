<?php
	require_once('controller.php');

	if(!isset($_REQUEST['profile_edit'])) {
		$user->page_protected();
		exit();
	}
	$row = $user->readProfileData($_SESSION['auth']['uid']);
?>

<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<style type="text/css">
	.container-edit {
		position: absolute;
		left: 0px;
		top: 50px;
		width: 350px;
		max-width: 100%;
		margin: 0 auto;
		padding: 10px;
		border-radius: 5px;
		background-color: rgba(245, 245, 245, 0.9);
		box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
		font-family: helvetica, arial;
		color: #3F729B;
		z-index: 1;
		text-align: left;
	}
	.container-edit::after {
		position: relative;
		bottom: 388px;
		/*left: -140px;*/
		content: ' ';
		width: 0;
		margin-right: 0px;
		margin-top: -382px;
		border: 18px solid;
		border-color: transparent transparent rgba(245, 245, 245, 0.9) transparent; /*rgba(245, 245, 245, 0.9)*/
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
		left: 5px;
		background: -webkit-linear-gradient(#f5f5f5, #d7d7d7);
		border: thin solid #bbb;
		color: #3F729B;
		padding: 5px 10px;
		border-radius: 5px;
		font-size: 20px;
		/*margin-left: -5px;*/
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
		margin-bottom: 2px;
	}
	.btn-save {
		position: relative;
		left: 200px;
		width: 100px;
		padding: 8px 2px;
		background: -webkit-linear-gradient(#0F7DC2, #3F729B);
		border: thin solid #3F729B;
		color: #fff;
		border-radius: 5px;
		font-weight: bold;
		cursor: pointer;
		transition: 0.55s;
		/*margin-top: -10px;*/
	}
	.btn-save:hover {
		background: -webkit-linear-gradient(#f5f5f5 ,#d7d7d7);
		color: #3F729B;
	}
	.btn-save:active {
		background: -webkit-linear-gradient(#d7d7d7 ,#f5f5f5);
	}
	label {
		text-align: left;
	}
	@media screen and (max-width: 480px) {
		.container-edit {
			left: -24px;
			/*top: 100px;*/
		}
		.btn-save {
			left: 160px;
		}
	}
</style>

<div class="container-edit">
	<div class="heading">Edit My Profile <i class="fa fa-close" title="Close" onclick="closeEdit();"></i></div>
	<div class="myaccount">
		<i class="fa fa-user icon"></i>
		<div class="edit-input">
			<div><label>Firstname:</label>
				<input type="text" class="edit-control" id="fname" name="firstname" value="<?php echo $row->firstname; ?>" placeholder="Firstname">
			</div>
			<div><label>Lastname:</label>
				<input type="text" class="edit-control" id="lname" name="lastname" value="<?php echo $row->lastname; ?>" placeholder="Lastname">
			</div>
			<div><label>Username:</label>
				<input type="text" class="edit-control" id="username" name="username" value="<?php echo $row->username; ?>" placeholder="Username">
			</div>
			<div><label>About Me:</label>
				<textarea class="edit-control" id="description" name="description" placeholder="Brief description"><?php echo $row->description; ?></textarea>
			</div>
		</div>
	</div>
	<button type="button" class="btn-save" onclick="saveProfile()"><i class="fa fa-floppy-o"></i> Save</button>
</div>