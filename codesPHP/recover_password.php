<?php
require 'data.php';
require("../PHPMailer/class.phpmailer.php");

$mail = new PHPMailer(); // the mailer object
$email = filter_var($_GET['mail'], FILTER_SANITIZE_STRING); // email to send link to
$feedback = "0"; // the feedback to the caller
$message = "<html><head><title>Hobb</title></head><body>
				<div style='background-color: #1abc9c;font-size:1.5em;font-weight:bold;padding:10px;'>
				<div>Click on the link below to confirm your password recreation.<br>
					
					<a href='www.hobbafrica.com/codesPHP/confirm_new_password.php?m=".$email."'>continue</a>
					
					<br><br>Thank you.<br><br>Yours sincerely, <br>Hobb Inc.
				</div>
				</div>
				</body></html>"; // email content

$sql = "SELECT `email` FROM `hobb_people` WHERE `email` = ?";
$stmt = $data_servant->pdo->prepare($sql);
if ($stmt->execute(array($email))) {
	$found = false; // to check if email is found in database
	while ($row = $stmt->fetch()) {
		$found = $row['email'];
	}
	if ($found) {
		# email exists
		$mail->IsSMTP();                                      // set mailer to use SMTP
		$mail->Host = "mail.bitsysnethosting.com";  // specify main and backup server
		$mail->SMTPAuth = true;     // turn on SMTP authentication
		$mail->Username = "admin@hobbafrica.com";  // SMTP username
		$mail->Password = "hobb001"; // SMTP password

		$mail->From = "admin@hobbafrica.com";
		$mail->FromName = "Hobb Inc. - Admin";

		$mail->AddAddress($email);
		$mail->AddReplyTo("admin@hobbafrica.com", "Information");
		$mail->WordWrap = 50; // set word wrap to 50 characters

		$mail->Subject = "Password Re-creation";
		$mail->Body    = $message;
		$mail->AltBody = $message;
		$mail->IsHTML(true); // set email format to HTML

		if ($mail->Send()) {
			$feedback = "1";
		} else {
			$feedback = "email sending failed";
		}
	} else {
		$feedback = "not found";
	}
} else {
	$feedback = "not found";
}

print($feedback);
?>