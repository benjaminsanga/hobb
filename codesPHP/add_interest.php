<?php 
require_once 'data.php';

$reply = '0';
$user = htmlspecialchars($_GET['user']);
$areaOfInterest = htmlspecialchars($_GET['aoi']);

$sql = "INSERT INTO interests (id, username, interested_in) VALUES (?, ?, ?)";
$statement = $data_servant->pdo->prepare($sql);
if ($statement->execute(array('', $user, $areaOfInterest))) {
	# send back 1
	$reply = '1';
}

echo($reply);
?>