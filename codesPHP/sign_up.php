<?php
require 'data.php';
require("../PHPMailer/class.phpmailer.php");

$mail = new PHPMailer();

$mail->IsSMTP();                                      // set mailer to use SMTP
$mail->Host = "mail.bitsysnethosting.com";  // specify main and backup server
$mail->SMTPAuth = true;     // turn on SMTP authentication
$mail->Username = "admin@hobbafrica.com";  // SMTP username
$mail->Password = "hobb001"; // SMTP password

$mail->From = "admin@hobbafrica.com";
$mail->FromName = "Hobb Inc. - Admin";

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Hobb</title>
	<meta charset="utf-8">
	<link rel="shortcut icon" type="image/x-icon" href="../icons/hobb-icon.png" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>
	<div>
<?php
if (isset($_POST['sign_up'])) {
	try {
		# authenticate
	    $firstname = filter_var($_POST['firstname'], FILTER_SANITIZE_STRING);
	    $lastname = filter_var($_POST['lastname'], FILTER_SANITIZE_STRING);
	    $gender = filter_var($_POST['gender'], FILTER_SANITIZE_STRING);
	    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
	    $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
	    $country = filter_var($_POST['country'], FILTER_SANITIZE_STRING);
	    $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
	    $profilePic = "default_profile_pic.jpg";
	    $today = date('Y-m-d h:i:s');

	    // check that no field is empty
	    $valid = ((!empty($firstname)) && (!empty($lastname)) && (!empty($gender)) && (!empty($email)) && (!empty($phone)) && (!empty($country)) && (!empty($password)));
	    if ($valid && strlen($firstname) > 0 && strlen($lastname) > 0 && strlen($phone) > 0 && strlen($email) > 0 && strlen($password) > 0) {

	    	# get emails and phone numbers to check if this has been used
		      $statement = $data_servant->pdo->prepare("SELECT phone, email FROM hobb_people");
		      $usedPhoneEmail = false;
		      if ($statement->execute()) {
		        while ($phone_email = $statement->fetch()) {
		          if ($phone_email['phone'] == $phone || $phone_email['email'] == $email) {
		            $usedPhoneEmail = true;
		          }
		        }
		      }

		      if (!$usedPhoneEmail) {
	        	# insert into confirm_reg
	        	$stmt2 = $data_servant->pdo->prepare("INSERT INTO confirm_reg VALUES (?, ?, ?)");
	        	$stmt2->execute(array('', $email, $code));
				
				# send email to user email
	        	$code = rand(0, 99999);
	        	$message = "<html><head><title>Hobb</title></head><body>
				<div style='background-color: #1abc9c;font-size:1.5em;font-weight:bold;padding:10px;'>
				<div>We are glad to have you on Hobb, your page creation is just a click away.<br>
					Click on the link below to complete your registration on Hobb.<br>
					
					<a href='http://www.hobbafrica.com/complete.php?code=".$code."&u=".$email."'>continue</a>
					
					<br><br>Thank you.<br><br>Yours sincerely, <br>Hobb Inc.
				</div>
				</div>
				</body></html>";

				$mail->AddAddress($email, $firstname." ".$lastname);
				$mail->AddReplyTo("admin@hobbafrica.com", "Information");
				$mail->WordWrap = 50; // set word wrap to 50 characters

				$mail->Subject = "Sign up completion";
				$mail->Body    = $message;
				$mail->AltBody = $message;
				$mail->IsHTML(true); // set email format to HTML

				if ($mail->Send()) {
	        		# mail was sent successfully
					// set a cookie
					setcookie('user', $email, time()+60*60*24*7, '/');

					$sql = "INSERT INTO `hobb_people` (`firstname`, `lastname`, `gender`, `email`, `phone`, `country`, `profile_pic`, `date_of_sign_up`, `last_update`) VALUES ('".$firstname."', '".$lastname."', '".$gender."', '".$email."', '".$phone."', '".$country."', '".$profilePic."', '".$today."', '".$today."')";
	        
			        $data_servant->pdo->quote($sql);
					$stmt = $data_servant->pdo->prepare($sql);
					if ($stmt->execute()) {
						# insert into access log
						$sql = "INSERT INTO `access_log` (`a_username`, `a_password`, `last_log_in`, `log_out_time`, `is_online`) VALUES ('".$email."','".md5($password)."','".$today."','".$today."',1)";

				        $data_servant->pdo->quote($sql);
				        $stmt1 = $data_servant->pdo->prepare($sql);
				        if ($stmt1->execute()) {
				        	# SIGN UP SUCCESS
							header('location: ../index.php?su=1&msg=SIGN UP WAS SUCCESSFUL! PROCEED TO YOUR EMAIL AND CLICK THE LINK TO COMPLETE REGISTRATION');
				        } else {
							# go to index page
							header('location: ../index.php?msg=DATA COULD NOT BE SAVED#signup');
						}
					} else {
				        # go to index page
				        header('location: ../index.php?msg=DATA COULD NOT BE SAVED#signup');
				    }
	        	} else {
	        		# goto
	        		header('location: ../index.php?msg=MAIL COULD NOT BE SENT. CHECK EMAIL AND TRY AGAIN!' . $mail->ErrorInfo);
	        	}
		      } else {
		        # go to index page
		        header('location: ../index.php?msg=EMAIL OR PHONE EXIST. USE A UNIQUE EMAIL AND PHONE NUMBER#signup');
		      }

	    } else {
	      # go to index page
	      header('location: ../index.php?msg=NO FIELD MUST REMAIN EMPTY#signup'.$firstname.$lastname.$phone.$email.$gender.$country);
	    }
	} catch (Exception $e) {
		print('Server Error: '. $e->getMessage());
	}
  }
?>
	</div>
</body>
</html>