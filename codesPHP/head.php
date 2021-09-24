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
  <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet" type="text/css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="css/style.css">
  <script src="js/script.js"></script>
  
  <!--FETCH UPDATES FROM DATABASE AND UPDATE NOTY FEED DIV-->
  <script type="text/javascript">
    function updates() {
      userId = document.getElementById('userpass').value;
      agent = new XMLHttpRequest();

      if (agent) {
        var ds = "codesPHP/updates.php?userId="+userId; // data source
        agent.open("GET", ds, true);
        
        agent.onreadystatechange = function(){
          if(agent.readyState == 4 && agent.status == 200){
            //alert(agent.responseText);
            document.getElementById('noty-feed').innerHTML = agent.responseText;
          }
        }
      }

      agent.send(null);
      // callback
      setTimeout(function(){updates()}, 100);
    }

    // change icon color when icon is in
    function changeIcon(thisIcon) {
        //alert(thisIcon);
        if (thisIcon == "home") {
          document.getElementById(thisIcon).src = "icons/home-icon-hover.png";
        } else if (thisIcon == "post") {
          document.getElementById(thisIcon).src = "icons/post-icon-hover.png";
        } else if (thisIcon == "explore") {
          document.getElementById(thisIcon).src = "icons/explore-icon-hover.png";
        }
      }

      // change icon color when mouse is out
      function changeIconHover(thisIcon) {
        //alert(thisIcon);
        if (thisIcon == "home") {
          document.getElementById(thisIcon).src = "icons/home-icon.png";
        } else if (thisIcon == "post") {
          document.getElementById(thisIcon).src = "icons/post-icon.png";
        } else if (thisIcon == "explore") {
          document.getElementById(thisIcon).src = "icons/explore-icon.png";
        }
      }
  </script>
</head>
<body onload="updates()" onkeypress="escape(event)">

<!--FACEBOOK LOG IN-->
<div id="fb-root"></div>
<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '1156550904475413',
      cookie     : true,
      xfbml      : true,
      version    : 'v2.10'
    });
      
    FB.AppEvents.logPageView();   
      
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "https://connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));


  function statusChangeCallback(response) {
  	if (response.status == 'connected') {
  		// get user and app details
      var uid = response.authResponse.userID;
  		var accessToken = response.authResponse.accessToken;
      // set expiry date for cookie
      var d = new Date();
      d.setTime(d.getTime() + (7 * 24 * 60 * 60 * 1000));
      var expires = "expires="+d.toUTCString();

  		document.cookie = "at="+accessToken+"; "+ expires +"; path=/";

  		FB.login(function(response) {
  			if (response.authResponse) {
  				token = response.authResponse.access_token;
  				 console.log('Welcome!  Fetching your information.... ');

  				 FB.api('/me', 'get', { access_token: token, fields: 'id,name,gender,picture' }, function(response) {

  				 	document.cookie = "fb_user="+response.name+"; "+ expires +"; path=/";
  				 	document.cookie = "pc="+response.picture.data.url+"; "+ expires +"; path=/";
  				 	
  				  console.log(response);

            // go to facebook login page
            window.location.href = "codesPHP/fb_login.php?"+"fb_user="+response.name;
  				});

  			} else {
  				console.log('User cancelled login or did not fully authorize.');
  			}
  		});
  		
  	} else if (response.status === 'not_authorized') {
  		// the user is logged in to Facebook, 
  		// but has not authenticated your app
      window.location.href = "index.php?msg=User did not authorize Hobb";
  	} else {
  		// the user isn't logged in to Facebook.
      window.location.href = "index.php?msg=User is not logged into Facebook";
  	}
  }

function checkLoginState() {
  FB.getLoginStatus(function(response) {
    statusChangeCallback(response);
  });
}
function displayPasswordRecoveryDiv() {
  var div = document.getElementById('password_recovery').style;
  div.display = 'block';
}
function submitRecoveryMail() {
  email = document.getElementById('recovery_mail').value;
  if (email.length == 0) { return; }
  var agent = new XMLHttpRequest();
    
    if (agent) {
      var ds = "codesPHP/recover_password.php?mail="+email; // data source
      agent.open("GET", ds, true);

      agent.onreadystatechange = function(){
        if(agent.readyState == 4 && agent.status == 200){
          if (agent.responseText == '1') {
            document.getElementById("recovery_content").innerHTML = "<div class='well'>Email sent! Confirm from account.</div>";
          } else if(agent.responseText == "not found") {
            window.location.href = "index.php?msg=Hobb don't recognize this email address.";
          } else if(agent.responseText == "email sending failed"){
            window.location.href = "index.php?msg=Hobb couldn't send email to this address.";
          }
        }
      }
    }
    agent.send(null);
}
</script>


<?php
$pdo = new PDO('mysql:host=localhost;dbname=sangaclou_hobb_', 'sangaclou_hobb_', 'bentaibahobb'); # sangaclou_hobb_, bentaibahobb
if (isset($_COOKIE['user'])) {
	$username = $_COOKIE['user'];
	$statement = $pdo->prepare("SELECT page_image FROM hobb_people WHERE email = ? LIMIT 1");
	if ($statement->execute(array($username))) {
		while ($image = $statement->fetch()) {
		?>
		<style type="text/css">
		  body {
		    background-image: url('<?php print("background_images/".$image['page_image']); ?>');
		    background-repeat: no-repeat;
		    background-attachment: fixed;
		  }
		</style>
		<?php
		}
	}
}
?>

