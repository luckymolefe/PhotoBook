
$(function() {
	$('.container-fluid').delay(15000).addClass('fade_out');
	//on click display login form
	$('.show-login').click(function() {
		$('.responseMsg').html('').hide();
		if($('#login-form').is(':hidden')) {
			$('#login-form').fadeIn('slow');
			$('#register-form').hide();
		}
		else {
			$('#login-form').hide();
		}
	});
	//on click display registration form
	$('.show-register').click(function() {
		$('.responseMsg').html('').hide();
		if($('#register-form').is(':hidden')) {
			$('#register-form').fadeIn('slow');
			$('#login-form').hide();
		}
		else {
			$('#register-form').hide();
		}
	});
	//toggle hidden menu for small size screen
	$('.menu-bar').click(function() {
		$('.hidden-menu').slideToggle('slow');
	});
	//on user Login run this
	$('#login, #prelogin').click(function() {
		var email, password;
		email = $('#email1').val().trim();
		password = $('#password1').val().trim();
		if($('#remember').is(':checked')) {
			var rememberUser = "on";
		} else {
			var rememberUser = "";
		}
		if(email == '' || password == '') {
			// alert('Please enter your email and password!');
			$('.responseMsg').html('<i class="fa fa-warning"></i> Please enter your email and password!').show();
			return false;
		}
		$('#prelogin').html('Loading...').addClass('flash');
		var url = {"login":"true", "email":email, "password":password, "remember":rememberUser};
		saveData(url);
	});
	//on user Register user run this
	$('#register').click(function() { 
		var firsname, lastname, email, password, passwordConfirm;
		firstname = $('#firstname').val().trim();
		lastname = $('#lastname').val().trim();
		email = $('#email2').val().trim();
		password = $('#password2').val().trim();
		passwordConfirm = $('#password_confirm').val().trim();
		if(firstname=='' || lastname=='' || email=='' || password=='' || passwordConfirm=='') {
			// alert("Please type in all fields are required!");
			$('.responseMsg').html('<i class="fa fa-warning"></i> Please type in all fields are required!').show();
			return false;
		}
		if(password != passwordConfirm) {
			// alert('Your passwords does not match!');
			$('.responseMsg').html('<i class="fa fa-warning"></i> Your passwords do not match!').show();
			return false;
		}
		var url = {"register":"true","firstname":firstname, "lastname":lastname, "email":email, "password":password, "passwordConfirm":passwordConfirm};
		saveData(url);
	});

	$('.like').on('mousedown', function() { //onclick like icon run this
		$(this).children().removeClass('fa-heart-o').addClass('fa-heart');
		$(this).children().addClass('puff');
		var mid = $(this).data('mediaid');
		addLike(mid);
	});

	$("div a[href='#top']").on('click', function(event) {
      // Prevent default anchor click behavior
      event.preventDefault();
      // Store hash
      var hash = this.hash;
      // Using jQuery's animate() method to add smooth page scroll
      // The optional number (900) specifies the number of milliseconds it takes to scroll to the specified area
      $('html, body').animate({
        scrollTop: $(hash).offset().top
      }, 2000, function() {
        // Add hash (#) to URL when done scrolling (default click behavior)
        window.location.hash = hash;
      });
    });

    $('.side-profile-image').on('mouseenter', function() {
    	$('.upload-thumbnail').stop(true, true).fadeIn('slow');
		$('.upload-thumbnail').on('mouseleave', function() {
			$('.upload-thumbnail').fadeOut('slow');
		});
    });
	
});
//on login/register form submission
function saveData(urlparams) {
	$('.responseMsg').html('').hide();
	$.ajax({
		url: "controller.php",
		type: "POST",
		data: urlparams,
		cache: false,
		beforeSend: function() {
			$('.responseMsg').html('<i class="fa fa-spinner fa-pulse"></i> Please wait...').show();
		},
		success: function(data) {
			// alert(data);
			var jason = JSON.parse(data);
			if(jason.message=="OK") {
				// window.location.href = jason.profilename;
				window.open(''+jason.profilename+'', '_self');
			} else {
				$(':input, :password').val(''); //reset all inputs after successful registration
				$('.responseMsg').html(jason.message).show();
			}
		}
	});
}
//show popup with emojis and post comment button
function showOptions(action) {
  $('.options-panel').html('');
  var userID = action.dataset.uid;
  var mediaID = action.dataset.mediaid;
  $.get("comment.php", {"comment":"true", "uid":userID, "media_id":mediaID}, function(data) {
    action.parentNode.parentNode.children[0].innerHTML = ""; //set to empty element first
    action.parentNode.parentNode.children[0].innerHTML = data; //then add new data to element
  });
}
//close emoji popup window
function closePopup(action) {
  action.parentNode.parentNode.innerHTML = "";
  /*$('.close-item').parent().animate({'height':'0px','width':'0px'}, 300, function() {
  	action.parentNode.parentNode.innerHTML = "";
  });*/
}
//onclicking of emoji add data to comment box
function addEmoji(action) {
	var urlpath = action.getAttribute('href');
	urlpath = urlpath.replace(new RegExp('.png', 'g'), '');
	// action.parentNode[5].children[4].value = urlpath;
	$('.comment-box').val(function(index,value) {
		return value+" ["+urlpath+"] ";
	});
	return false;
}
//call to save comments, on user commenting
function postComment(action) {
	$('.comment-box').removeClass('errorHighlight'); //reset error alert
	var comment = action.parentNode.children[2].value;
	var mid = action.parentNode.children[3].value;
	if(comment=='') {
		action.parentNode.children[2].classList.add('errorHighlight');
		alert('Please type your comment first!');
		return false;
	}
	$('.container-popup').parent().html(''); 
	action.children[0].classList.add('flash'); //childNodes
	$.post('post_controller.php', {"postcomment":"true", "media_id":mid, "comment":comment}, function(response) {
		$('.comment-box').val('');
		var jason = JSON.parse(response);
		action.children[0].classList.remove('flash');
		alert(jason.message);
	});
}
function logout() { //confirm user logout
	var action = confirm('You want to signout?');
	if(action == false) {
		return false;
	}
}

