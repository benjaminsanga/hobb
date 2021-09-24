<?php
require 'codesPHP/data.php';

# increment log out page clicks
# get current clicks
$s=$data_servant->pdo->prepare("SELECT clicks FROM pages WHERE page = 'how it works' LIMIT 1");
if ($s->execute()) {
  if ($r=$s->fetch()) {
    $st=$data_servant->pdo->prepare("UPDATE pages SET clicks = ".++$r['clicks']." WHERE page = 'how it works'");
    $st->execute();
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Theme Made By www.w3schools.com - No Copyright -->
  <title>Hobb</title>
  <meta charset="utf-8">
  <link rel="shortcut icon" type="image/x-icon" href="icons/hobb-icon.png" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="css/style.css">
  <script src="js/script.js"></script>
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
      <a class="navbar-brand" href=""><img src="icons/hobb-icon.png" width="35" height="35"></a>
    </div>
    <div class="collapse navbar-collapse navbar-right" id="myNavbar">
      <ul class="nav navbar-nav navbar-right list-inline">
        <li><a href='index.php' class="link">back</a></li>
        <li><a href='index.php#login' class="link">log in</a></li>
        <li><a href='index.php#signup' class="link">sign up</a></li>
        <li><a href='index.php#explore' class="link">explore</a></li>
      </ul>
    </div>
  </div>
</nav>
<br><br>
<!-- Third Container (Grid) -->
<div class="container-fluid bg-1 text-center">    
  <h1 class="margin">How It Works</h1>
  <div class="row">
    <div class="col-sm-2"></div>
    <div class="col-sm-8">
      <h3 class="margin">Create Account&nbsp;&nbsp;<img src="icons/point.png" width="20" height="20"></h3>
      <p class="margin">
        <img src="icons/point-mark.png" width="20" height="20">&nbsp;&nbsp;
        <a href="index.php#signup">Sign up</a> with Hobb Africa for an account and get involved in the revolution of appreciating businesses in Africa for a better economy. You can include company details to get a wider hobb for patronage.
      </p>
    </div>
    <div class="col-sm-2"></div>
  </div>

<div class="row">
  <div class="col-sm-2"></div>
  <div class="col-sm-8">
    <h3 class="margin">Share And Get Information&nbsp;&nbsp;<img src="icons/point.png" width="20" height="20"></h3>
    <p class="margin">
      <img src="icons/point-mark.png" width="20" height="20">&nbsp;&nbsp;
      Post to Hobb Africa what you do or have to offer in text, image or video formats and see what others have to offer. Recieve payment for your product/service through a one-on-one conversation with the owner of business.
    </p>
  </div>
  <div class="col-sm-2"></div>
</div>

<div class="row">
  <div class="col-sm-2"></div>
  <div class="col-sm-8">
    <h3 class="margin">Get More Market&nbsp;&nbsp;<img src="icons/point.png" width="20" height="20"></h3>
    <p class="margin">
      <img src="icons/point-mark.png" width="20" height="20">&nbsp;&nbsp;
      Each week a "Top 5" list is selected by most viewed post. Hobb Africa markets your products/service by sharing them on it's social media pages including but not limited to Facebook and Instagram for free.
    </p>
  </div>
  <div class="col-sm-2"></div>
</div>

  <p class="margin"><span class="text-danger">Note: </span>For businesses in <b>Africa</b> only.</p>

</div>


<?php require_once 'codesPHP/foot.php'; ?>
