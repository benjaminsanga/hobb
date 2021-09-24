<?php
require_once 'data.php';

if (isset($_POST['change_photo'])) {
	$new_photo = $_FILES['new_photo'];
	$email = $_COOKIE['user'];
	if (move_uploaded_file($new_photo['tmp_name'], "../profile_photos/".$new_photo['name'])) {
		# put in table
		$stmt = $data_servant->pdo->prepare("UPDATE hobb_people SET profile_pic = ? WHERE email = ?");
		if ($stmt->execute(array($new_photo['name'], $email))) {
			# go to settings page
			header('location: ../settings.php');
		}
	} else {
		header('location: ../settings.php');
	}
} else {
	header('location: ../settings.php');
}

if (isset($_POST['change_profile_photo'])) {
	$new_photo = $_FILES['new_photo'];
	$email = $_COOKIE['user'];
	if (move_uploaded_file($new_photo['tmp_name'], "../profile_photos/".$new_photo['name'])) {
		# put in table
		$stmt = $data_servant->pdo->prepare("UPDATE hobb_people SET profile_pic = ? WHERE email = ?");
		if ($stmt->execute(array($new_photo['name'], $email))) {
			# go to settings page
			header('location: ../profile.php');
		}
	} else {
		header('location: ../profile.php');
	}
} else {
	header('location: ../profile.php');
}


?>