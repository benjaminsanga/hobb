<?php
require 'data.php';

if (isset($_POST['change_page_image'])) {
	# get values
	$email = htmlspecialchars(trim($_POST['email']));
	$newImage = $_FILES['new_page_image'];

	if (move_uploaded_file($newImage['tmp_name'], "../background_images/".$newImage['name'])) {
		# insert into table
		$sql = "UPDATE hobb_people SET page_image = ? WHERE email = ? LIMIT 1";
		$statement = $data_servant->pdo->prepare($sql);
		if ($statement->execute(array($newImage['name'], $email))) {
			# go to
			header('location: ../settings.php?m=success');
		} else {
			# go to
			header('location: ../settings.php?m=saving failed');
		}
	} else {
		# go to
		header('location: ../settings.php?m=file not moved');
	}
} else {
	# go to
	header('location: ../settings.phpm=form not submitted');
}

?>