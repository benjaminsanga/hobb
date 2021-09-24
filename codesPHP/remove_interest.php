<?php
require 'data.php';

$interest = filter_var($_GET['interest'], FILTER_SANITIZE_STRING);
$user = filter_var($_GET['user'], FILTER_SANITIZE_STRING);
$feedback = '0';

$sql = "DELETE FROM `interests` WHERE `username` = '{$user}' AND `interested_in` = '{$interest}' LIMIT 1";
$stmt = $data_servant->pdo->prepare($sql);
if ($stmt->execute()) {
	# deletion success
	$feedback = '1';
}

print($feedback);

?>