<?php
require 'data.php';

if (isset($_GET['email'])) {
	$feedback = '0';
	$email = htmlspecialchars(trim($_GET['email']));
	if ($email != "") {
		$sql = "UPDATE hobb_people SET page_image = '' WHERE email = ?";
		$statement = $data_servant->pdo->prepare($sql);
		if ($statement->execute(array($email))) {
			$feedback = '1';
		}
	}
	print($feedback);
}
?>