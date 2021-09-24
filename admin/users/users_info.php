<?php require '../../codesPHP/data.php'; ?>

<!DOCTYPE html>
<html>
<head>
	<title>Hobb - Admin</title>
	<meta charset="utf-8">
	<link rel="shortcut icon" type="image/x-icon" href="../../icons/hobb-icon.png" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

	<style type="text/css">
		body {
			background-color: #474e5d;
			font-weight: bold;
			color: grey;
		}
		h1 {
			font-size: 4em;
		}
		h2 {
			font-size: 3em;
		}
		p, table, tr, td {
			font-size: 1.1em;
		}
		.pages_clicks {
			width: 100%;
		}
	</style>
</head>
<body>

	<div class="container" id="main">
		<h1 class="margin">Hobb Users Data</h1>
		<hr>
		<div class="row text-center">
			
			<?php $today = date('Y-m-d'); ?>

			<div class="col-sm-4">
				<h2>Total Users: </h2>
				<?php

				$gen_users_stmt = $data_servant->pdo->prepare("SELECT count('p_id') AS total_number FROM hobb_people");
				$gen_users_stmt->execute();
				if ($total_number = $gen_users_stmt->fetch()) {
					printf("<h2 style='font-size:7em;'>%s</h2>", $total_number['total_number']);
				}

				?>	
			</div>
			<div class="col-sm-4" id="daily_users">
				<h2>Daily Sign Ups: </h2>
				<?php
				$daily_users_stmt = $data_servant->pdo->prepare("SELECT count('p_id') AS total_number FROM hobb_people WHERE date_of_sign_up LIKE '%{$today}%'");
				$daily_users_stmt->execute(array($today));
				if ($total_number = $daily_users_stmt->fetch()) {
					printf("<h2 style='font-size:7em;'>%s</h2>", $total_number['total_number']);
				}

				?>	
			</div>
			<div class="col-sm-4" id="daily_users">
				<h2>Daily Users: </h2>
				<?php
				$daily_users_stmt = $data_servant->pdo->prepare("SELECT count('a_id') AS total_number FROM access_log WHERE last_log_in LIKE '%{$today}%'");
				$daily_users_stmt->execute();
				if ($total_number = $daily_users_stmt->fetch()) {
					printf("<h2 style='font-size:7em;'>%s</h2>", $total_number['total_number']);
				}

				?>	
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12 text-center">
				<h3>Clicks per page</h3>

				<?php
				$clicks = array();
				$clicks_stmt = $data_servant->pdo->prepare("SELECT clicks FROM pages");
				$clicks_stmt->execute();
				while ($row = $clicks_stmt->fetch()) {
					$clicks[] = $row['clicks'];
				}
				?>

				<table class="table table-hover table-bordered text-center pages_clicks">
					<tbody>
						<tr>
							<td>Index</td>
							<td>Home</td>
							<td>Explore</td>
							<td>Profile</td>
							<td>See Profile</td>
							<td>Single post</td>
							<td>Settings</td>
							<td>How it works</td>
							<td>Log out</td>
							<td>Instagram</td>
							<td>Facebook</td>
							<td>Post</td>
							<td>About</td>
						</tr>
						<tr>
							<?php
							if (count($clicks) > 0) {
								$i=0;
								do {
									printf("<td>%s</td>", $clicks[$i]);
									$i++;
								} while ($i < count($clicks));
							}
							?>
						</tr>
					</tbody>
				</table>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12 text-center">
				<h3>Hobb Inc. &copy; <?php print(date('Y')); ?></h3>
			</div>
		</div>

	</div>
</body>
</html>


