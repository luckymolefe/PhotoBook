<?php
if(!isset($_GET['comment'])) {
	return false;
	exit();
}
//Detect user agent
if( strstr($_SERVER['HTTP_USER_AGENT'], "Firefox") ){
	$margin_position = "0px"; //422px positioning the comment-box popup by detecting user agent
	$top_position = "-100px";
}
else {
	$margin_position = "-420px";
	$top_position = "-85px";
}

?>
<style type="text/css">
	.container-popup {
		position: absolute;
		/*left: 90px;*/
		bottom: 10px;
		width: 450px;
		max-width: 100%;
		margin: 0 auto;
		background-color: rgba(255, 255, 255, 0.8);
		margin-left: 185px;
		margin-top: <?php echo $margin_position; ?>;
		padding: 5px 5px 5px 5px;
		text-align: center;
		border-radius: 4px;
		box-shadow: -1px -1px 5px rgba(0, 0, 0, 0.5);
		font-family: arial, helvetica;
		font-size: 13px;
		z-index: 0;
	}
	.container-popup::after {
		position: absolute;
		bottom: -36px;
		right: 15px;
		content: ' ';
		width: 0;
		/*float: right;*/
		margin-right: -5px;
		margin-top: <?php echo $top_position; ?>;
		border: 18px solid;
		border-color: rgba(225, 225, 225, 0.9)  transparent transparent transparent;
	}
	@media screen and (max-width: 660px) {
		.container-popup {
			margin-left: 50px;
		}
	}
	@media screen and (max-width: 600px) {
		.container-popup {
			margin-left: 30px;
		}
	}
	@media screen and (max-width: 560px) {
		.container-popup {
			margin-left: 5px;
			margin-top: -442px;
		}
	}
	@media screen and (max-width: 490px) {
		.container-popup {
			width: auto;
			margin-left: -10px;
			margin-top: -452px;
		}
	}
	textarea {
		resize: none;
		text-indent: none;
	}
	.input-control:focus {
		border-color: #66afe9;
		outline: 0;
		-webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);
		      box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);
	}
	.comment-input {
		max-width: 100%;
		width: 100%;
		margin: 0 auto;
		padding: 15px 0 10px 0;
		border: thin solid #bbb;
		border-radius: 3px;
		text-indent: 10px;
		color: #555;
		font-weight: bold;
		resize: none;
	}
	.btn-comment {
		width: 150px;
		padding: 10px 5px;
		border: thin solid #46b8da;
		border-radius: 5px;
		font-weight: bold;
		color: #fff;
		background-color: #3F729B;
		border-color: #3F729B;
		margin-top: 5px;
	}
	.btn-comment:hover {
		background: #5bc0de;
		border-color: #5bc0de;
		cursor: pointer;
	}
	.btn-comment:active {
		background-color: #146eb4;
		color: #fff;
		border: thin solid #c5d5cd;
		outline: 0 none;
	}
	.btn-comment.btn-block {
		width: 100%;
	}
	.btn-comment.btn-small {
		width: 50px;
	}
	.close-item {
		/*background-color: rgba(255, 255, 255, 0.8);*/
		color: #e42;
		border-radius: 2px;
		padding: 2px 3px 2px 3px;
		font-size: 15px;
		cursor: pointer;
		float: right;
		margin-top: -3px;
		margin-right: -2px;
		/*box-shadow: 1px 2px 5px rgba(0, 0, 0, 0.5);*/
	}
	.close-item:hover {
		background-color: #e42;
		color: #fff;
	}
	#viewComments {
		color: #146eb4;
		font-size: 12px;
		text-decoration: none;
		display: inline-block;
		float: right;
		margin-top: 33px;
		margin-right: -14px;
		font-size: 20px;
	}
	#viewComments:hover {
		color: #555;
	}
	#viewComments:active {
		color: #e42;
	}
	.popIn {
    	animation: popIn .3s ease forwards;
	}
	@keyframes popIn {
	  	0%  { transform: scale(0); opacity: 0; }
	  	90%  { transform: scale(1.1);  opacity: 0.85; }
	  	100%{ transform: scale(1); opacity: 1; }
	}
	.emojis {
		max-height: 400px;
		text-align: left;
		border-top: thin solid #ccc;
		/*border-bottom: thin solid #ccc;*/
		padding: 5px 0px;
		overflow-y: auto;
	}
</style>

<div class="container-popup popIn">
	<span class="fa fa-close close-item" title="Close" onclick="closePopup(this);"></span>
	<form action="" method="POST" enctype="application/x-www-urlencoded">
		<input type="hidden" id="user_id" name="user_id" value="<?php echo $user_id; ?>">
		<input type="hidden" id="media_id" name="media_id" value="<?php echo $media_id; ?>">
		<div>Reaction</div>
		<div class="emojis">
			<?php
				$dir = 'images/emoji';
				$emoji_items = scandir($dir);
				foreach($emoji_items as $item) :
					if($item != '.' && $item != '..') {
						$item = str_replace(' ', '%20', $item);
			?>
				<a href="<?php echo $item; ?>" onclick="return addEmoji(this);"><img src="<?php echo $dir.'/'.$item; ?>" title="<?php echo $item; ?>" class="emoji_icon"></a>
			<?php
					}
				endforeach;
			?>
		</div>
	</form>
</div>
