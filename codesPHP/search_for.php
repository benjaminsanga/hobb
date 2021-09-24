<?php
require 'data.php';

$str = filter_var($_GET['text'], FILTER_SANITIZE_SPECIAL_CHARS);
$text = filter_var($str, FILTER_SANITIZE_STRING);
$feedback = "";

if (strlen($text)<0) {
	return;
}

$sql = "SELECT firstname, lastname, email FROM hobb_people WHERE firstname LIKE '%{$text}%' OR lastname LIKE '%{$text}%'";
$statement = $data_servant->pdo->prepare($sql);

if ($statement->execute()) {
	$feedback .= "People<br>";

	while ($names = $statement->fetch()) {
		$feedback .= "<a href='see_profile.php?user=". $names['email'] ."'><p>". $names['firstname'] ." ". $names['lastname'] ."</p></a>";
	}
}

$sql = "SELECT post_text, post_id FROM posts WHERE post_text LIKE '%{$text}%'";
$statement1 = $data_servant->pdo->prepare($sql);
if ($statement1->execute()) {
	$feedback .= "<hr>Posts<br>";

	while ($post = $statement1->fetch()) {
		$feedback .= "<a href='single_post.php?p_id=".$post['post_id']."'><p>". substr($post['post_text'], 0, 35) ."</p></a>";
	}

}

# set a cookie
setcookie('search', $text, time()+10, "/");

print($feedback);
?>