function editPopup() { //call edit popup window
	$('.profile-settings').html('');
	$.get("edit.php", {"profile_edit":"true"}, function(data) {
		$('.profile-edit').html(data);
	});
}

function openSettings() { //call settings popup window
	$('.profile-edit').html('');
	$.get("settings.php", {"settings":"true"}, function(data) {
		$('.profile-settings').html(data);
	});
}

function saveProfile() { //call to save edit profile edit
	var firstname, lastname, username, description;
	firstname = $('#fname').val().trim();
	lastname = $('#lname').val().trim();
	username = $('#username').val().trim();
	description = $('#description').val().trim();
	if(firstname =='' || lastname =='' || username =='' || description =='') {
		alert("Please provide all required fields.");
		return false;
	}
	$.post("controller.php", {"saveprofile":"true", "firstname":firstname, "lastname":lastname, "username":username, "description":description}, function(data) {
		var jason = JSON.parse(data);
		alert(jason.message);
		closeEdit(); //and close popup window
	});
}

function closeEdit() {
	$('.container-edit').animate({"height":"0px"}, 100, function() {
		$('.profile-edit').html('');
		$('.profile-settings').html('');
	});
}

function changePassword() {
	var newPassword, confirmPassword;
	newpassword = $('#newpassword').val().trim();
	confirmPassword = $('#passwordConfirm').val().trim();
	if(newpassword == '' || confirmPassword == '') {
		alert('Please enter your new passowrd!');
		$('#newpassword, #passwordConfirm').addClass('errorHighlight');
		return false;
	}
	if(newpassword != confirmPassword) {
		alert('Your new passowrd does not match!');
		return false;
	}
	$.post("controller.php", {"changepassword":"true", "newpassword":newpassword, "confirmpassword":confirmPassword}, function(data) {
		var jason = JSON.parse(data);
		alert(jason.message);
		closeEdit();
	});
}
//User persistent data pre-login and switch login account
function switchAccount(action) {
	$('.responseMsg').html('').hide();
	if(action=='1') { //closing preLogin form and open Login form
		$('#login-form').show().animate({'margin-left':'-20px'}, 600, function() {
			$('.prelogin').fadeOut('fast');
		});
	}
	if(action=='2') { //closing login form and open preLogin
		$('#login-form').animate({'margin-left':'380px'}, 500, function() {
			$('#login-form').fadeOut('fast');
			$('.prelogin').fadeIn('fast');
		});
	}
	if(action=='3') { //action for loggin in
		var email, password;
		email = $('#email').val().trim();
		password = $('#password').val().trim();
		if($('#keep_logged').is(':checked')) {
			var rememberUser = "on";
		} else {
			var rememberUser = "";
		}
		if(email == '' || password == '') {
			// alert('Please enter your email and password!');
			$('.responseMsg').html('<i class="fa fa-warning"></i> Please enter your email and password to login!').show();
			return false;
		}
		var url = {"login":"true", "email":email, "password":password, "remember":rememberUser};
		saveData(url); //call to save data
	}
	if(action=='4') {
		var action = confirm('Logout from this account?');
		if(action == false) {
			return false;
		}
		$('.responseMsg').html('<i class="fa fa-spinner fa-pulse"></i> Logging out...').show();
		$.post("controller.php", {"signout":"true", "autologin":"reset"}, function(data) {
			var jason = JSON.parse(data);
			if(jason.response == "OK") {
				location.reload(true);
			}
		});
	}
}

