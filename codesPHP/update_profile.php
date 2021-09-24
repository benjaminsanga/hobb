<?php
require 'data.php';

if (isset($_POST['update'])) {
	# get values
	$hobby = htmlspecialchars(trim($_POST['hobby']));
	$dob = htmlspecialchars(trim($_POST['dob']));
	$city = htmlspecialchars(trim($_POST['city']));
	$email = htmlspecialchars(trim($_POST['email']));

	$sql = "UPDATE hobb_people SET hobby = ?, dob = ?, city = ? WHERE email = ?";
	$statement = $data_servant->pdo->prepare($sql);
	if ($statement->execute(array($hobby, $dob, $city, $email))) {
		# go to
		header('location: ../settings.php');
	} else {
		#goto
		header('location: ../settings.php');
	}
} else {
	#goto
	header('location: ../settings.php');
}
?>