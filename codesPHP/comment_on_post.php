<?php
require_once 'data.php';

$post_id = htmlspecialchars($_GET['postId']);
$post_id = (int)$post_id;
$username = htmlspecialchars($_GET['username']);
$comment = htmlspecialchars($_GET['comment']);
$today = date('Y-m-d h:i:s');
$feedback = false;

$sql = "INSERT INTO post_comments VALUES (?, ?, ?, ?, ?)";
$statement = $data_servant->pdo->prepare($sql);
if ($statement->execute(array('', $post_id, $username, $comment, $today))) {
	$feedback = true;
}

print($feedback);


/*$stmt = $data_servant->pdo->prepare("SELECT comment FROM post_comments WHERE post_id = ? LIMIT 2 ORDER BY p_id DESC");
	$stmt->execute(array($post_id));

	$feedback = "<div class='col-sm-12'>";

	while ($comment = $stmt->fetch()) {	
        $feedback .= "<p>Mercy: mercy's comment</p>
              <p>Paul: another comment</p>
	            ";
	}
	$feedback .= "<input style='width: 80%;font-size: 15px;' type='text' name='comment' placeholder='say something about this post...'><button onclick='commentOnPost('commenting-{$postId}', '{$username}')' class='btn btn-default btn-sm' id='comment'>Comment</button></div>";*/
?>

