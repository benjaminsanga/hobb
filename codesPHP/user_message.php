<?php
/*
* GET user message and package it for sending to hobb email
*/
require 'data.php';
require("../PHPMailer/class.phpmailer.php");

$message = filter_var($_GET['message'], FILTER_SANITIZE_STRING);
$mail = new PHPMailer(); // the mailer object
$feedback = "0"; // the feedback to the caller
$mailContent = "<html><head><title>Hobb</title></head><body>
				<div style='background-color: #1abc9c;font-size:1.5em;font-weight:bold;padding:10px;'>
				<div>
				<p>
					Message from user:<br>".$message."
				</p>
				</div>
				</div>
				</body></html>"; // email content

if ($message != "") {
	# send message to hobb email
	$mail->IsSMTP();                                      // set mailer to use SMTP
	$mail->Host = "mail.bitsysnethosting.com";  // specify main and backup server
	$mail->SMTPAuth = true;     // turn on SMTP authentication
	$mail->Username = "admin@hobbafrica.com";  // SMTP username
	$mail->Password = "hobb001"; // SMTP password

	$mail->From = "admin@hobbafrica.com";
	$mail->FromName = "Hobb - User Message";

	$mail->AddAddress("hobbincorporation@gmail.com");
	$mail->AddReplyTo("hobbincorporation@gmail.com", "User Information");
	$mail->WordWrap = 50; // set word wrap to 50 characters

	$mail->Subject = "Message From Hobb User";
	$mail->Body    = $mailContent;
	$mail->AltBody = $mailContent;
	$mail->IsHTML(true); // set email format to HTML

	if ($mail->Send()) {
		$feedback = "1";
	} else {
		$feedback = "email sending failed";
	}

	print($feedback);
}

?>