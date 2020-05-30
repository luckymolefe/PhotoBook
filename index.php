<?php
	//photobook home page
	if(file_exists('post_controller.php') && is_file('post_controller.php')) {
		require_once('post_controller.php');
	}
	if(!isset($_SESSION['auth']['token'])) { //only if user session not active then,
		$url_direct = ($user->keepLoggedIn()) ? 'javascript:login()' : 'javascript:void(0)'; //then redirect to stored session login
	}
	//if user not loggedin set to TRUE...else...FALSE
	$logged = (isset($_SESSION['auth']['token'])) ? true : false;  
	$row = (isset($_SESSION['auth']['uid'])) ? $user->readProfileData($_SESSION['auth']['uid']) : null; //read user profile details
	$allposts = $post->readWallPosts();
	#include_once('account/auth_forms.php');
?>
<!DOCTYPE html>
<html>
<head>
	<title>Photobook :: Home</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="styles/styles.css">
	<link rel="stylesheet" type="text/css" href="../boostrap3/font-awesome/css/font-awesome.min.css">
	<script type="text/javascript" src="../boostrap3/jquery/jquery-1.12.4.min.js"></script>
	<script type="text/javascript" src="js/photobook.js"></script>
	<link rel="icon" type="image/png" href="images/logos/logo_1_icon.png"/>
	<link rel="shortcut icon" type="image/x-icon" href="images/logos/logo_1_icon.png"/>
</head>
<body id="top" data-spy="scroll">
	<?php include_once('account/auth_forms.php'); ?>
	<div class="layer"></div>
	<div class="container-menu">
		<nav class="navbar">
			<ul>
				<li><a href="home"><img src="images/logos/logo_2.png" class="logo"/></a></li>
				<li><input type="search" class="form-input search" name="search" placeholder="Search..."><i class="fa fa-arrow-right btn-search" title="submit query" onclick="doSearch()"></i></li>
				<?php if($logged) { ?>
					<li><a href="<?php echo $row->username; ?>" id="profile" class="nav-link">myProfile</a></li>
					<li><a href="signout/true" id="logout" class="nav-link" onclick="return logout();">Logout</a></li>
				<?php } else { ?>
					<li><a href="<?php echo $url_direct; ?>" class="show-login nav-link">Login</a></li>
					<li><a href="javascript:void(0)" class="show-register nav-link">Register</a></li>
				<?php } ?>
			</ul>
			<div class="fa fa-bars menu-bar" title="Toggle Menu"></div>
		</nav>
	</div>
	
	<div class="hidden-menu"> <!-- hidden top drop-menu -->
		<?php if($logged) { ?>
			<li><i class="fa fa-user"></i> <a href="<?php echo $row->username; ?>" id="profile">myProfile</a></li>
			<li><i class="fa fa-power-off"></i> <a href="signout/true" id="logout" onclick="return logout();">Logout</a></li>
		<?php } else { ?>
			<li><i class="fa fa-sign-in"></i> <a href="<?php echo $url_direct; ?>" class="show-login">Login</a></li>
			<li><i class="fa fa-user-plus"></i> <a href="javascript:void(0)" class="show-register">Register</a></li>
		<?php } ?>
	</div>
		
	<div class="contents-wrapper">
		<div class="posts">
		<?php
		if(count($allposts) > 0) {
			foreach($allposts as $res) :
				$owner = $user->readProfileData($res->user_id);
				$likes = $post->countLikes($res->media_id);
				$commentsCount = $post->countComments($res->media_id);
				// $myLike = $comment->readOneComment($res->user_id, $res->media_id);
				$myLike = (isset($_SESSION['auth']['uid'])) ? $comment->readLikes($row->id, $res->media_id) : 0;
				$allComments = $comment->readAllComments($res->media_id);
		?>
			<!-- post item starts -->
			<li class="post-card" id="<?php echo $res->media_id; ?>">
				<div class="post-card-username">
					<span><img src="images/profile_thumbnails/<?php echo $owner->profile_url; ?>" class="user-thumbnail"></span>
					<span class="names"><a href="<?php echo $owner->username; ?>"><?php echo $owner->username; ?></a></span>
					<span class="post-card-time"><?php echo  $post->timeDiff($res->created); ?></span>
				</div>
				<span><img src="images/wallposts/<?php echo $res->urlpath; ?>" class="post-card-resource" onclick="zoomInImage('<?php echo $res->urlpath; ?>', null);"></span>
				<div><?php echo $media_title = (!empty($res->media_title)) ? $res->media_title : ''; ?></div>
				<div class="post-card-stats"><?php echo ($likes > 0) ? $likes.' likes' : '<small>Be first to like <i class="fa fa-heart pulse-icon"></i> this!</small>'; ?></div>
				<div class="card-comments">
					<?php if($commentsCount > 0) {
							$allusers = $user->readAllUsers();
							foreach($allComments as $commented) :
								foreach($allusers as $ownerUser) {
									if($commented->user_id == $ownerUser->id) {
										$username = $ownerUser->username;
										$photo_url = $ownerUser->profile_url;
									}
								}
					?>
					<div class="post-card-comment">
						<span><img src="images/profile_thumbnails/<?php echo $photo_url;?>" class="comments-thumbnail"/></span>
						<span class="user-comment" id="user-comment"><a href="<?php echo $username; ?>"><?php echo $username; ?></a></span>
						<?php
							//look for an emoji within haystack comment
							$commented->comment = preg_replace('/(\[)/', '<img src="images/emoji/', $commented->comment);
							$commented->comment = preg_replace('/(\])/', '.png" class="emoji_icon" />', $commented->comment);
							echo $commented->comment;
						?>
					</div>
					<?php endforeach; } ?>
				</div>
				
				<div class="post-card-footer">
					<span class="options-panel"></span>
					<?php if($myLike != false && $myLike->totalLikes > 0 && $logged == true) { ?>
					<span class="liked" title="Already liked">
						<i class="fa fa-heart"></i>
					</span>
					<?php } else { ?><!-- onclick="return addLike(this);" -->
					<span class="like" title="Love this" data-mediaid="<?php echo $res->media_id; ?>"> 
						<i class="fa fa-heart-o"></i>
					</span>
					<?php } ?>
					<input type="text" name="comment" class="comment-box" placeholder="Add a comment...">
					<input type="hidden" class="mediaid" name="mediaid" value="<?php echo $res->media_id; ?>">
					<span class="post_comment" title="post comment" onclick="return postComment(this);">
						<i class="fa fa-commenting"></i>
					</span>
					<span class="card-footer-options">
						<i class="fa fa-ellipsis-h" onclick="showOptions(this)"></i>
					</span>
				</div>
			</li>
		<?php endforeach; } ?>
			<!-- post item end -->
		</div><!-- END DIV posts -->
		<div class="scroll"><a href="#top" title="To Top"><i class="fa fa-chevron-up"></i></a></div>
	</div>
</body>

</html>