function show_upload(action) { //helper functiont to open
	if(action == "wallpost") {
		$.get("post_upload.php", {"upload":"true", "action":action}, function(data) {
			$('.layer').html(data).show();
		});
	}
	if(action == "thumbnail") {
		$.get("post_upload.php", {"upload":"true", "action":action}, function(data) {
			$('.layer').html(data).show();
		});
	}
}

function closeUploadItem() {
	$('.layer').html('').fadeOut('slow');
}

function self_autoload() {
	return location.reload(true);
}

function uploadImage() {
	if($('#fileUpload').val() == "") { //if file upload field empty
		$('#fileUpload').addClass('errorHighlight');
		$('.resposeMsg').addClass('error-text');
		$('.resposeMsg').html('<i class="fa fa-warning"></i> Please select file to upload!').show();
		return false;
	}
	//instantiate formData object
	var formdata = new FormData();
	var mediaFileName = document.getElementById('fileUpload').files[0]; //get the first File value from the form
	formdata.append("posttowall", "true"); //then append all form information data to be send via ajax server request
	formdata.append("mediaUpload", mediaFileName);
	formdata.append("contentTitle", $('#contentTitle').val());
	// formdata.append("userId", $('#userId').val());
	formdata.append("uploading_uri", $('#upload_uri').val().trim());
	// formdata.append("profileName", $('#profName').val().trim());
	$.ajax({
		url: "post_controller.php",
		type: "POST",
		data: formdata,
		contentType: false,
		cache: false,
		processData: false,
		beforeSend: function() {
			$('.resposeMsg').removeClass('error-text');
			$('.resposeMsg').html('<i class="fa fa-spinner fa-pulse"></i> Loading...').show();
		},
		success: function(jsonDataResponse) {
			// alert(jsonDataResponse);
			var jason = JSON.parse(jsonDataResponse);
			if(jason.message == "OK") {
				$('.resposeMsg').html('Image uploaded successfully!...');
				setTimeout(function() {
					closeUploadItem(); //then window when upload finished
					self_autoload();
				}, 1000);
			} else {
				$('.resposeMsg').html(jason.message);
			}
		}
	});
}

function addLike(action) {
	// var mid = action.parentNode.children[3].value;
	var mid = action;
	$.post('post_controller.php', {"addlike":"true", "media_id":mid}, function(response) {
		var jason = JSON.parse(response);
		alert(jason.message);
	});
}

function zoomInImage(action, mediaID) {
	$('.layer').html(''); //clear layer first
	var urlpath = '<img src="images/wallposts/'+action+'" class="imageZoom"/>'+
	'<i class="fa fa-close close-imageZoom" title="Close" onclick="closeUploadItem();"></i>'+
	'<span class="fa fa-comment link-comment" title="Comment on this" onclick="openPost('+mediaID+')"></span>';
	$('.layer').html(urlpath).show(); //then load new element
}

