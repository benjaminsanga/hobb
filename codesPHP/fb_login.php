<?php 
require 'data.php'; 
?>
<!DOCTYPE html>
<html>
<head>
	<title>Hobb</title>
</head>
<body>
<?php
// check for facebook cookie
$user = filter_var($_GET['fb_user'], FILTER_SANITIZE_STRING);
setcookie('fb_user', $user, time()+60*60*24*7, '/');
if (!empty($_COOKIE['fb_user'])) {
	$name = $_COOKIE['fb_user'];
	$today = date('Y-m-d h:i:s');

	$stmt = $data_servant->pdo->prepare("SELECT * FROM facebook_logins WHERE name = ?");
	if ($stmt->execute(array($name))) {
		# goto
		header('location: ../home.php');
	} else {
		# insert into facebook_logins table in database
		$stmt = $data_servant->pdo->prepare("INSERT INTO facebook_logins VALUES (?, ?, ?)");
		if ($stmt->execute(array('', $name, $today))) {
			# goto
			header('location: ../home.php');
		}
	}
} else {
	//header('location: ../index.php?msg=Sorry! Facebook Login Error. Please Try Again');
}
?>
</body>
</html>