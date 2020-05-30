<?php
	require_once('controller.php');
	//uploading form
	if(!isset($_REQUEST['upload'])) {
		$user->page_protected();
		exit();
	}

	if(isset($_REQUEST['upload']) && $_REQUEST['upload'] == "true") {
		$request_uri = $_REQUEST['action'];
	}
	$disabled = ($request_uri=="thumbnail") ? 'disabled' : '';
?>
<style type="text/css">
	.upload_form {
		width: 500px;
		max-width: 100%;
		margin: 0 auto;
		background-color: #f5f5f5;
		padding: 10px 15px;
		border-radius: 5px;
		font-family: helvetica, arial, verdana;
		color: #777;
		margin-top: 80px;
	}
	.form-control {
		width: 100%;
		border-radius: 5px;
		padding: 5px;
		resize: none;
		border: thin solid #ccc;
		color: #777;
		margin-bottom: 15px;
	}
	input[type="file"] {
		background-color: #ffffff;
	}
	.page-head {
		font-size: 25px;
		text-align: center;
		margin-bottom: 10px;
	}
	.close-item {
		position: absolute;
		top: 2px;
		margin-left: 235px;
		padding: 4px 7px;
		background-color: transparent;
		color: #e42;
		border-radius: 5px;
		transition: 0.5s;
	}
	.close-item:hover {
		background-color: #e42;
		color: #fff;
		cursor: pointer;
	}
	.resposeMsg {
		text-align: center;
		color: #3F729B;
		margin-bottom: 10px;
	}
	.popIn {
      animation: pop .3s ease-in forwards;
    }
    @keyframes pop {
      0%   {transform: scale(0); opacity: 0; }
      90%   {transform: scale(1.1); opacity: 0.55; }
      100% {transform: scale(1); opacity: 1; }
    }
</style>
<script type="text/javascript">
	
</script>

<div class="upload_form popIn">
	<div class="page-head">Upload <?php echo $request_uri; ?> Image</div>
	<i class="fa fa-close close-item" onclick="closeUploadItem()"></i>
	<form action="post_controller.php" method="post" name="multiple_upload_form" id="multiple_upload_form" enctype="multipart/form-data">
	  <input type="file" class="form-control" name="images" id="fileUpload" autofocus />
	  <input type="hidden" name="upload_uri_obj" id="upload_uri" value="<?php echo $request_uri; ?>">
	  <!-- <input type="hidden" name="profileImage" id="profImg" value="<?php echo $_GET['profileImage']; ?>">
	  <input type="hidden" name="profileName" id="profName" value="<?php echo $_GET['profileName']; ?>"> -->
	  <!-- <textarea name="contentMessage" id="contentTitle" <?php echo $disabled; ?> class="form-control" rows="5" placeholder="Add caption here..."></textarea> -->
	  <center>
	    <button type="reset" class="btn btn-sm cancelForm" onclick="closeUploadItem()">Cancel <span class="glyphicon glyphicon-floppy-remove"></span></button>
	    <button type="button" class="btn btn-sm submit" name="upload" onclick="uploadImage()">Upload <span class="fa fa-upload"></span></button>
	  </center>
	</form>
	<div class="resposeMsg"></div>
</div>