function openPost(postID) {
	location.href = "home#"+postID; //redirect to home and jump to post
}

function login() {
	location.href = "login";
}

function doSearch() {
	var searchTerm = $('.search').val().trim();
	if(searchTerm=='') {
		$('.search').addClass('errorHighlight');
		return false;
	} else {
		$.ajax({
			url: "post_controller.php",
			type: "GET",
			data: {"search":"true", "query":searchTerm},
			cache: false, 
			beforeSend: function() {
				// $('.posts').html('<i class="fa fa-spinner fa-pulse loader"></i> Loading...');
				$('.posts, .profile-contents').html('').addClass('loading');
			},
			success: function(responsedata) {
				$('.posts, .profile-contents').removeClass('loading');
				$('.posts, .profile-contents').html(responsedata);
			}
		});
	}
}

function followUser(action, uid) {
	var action = confirm('Follow this user?');
	if(action == false) {
		return false;
	}
	var follow_id = uid;//action.dataset.follow_user;
	
	$('#follow').html('<i class="fa fa-spinner fa-pulse"></i> Following...');
	if(follow_id != '') {
		$.post('post_controller.php', {"following":"true", "follow_id":follow_id}, function(response_data) {
			var jason = JSON.parse(response_data);
			$('#follow').html('Follow');
			if(jason.response=="OK") {
				alert(jason.message);
				self_autoload();
			} else {
				alert(jason.message);
			}
		});
	} else {
		alert("Sorry something went wrong we are busy fixing it");
	}
}
function openMessaging(uid) {
	$.post('messaging.php', {"sendingOptions":"true", "id":uid}, function(response) {
		$('.layer').html(response).show();
	});
}
function sendMessage(action) {
	var receiver_id = action.dataset.receiver;
	var message = $('#message').val().trim();
	if(message != '') {
		var action = confirm('Send message?');
		if(action == false) {
			return false;
		}
		$('#send').html('<i class="fa fa-spinner fa-pulse"></i> Sending...');
		$('.btn-send').attr('disabled',true);
		$.post('post_controller.php', {"sending":"true", "receiverId":receiver_id, "messageText":message}, function(response) {
			var jason = JSON.parse(response);
			$('.msg_response').html(jason.message);
			$('#send').html('Send Message');
			$('.btn-send').attr('disabled', false);
			$('#message').val('');
			setTimeout(function() {
				closeUploadItem(); //then window when finished
			}, 1500);
		});
	} else {
		$('#message').addClass('errorHighlight');
		$('.btn-send').attr('disabled',false);
		alert("Please type your message!");
		$('.msg_response').html('<i class="fa fa-warning"></i> Please type your message!');
		return false;
	}
}
function checkActiveMessage(uid) {
	$.post("post_controller.php", {"checkMessages":"true", "requesterId":uid}, function(response) {
		// alert(response);
		var jason = JSON.parse(response);
		if(jason.response=="OK") {
			$('.notifyPop').addClass('zoomIn').html(jason.message).show();
		}
	});
}

function closeMessagePop(action, msg_dated) {
	action.parentNode.style.display='none';
	$.post("post_controller.php", {"unsetMessage":"true", "dated":msg_dated}, function(data) {
		// alert(data);
		var jason = JSON.parse(data);
		// alert(jason.response);
		$('.notifyPop').html('<span class="fa fa-check"></span> '+jason.response).show();
		$('.notifyPop').delay(3000).fadeOut('slow');
	});
}

function loadRecommend(uri_path) {
	$.post("post_controller.php", {"getrecommends":"true"}, function(responsedata) {
		// alert(responsedata);
		var jason = JSON.parse(responsedata);
		if(jason.message=="OK") {
			var userData = jason.randomUser;
			$.post("recommend.php", {"recommend":"true", "uid":userData, "uri_path":uri_path}, function(data) {
				$('.recommendations').html(data);
			});
		}
	});
}

setInterval(function() {
	loadRecommend();
}, 50000);

/*setTimeout(function() {
	$('.container-fluid').delay(15000).addClass('fade_out');
}, 15000);*/


