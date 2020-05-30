<?php
$fullpath = $_SERVER['DOCUMENT_ROOT'].'/photobook/model/';
// controller
if(file_exists($fullpath.'model.php') && is_file($fullpath.'model.php')) {
	require_once($fullpath.'model.php');
} else {
	echo "Failed to include neccessary file!.";
}
//user login into account
/*$_REQUEST['login'] = "true";
$_REQUEST['email'] = "luckmolf@company.com";
$_REQUEST['password'] = "Luckys";
($user->isActive($_REQUEST['email'])=="true") ? print 'activated' : print 'not activated';*/
if(isset($_REQUEST['login']) && $_REQUEST['login'] == "true") {
	sleep(1);
	$params = array(
		'email' => $_REQUEST['email'],
		'password' => $_REQUEST['password'],
		'remember_me' => $_REQUEST['remember']
	);
	$sanitized = $user->sanitize_data($params);//clean data
	if(!empty($sanitized['email']) || !empty($sanitized['password'])) {
		if(filter_var($sanitized['email'], FILTER_VALIDATE_EMAIL)) {
			if($data=$user->login($sanitized['email'], $sanitized['password'])) {
				if($data==="activate") {
					$response['message'] = "<i class='fa fa-info-circle'></i> Please check your email inbox";
				} elseif($data==="invalid") {
					$response['message'] = "<i class='fa fa-warning'></i> Sorry, account is invalid!";
				} else {
					$response['message'] = "OK";
					$response['profilename'] = $data; //assign username
				}
				//quickly process login form cookies
				if(!empty($sanitized['remember_me'])) {
					//if remember checkbox is checked, then setcookie
					setcookie("email", $sanitized['email'], time() + (10 * 356 * 24 * 60 * 60));
	                setcookie("password", $sanitized['password'], time() + (10 * 356 * 24 * 60 * 60));
				} else {
					//else if remember was unchecked then, unset/erase cookie
					if(!empty($_COOKIE['email'])) { //unset email cookie
						setcookie("email", "", time() - (10 * 356 * 24 * 60 * 60));
					}
					if(!empty($_COOKIE['password'])) { //unset password cookie
		                setcookie("password", "", time() - (10 * 356 * 24 * 60 * 60));
	                }
				}
			} else {
				$response['message'] = "<i class='fa fa-warning'></i> Invalid login credentials";
			}
		} else {
			$response['message'] = "<i class='fa fa-warning'></i> Invalid email address!";
		}
	} else {
		$response['message'] = "<i class='fa fa-warning'></i> Please provide all required fields!";
	}
	echo json_encode($response);
	exit();
}
//user creating new account
if(isset($_REQUEST['register']) && $_REQUEST['register'] == "true") {
	sleep(1);
	$params = array(
		'firstname'=>$_REQUEST['firstname'],
		'lastname' => $_REQUEST['lastname'],
		'email' => $_REQUEST['email'],
		'password' => $_REQUEST['password'],
		'passwordConfirm' => $_REQUEST['passwordConfirm']
	);
	$clean_data = $user->sanitize_data($params);
	if(!empty($clean_data['firstname']) && !empty($clean_data['lastname']) && !empty($clean_data['email']) && !empty($clean_data['password'])) {
		if($clean_data['password'] == $clean_data['passwordConfirm']) {
			if(filter_var($clean_data['email'], FILTER_VALIDATE_EMAIL)) {
				if($user->signup($clean_data['firstname'], $clean_data['lastname'], $clean_data['email'], $clean_data['password'])) {
					$response['message'] = "<i class='fa fa-info-circle'></i> You have successfully registered!<br>Check your email for account activation!";
				} else {
					$response['message'] = "<i class='fa fa-warning'></i> Failed to register account!";
				}
			} else {
				$response['message'] = "<i class='fa fa-warning'></i> Invalid email address!";
			}
		} else {
			$response['message'] = "<i class='fa fa-warning'></i> Your password does not match!";
		}
	} else {
		$response['message'] = "<i class='fa fa-warning'></i> Please provide all required fields!";
	}
	echo json_encode($response);
	exit();
}
//user loggin out of account
if(isset($_REQUEST['signout']) && $_REQUEST['signout'] == "true") {
	if(isset($_REQUEST['autologin']) && $_REQUEST['autologin'] == "reset") {
		(!empty($_COOKIE['email'])) ? setcookie("email", "", time() - (10 * 356 * 24 * 60 * 60)) : '';
		(!empty($_COOKIE['password'])) ? setcookie("password", "", time() - (10 * 356 * 24 * 60 * 60)) : '';
		$response['response'] = "OK";
		echo json_encode($response);
		exit();
	}
	if($user->logout()) {
		// $response['message'] = "";
		header("Location: ../home");
	}
	echo json_encode($response);
	exit();
}

if(isset($_REQUEST['saveprofile']) && $_REQUEST['saveprofile'] == "true") {
	$params = array(
		'firstname' => $_REQUEST['firstname'],
		'lastname' => $_REQUEST['lastname'],
		'username' => $_REQUEST['username'],
		'description' => $_REQUEST['description'],
		'uid' => $_SESSION['auth']['uid']
	);
	$sanitized = $user->sanitize_data($params);//clean data
	$sanitized['description'] = nl2br($sanitized['description']);
	$result = $user->updateProfile($sanitized['firstname'], $sanitized['lastname'], $sanitized['username'], $sanitized['description'], $sanitized['uid']);
	if($result) {
		$response['message'] = "Profile updated successfully!";
	}
	else {
		$response['message'] = "Failed to update profile";
	}
	echo json_encode($response);
	exit();
}

if(isset($_REQUEST['changepassword']) && $_REQUEST['changepassword'] == "true") {
	$params = array(
		'newpassword' => $_REQUEST['newpassword'],
		'confirmpassword' => $_REQUEST['confirmpassword'],
		'uid' => $_SESSION['auth']['uid']
	);
	$sanitized = $user->sanitize_data($params);//clean data
	if($sanitized['newpassword'] != $sanitized['confirmpassword']) {
		$response['message'] = "Your password does not match";
	} else {
		if($user->updateSettings($sanitized['newpassword'], $sanitized['uid'])) {
			$response['message'] = "Password changed successfully!";
		} else {
			$response['message'] = "Failed to change your password!";
		}
		//reset any cookie values, if was set
		if(!empty($_COOKIE['email'])) { //unset email cookie
			setcookie("email", "", time() - (10 * 356 * 24 * 60 * 60));
		}
		if(!empty($_COOKIE['password'])) { //unset password cookie
            setcookie("password", "", time() - (10 * 356 * 24 * 60 * 60));
        }
	}
	echo json_encode($response);
	exit();
}

?>