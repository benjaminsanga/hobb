<?php
require 'codesPHP/data.php';

if (isset($_GET['code']) && isset($_GET['u'])) {
	# get code
	$code = filter_var($_GET['code'], FILTER_SANITIZE_STRING);
	$email = filter_var($_GET['u'], FILTER_SANITIZE_STRING);

	setcookie('user', $email, time()+60*60*24*7, '/');

	# confirm email and code
	$stmt = $data_servant->pdo->prepare("SELECT email, code FROM confirm_reg WHERE code = ? LIMIT 1");
	if ($stmt->execute(array($code))) {
		$stmt = null;
		$stmt = $data_servant->pdo->prepare("DELETE FROM confirm_reg WHERE code = ? LIMIT 1");
		$stmt->execute(array($code));
		
		# goto
		header('location: home.php');
	}
}
?>