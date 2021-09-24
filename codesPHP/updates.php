<?php
require 'data.php';

$userId = htmlspecialchars(trim($_GET['userId']));
$feedback = "";

$sql = "SELECT second_username FROM connected_people WHERE first_username = ?";
$stmt = $data_servant->pdo->prepare($sql);
if ($stmt->execute(array($userId))) {
	while ($friends = $stmt->fetch()) {
		$sql = "SELECT post_id, post_text FROM posts WHERE p_username = ?";
		$statement = $data_servant->pdo->prepare($sql);
		if ($statement->execute(array($friends['second_username']))) {
			while ($postText = $statement->fetch()) {
				if ($postText['post_text'] != "") {
					# get only non empty fields
					$feedback .= "<a href='single_post.php?p_id=".$postText['post_id']."'>";
					$feedback .= "<p class='feed'><img src='icons/bullet.png' width='10' height='10' > ".substr($postText['post_text'], 0, 40)."</p><a>";
				}
			}
		}
	}
}

print($feedback);

?>