<style type="text/css">
  .link { color: #474e5d; font-weight: bold; }
  #header {
    background: #1abc9c; /* For browsers that do not support gradients */
    background: -webkit-linear-gradient(#1abc9c, white); /* For Safari 5.1 to 6.0 */
    background: -o-linear-gradient(#1abc9c, white); /* For Opera 11.1 to 12.0 */
    background: -moz-linear-gradient(#1abc9c, white); /* For Firefox 3.6 to 15 */
    background: linear-gradient(#1abc9c, white); /* Standard syntax */
    border-bottom: 1px solid #1abc9c;
  }
  #search-result {
    display: none;
    border: 1px solid green;
    padding: 10px;
  }
</style>

<?php

$current_url = htmlspecialchars($_SERVER['PHP_SELF']); // current page

$ifIndex = (strpos($current_url, "index") > -1); // check if its index page

// Get initial variables
$index_links = array();
$main_links = array();

$allowedURL = ((strpos($current_url, "home") > -1) || (strpos($current_url, "notifications") > -1) || (strpos($current_url, "explore") > -1) || (strpos($current_url, "profile") > -1) || (strpos($current_url, "settings") > -1) || (strpos($current_url, "single_post") > -1) || (strpos($current_url, "profile") > -1));

if (strpos($current_url, "index") > -1) {
  // links for index page
  $index_links = array('how it works' => 'how_it_works', 'log in' => '#login', 'sign up' => '#signup', 'explore' => '#explore');
} elseif ($allowedURL) {
  // links for other pages aside index page
  $main_links = array('home' => 'icons/home-icon.png', 'post' => 'icons/post-icon.png', 'explore' => 'icons/explore-icon.png');
}

?>

<!-- Navbar -->
<nav class="navbar navbar-default navbar-fixed-top" id="header" style="padding: 5px;">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>                        
      </button>
      <a class="navbar-brand" href="">
        <img src="icons/hobb-icon.png" width="35" height="35">
      </a>
    </div>
    <div class="collapse navbar-collapse <?php echo !($ifIndex) ? "navbar-left" : "navbar-right"; ?>" style="margin-left: 10%;" id="myNavbar">
      <ul class="nav navbar-nav navbar-left list-inline">
        <?php

          # if there's a variable in the link, get it with substr() and attach it to the link

          if (!empty($index_links)) {
            $output = "";
            foreach ($index_links as $alias => $url) {
              if ($url == "how_it_works") {
                $output .= "<li><a href='". $url .".php' class='link'>". $alias ."</a></li>";
              } else {
                $output .= "<li><a href='". $url ."' class='link'>". $alias ."</a></li>";
              }
              
            }
            echo($output);
          } elseif (!empty($main_links)) {
            $output = "";
            foreach ($main_links as $url => $icon) {
              
              if ($url == 'post') {
                // make a drop div for it for uploading
                $output .= "<li><a href='#' onclick='show_hide_postbox()' title='{$url}'><img src='". $icon ."'  id='".$url."' width=25 height=25 onmouseenter='changeIcon(\"{$url}\")' onmouseout='changeIconHover(\"{$url}\")'></a></li>";
              } else {
                $output .= "<li><a href='". $url .".php' title='{$url}'><img src='". $icon ."'  id='".$url."' width=25 height=25 onmouseenter='changeIcon(\"{$url}\")' onmouseout='changeIconHover(\"{$url}\")'></a></li>";
              }
            }
              
            echo($output);
          }
        ?>
      </ul>
    </div>
    <?php
      if (!$ifIndex) {
    ?>
    <div class="navbar-right">
      <div class="btn-group input-group">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
        <span class="caret"></span></button>
        <ul class="dropdown-menu" role="menu">
          <li><a href="index.php?logout=1">Log out</a></li>
          <?php
          if (!empty($_COOKIE['user'])) { ?><li><a href="settings.php">Settings</a></li><?php } ?>
        </ul>
      </div>
    </div>
    <div class="navbar-right visible-lg" style="width: 400px;" id="search">
      <div class="input-group">
        <input type="text" class="form-control" id="search-box" placeholder="Search Hobb" name="search" onkeyup="searchFor()" autocomplete="off" value="<?php print(isset($_COOKIE['search']) ? $_COOKIE['search'] : ""); ?>" style="border-radius: 5px;">
      </div>
    </div>

    <?php
      } // end of ifIndex
    ?>
  </div>
</nav>

<div class="thumbnail" id="search-result">
            
</div>

<script type="text/javascript">
  function searchFor() { 
    // get search text
    text = document.getElementById('search-box').value;
    if (text.length==0) {
      resultDiv.style.display = 'none';
      return;
    }
    // get result div
    resultDiv = document.getElementById('search-result');

    //get search box position
    var searchDivPos = document.getElementById('search-box').getBoundingClientRect();

    var agent = new XMLHttpRequest();
  
    if (agent) {
      var ds = "codesPHP/search_for.php?text="+text; // data source
      agent.open("GET", ds, true);
      
      agent.onreadystatechange = function(){
        if(agent.readyState == 4 && agent.status == 200){
          if (agent.responseText != "") { 
            resultDiv.style.zIndex = 100; 
            resultDiv.style.position = 'absolute';
            resultDiv.style.left = (searchDivPos.left) + 'px';
            resultDiv.style.top = (searchDivPos.top+70) + 'px';
            resultDiv.style.width = document.getElementById('search').style.width;
            resultDiv.innerHTML = agent.responseText;
            resultDiv.style.display = 'block';
          }
        }
      }
    }
    agent.send(null);
  }
</script>

