<?php
require_once 'data.php';

$first = htmlspecialchars($_GET['first']);
$second = htmlspecialchars($_GET['second']);
$today = date('Y-m-d h:i:s');
$feedback = "0";

$stmt = $data_servant->pdo->prepare("INSERT INTO connected_people VALUES (?, ?, ?, ?)");
if ($stmt->execute(array('', $first, $second, $today))) {
	$feedback = "1";
}

echo($feedback);

?>