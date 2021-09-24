<?php
require_once 'data.php';

if (isset($_POST['new_post_submit'])) {
	# increment log out page clicks
	# get current clicks
	$s=$data_servant->pdo->prepare("SELECT clicks FROM pages WHERE page = 'post' LIMIT 1");
	if ($s->execute()) {
	  if ($r=$s->fetch()) {
	    $st=$data_servant->pdo->prepare("UPDATE pages SET clicks = ".++$r['clicks']." WHERE page = 'post'");
	    $st->execute();
	  }
	}
	
	# a post attempt
	$username = htmlspecialchars($_POST['username']); // user username
	$writeUp = htmlspecialchars($_POST['write_up']); // post write up
	$file = $_FILES['file_content']; // post file type if any
	$location = htmlspecialchars($_POST['location']); // location
	$success = false; // check if upload success	
	$fileType = $file['type'];
	$today = date('Y-m-d h:i:s');

	$valid = ((!empty($write_up) || !empty($file) && (strlen($writeUp)>0) || strlen($file['name'])>0));

	if ($valid) {
		if(!empty($file) && $file['name'] != ""):
			if (strpos($fileType, "video") > -1) {
				#move to videos folder
				if (move_uploaded_file($file['tmp_name'], "../videos/".$file['name'])) {
					# store values into database
					$table = "posts";
					$fields = array("p_username", "post_text", "post_video", "time_of_post", "location");
					$values = array($username, $writeUp, $file['name'], $today, $location);
					if ($data_servant->insert($table, $fields, $values)) {
						# go to home page
						header('location: ../home.php?msg=failed post');
					} else {
						# go to home page with failed upload message
						header('location: ../home.php');
					}
				}
			} elseif (strpos($fileType, "image") > -1) {
				# move to images folder
				if (move_uploaded_file($file['tmp_name'], "../images/".$file['name'])) {
					# store values into database
					$table = "posts";
					$fields = array("p_username", "post_text", "post_photo", "time_of_post", "location");
					$values = array($username, $writeUp, $file['name'], $today, $location);
					if ($data_servant->insert($table, $fields, $values)) {
						# go to home page
						header('location: ../home.php?msg=failed post');
					} else {
						# go to home page with failed upload message
						header('location: ../home.php');
					}
				}
			}
		endif; // for videos or photos

		if(!empty($writeUp) && ($writeUp != "") && ($file['name'] == "")):
			$table = "posts";
			$fields = array("p_username", "post_text", "time_of_post", "location");
			$values = array($username, $writeUp, $today, $location);
			if ($data_servant->insert($table, $fields, $values)) {
				# go to home page
				header('location: ../home.php?msg=failed post');
			} else {
				# go to home page with failed upload message
				header('location: ../home.php');
			}
		endif; // for text only
	} else {
		# go back if not valid
		header('location: ../home.php?msg=Post Cannot Be Empty');
	}

	
}

/*$uploads_dir = '/uploads';
foreach ($_FILES["pictures"]["error"] as $key => $error) {
    if ($error == UPLOAD_ERR_OK) {
        $tmp_name = $_FILES["pictures"]["tmp_name"][$key];
        // basename() may prevent filesystem traversal attacks;
        // further validation/sanitation of the filename may be appropriate
        $name = basename($_FILES["pictures"]["name"][$key]);
        move_uploaded_file($tmp_name, "$uploads_dir/$name");
    }
}*/

?>