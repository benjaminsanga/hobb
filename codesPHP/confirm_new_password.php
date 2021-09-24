<?php
require 'data.php';

# increment log out page clicks
# get current clicks
$s=$data_servant->pdo->prepare("SELECT clicks FROM pages WHERE page = 'password recovery' LIMIT 1");
if ($s->execute()) {
  if ($r=$s->fetch()) {
    $st=$data_servant->pdo->prepare("UPDATE pages SET clicks = ".++$r['clicks']." WHERE page = 'password recovery'");
    $st->execute();
  }
}

$m = filter_var($_GET['m'], FILTER_SANITIZE_STRING);
setcookie('new_password_mail', $m, time()+60*60*24, '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Theme Made By www.w3schools.com - No Copyright -->
  <title>Hobb - Password Recovery</title>
  <meta charset="utf-8">
  <link rel="shortcut icon" type="image/x-icon" href="../icons/hobb-icon.png" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="../css/style.css">
  <script src="../js/script.js"></script>
</head>
<body>

<style type="text/css">
  .link { color: #f2f2f2; font-weight: bold; }
  #header {
    background: #1abc9c; /* For browsers that do not support gradients */
    background: -webkit-linear-gradient(#1abc9c, #f2f2f2); /* For Safari 5.1 to 6.0 */
    background: -o-linear-gradient(#1abc9c, #f2f2f2); /* For Opera 11.1 to 12.0 */
    background: -moz-linear-gradient(#1abc9c, #f2f2f2); /* For Firefox 3.6 to 15 */
    background: linear-gradient(#1abc9c, #f2f2f2); /* Standard syntax */
    border-bottom: 1px solid #1abc9c;
  }

</style>

<!-- Navbar -->
<nav class="navbar navbar-default navbar-fixed-top" id="header" style="padding: 5px;">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>                        
      </button>
      <a class="navbar-brand" href=""><img src="../icons/hobb-icon.png" width="35" height="35"></a>
    </div>
    <div class="collapse navbar-collapse navbar-right" id="myNavbar">
      <ul class="nav navbar-nav navbar-right list-inline">
        <li><a href='../index.php' class="link">cancel</a></li>
      </ul>
    </div>
  </div>
</nav>
<br><br>
<!-- Third Container (Grid) -->
<div class="container-fluid bg-1 text-center">    

  <?php
  if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isset($_POST['submit_new_password'])) {
      # get new password
      $email = filter_var($_COOKIE['new_password_mail'], FILTER_SANITIZE_STRING);
      $password = filter_var($_POST['new_password'], FILTER_SANITIZE_STRING);
      $password = md5($password);

      if (!empty($password)) {
        $sql = "UPDATE access_log SET a_password = ? WHERE a_username = ?";
        $stmt = $data_servant->pdo->prepare($sql);
        if ($stmt->execute(array($password, $email))) {
          # goto index page and log in
          header('location: ../index.php?msg=Password Re-creation Successful! Log In With New Password#login');
        }
      }
    }
  }
  ?>


  <h1 class="margin">Password Re-creation</h1>
  <div class="row">
    <div class="col-sm-4"></div>
    <div class="col-sm-4">
      <p class="margin">Type in a new password</p>
      <form action="" method="POST">
        <div class="input-group">
          <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
          <input id="new_password" type="text" class="form-control" name="new_password" placeholder="New Passowrd" >
        </div> 
        <div class="form-group">        
          <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" name="submit_new_password" class="btn btn-success">Submit New Password</button>
          </div>
        </div>
      </form>
    </div>
    <div class="col-sm-4"></div>
  </div>

</div>

<?php require_once '../js/script.php'; ?>

<?php
if (isset($_GET['msg'])) {
?>
<script type="text/javascript">
  window.onclick = function() {
    document.getElementById('msg-display').style.display = "none";
  }
  document.getElementById('msg-display').style.display = "block";
</script>
<?php
}
?>

<!-- Footer -->
<footer class="container-fluid bg-4" style="padding: 20px;color: white;">
  <div class="row">
    <div class="col-xs-6 text-left">
      <p style="font-size: 12px;">
        <a href="about.php" style="color: white;">About</a>&nbsp;&nbsp;
        <!--a href="" style="color: white;">Contact</a-->
      </p>
    </div>
  <div class="col-xs-6 text-right">
    <a target="_blank" href="https://www.facebook.com/hobbafrica/" >
      <img src="../icons/facebook.png" style="padding:5px;width: 30px;height: 30px;"></a>
    <a target="_blank" href="https://www.instagram.com/hobbafrica/">
      <img src="../icons/instagram.png" style="padding:5px;width: 30px;height: 30px;"></a>
    <span style="font-size: 0.7em;">Hobb Inc. <?php print(date('Y')); ?></span>
  </div>
  </div>
</footer>

</body>
</html>
