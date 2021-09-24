<?php
require 'codesPHP/data.php';
# increment log out page clicks
# get current clicks
$s=$data_servant->pdo->prepare("SELECT clicks FROM pages WHERE page = 'about' LIMIT 1");
if ($s->execute()) {
  if ($r=$s->fetch()) {
    $st=$data_servant->pdo->prepare("UPDATE pages SET clicks = ".++$r['clicks']." WHERE page = 'about'");
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
</head>
<body>

<style type="text/css">
  .link { color: #f2f2f2; }
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
      <a class="navbar-brand" href="home.php"><img src="icons/hobb-icon.png" width="35" height="35"></a>
    </div>
    <div class="collapse navbar-collapse navbar-right" id="myNavbar">
      <ul class="nav navbar-nav navbar-right list-inline">
        <li><a href='home.php' class="link" style="color: #f2f2f2;">home</a></li>
      </ul>
    </div>
  </div>
</nav>
<br><br>
<!-- Third Container (Grid) -->
<div class="container-fluid bg-1 text-left">    
  <div class="row">
    <div class="col-sm-1"></div>
    <div class="col-sm-10" style="font-size: 1.2em;font-weight: bold;line-height: 1.3em;">
      <div class="row">
        <h5 class="margin">About</h5>
        <div class="col-sm-12">
          <p>Hobb Africa Inc. is an internet company located in Jos-Nigeria.</p>

          <!--p>We provide a platform for everyone who has a business (of any scale) or follows up on some business (from any part of the world) to showcase/patronize such business on the go.</p-->

          <p>It is for creating awareness of businesses within a region and letting people to follow up on the businesses of their choice on the go.</p>

          <p>Hobb started in 2017 by Benjamin Taiba while he was considering the enterpreneurs, inventors, resources, businesses, innovations etc. in Africa and its locals. He learnt that people are ignorant or oblivious about the resources or goods and services next door and therefore go even outside the Africa continent to get them. It became clear that we have things we don't know we do and we suffer for the ignorance.</p>

          <p>Hobb runs in Africa only. Except those who "have a look" at the platform without Africa. Expansion is possible in the future to other parts of the world.</p>
        </div>
      </div>
      <div class="row">
        <h3 class="margin">Our Team</h3>
        <div class="col-sm-4">
          <p>Benjamin Taiba</p>
          <p>CEO, Hobb Inc.</p>
        </div>
        <div class="col-sm-4"></div>
      </div>
      <br><br>
      <hr>
      <div>
        <p class="margin"><span class="text-danger">Note: </span>For businesses in <b>Africa</b> only.</p>
      </div>
    </div>
    <div class="col-sm-1"></div>
  </div>

</div>


<?php require_once 'codesPHP/foot.php'; ?>
