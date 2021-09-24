<?php
require_once 'data.php';

$postId = htmlspecialchars($_GET['postId']);
$likedBy = htmlspecialchars($_GET['likedBy']);
$today = date('Y-m-d h:i:s');
$success = "0";

$sql = "INSERT INTO post_reactions VALUES (?, ?, ?, ?)";
$stmt = $data_servant->pdo->prepare($sql);
if($stmt->execute(array('', $postId, $likedBy, $today))) {
	$success = "1";
}

echo($success);

?>