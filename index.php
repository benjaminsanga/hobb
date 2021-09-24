<?php
require_once 'codesPHP/data.php'; 

$today = date('Y-m-d h:i:s');

if (isset($_GET['logout']) && isset($_COOKIE['user']) && !empty($_COOKIE['user'])) {  
  # set log out time
  $stmt = $data_servant->pdo->prepare("UPDATE `access_log` SET `log_out_time` = ?, `is_online` = ? WHERE `a_username` = ?");
  $stmt->execute(array($today, 0, $_COOKIE['user']));

  # increment log out page clicks
  # get current clicks
  $s=$data_servant->pdo->prepare("SELECT clicks FROM pages WHERE page = 'log out' LIMIT 1");
  if ($s->execute()) {
    if ($r=$s->fetch()) {
      $st=$data_servant->pdo->prepare("UPDATE pages SET clicks = ".++$r['clicks']." WHERE page = 'log out'");
      $st->execute();
    }
  }

  # unset cookies
  setcookie('user', "", time()-60*60*24, '/');
} elseif (isset($_COOKIE['fb_user'])) {
  # unset cookies
  setcookie('fb_user', "", time()-60*60*24, '/');
} else {
  # increment log out page clicks
  # get current clicks
  $s=$data_servant->pdo->prepare("SELECT clicks FROM pages WHERE page = 'index' LIMIT 1");
  if ($s->execute()) {
    if ($r=$s->fetch()) {
      $st=$data_servant->pdo->prepare("UPDATE pages SET clicks = ".++$r['clicks']." WHERE page = 'index'");
      $st->execute();
    }
  }
}

// if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  # if trying to log in
  if (isset($_POST['log_in'])) {
    # authenticate
    $username = filter_var($_POST['username'], FILTER_VALIDATE_EMAIL);
    $password = filter_var($_POST['password'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $valid = ($username != "" && $password != "");

    if (!$valid) {
      # go to index page
      header('location: index.php?msg=Invalid');
    }
    // encrypt password
    $password = md5($password);

    $params = array("a_username", "a_password");
    $table = "access_log";
    $condition = "a_username='".$username."' AND a_password='".$password."' LIMIT 1";

    $feedback = $data_servant->select($params, $table, $condition);

    if (($feedback[0][0] === $username) && ($feedback[0][1] === $password)) {
      # log user in

      $rs = $data_servant->pdo->prepare("UPDATE access_log SET last_log_in = ?, is_online = ? WHERE a_username = ?");
      if ($rs->execute(array($today, 1, $username))) {
        # set cookie
        setcookie('user', $username, time()+60*60*24*7, '/');

        // go to home page
        header('location: home.php');
      }

    } else {
      # access invalid go to index page
      header('location: index.php?msg=Invalid access details#login');
    }   

  } 
} # end of if post statement
else {
  # increment log out page clicks
  # get current clicks
  $s=$data_servant->pdo->prepare("SELECT clicks FROM pages WHERE page = 'index' LIMIT 1");
  if ($s->execute()) {
    if ($r=$s->fetch()) {
      $st=$data_servant->pdo->prepare("UPDATE pages SET clicks = ".++$r['clicks']." WHERE page = 'index'");
      $st->execute();
    }
  }
}
?>

<?php require_once 'codesPHP/head.php'; ?>

<?php
$countries = array("Algeria", "Angola", "Benin", "Botswana", "Burkina Faso", "Burundi", "Cameroon", "Cape Verde", "Central African Republic", "Chad", "Democratic Republic of Congo", "Republic of Congo", "Cote d'Ivoire", "Djibouti", "Egypt", "Equatorial Guinea", "Eritrea", "Ethiopia", "Gabon", "Gambia", "Ghana", "Guinea", "Guinea Bissau", "Kenya", "Lesotho", "Liberia", "Libya", "Madagascar", "Malawi", "Mali", "Mauritania", "Mauritius", "Morocco", "Mozambique", "Namibia", "Niger", "Nigeria", "Reunion", "Rwanda", "Sao Tome and Principe", "Senegal", "Seychelles", "Sierra Leone", "Somalia", "South Africa", "South Sudan", "Sudan", "Swaziland", "Tanzania", "Togo", "Tunisia", "Uganda", "Zambia", "Zimbabwe");
?>

<style type="text/css"> .bg-1, .bg-2, .bg-3, .bg-4 { color: white; } </style>

<!-- First Container -->
<div class="container-fluid bg-1 text-center" style="background-image: url('icons/welcome.png');background-size: 100% 100%; background-repeat: no-repeat;">
  <h3 class="margin"></h3>
  <img src="icons/explore.png" class="img-responsive img-rounded margin" style="display:inline" alt="logo" width="350" height="350">
  <h2 class="margin" style="color: white;">
  Hobb creates awareness for local content, innovation and invention in Africa and helps to market them.</h2>
</div>

<!-- Second Container -->
<span id="login"></span>
<div class="container-fluid bg-2 text-center">
  <div class="row">
  <h3 class="margin">Log In</h3>
  <div class="col-sm-4">
    <h4 class="text-center">Hobb Account</h4>
    <form action="<?php echo($_SERVER['PHP_SELF']); ?>" class="form-horizontal" method="POST">
      <div class="input-group">
      <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
      <input id="username" type="text" class="form-control" name="username" placeholder="Username" 
        value="<?php echo (!empty($_COOKIE['user']) ? $_COOKIE['user'] : $_COOKIE['fb_user']); ?>" >
      </div>
      <div class="input-group">
        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
        <input id="password" type="password" class="form-control" name="password" placeholder="password">
      </div>
      <div class="form-group">        
        <div class="col-sm-offset-2 col-sm-10">
          <span class="text-left small-text" onclick="displayPasswordRecoveryDiv()" style="cursor: pointer;"><i>
            lost password?
          </i></span>
          <button type="submit" name="log_in" class="btn btn-success">Log In</button>
        </div>
      </div>
    </form>
  </div>
  
  <div class="col-sm-4">
    <h4 class="text-center">Facebook Account</h4>
    <!--fb:login-button 
      scope="public_profile,email"
      onlogin="checkLoginState();">
    </fb:login-button-->
    <div class="fb-login-button" data-width="300px" data-max-rows="1" data-size="large" data-button-type="continue_with" data-show-faces="false" data-auto-logout-link="false" data-use-continue-as="true" onlogin="checkLoginState();"></div>
  </div>

  <div class="col-sm-4 text-center">
    <h3 class="margin">Share with Africa. Know what Africa have. Explore Africa.</h3>
  </div>
  <!--a href="#" class="btn btn-default btn-lg">
    <span class="glyphicon glyphicon-search"></span> Search
  </a-->
  </div>
</div>
<div id="password_recovery">
    <div id="recovery_content">
      <div class="well">
        <div>
          <h4>Enter email address:</h4>
          <p><kbd>You'll receive an email from Hobb. Go to your email account to verify that it's yours.</kbd></p>
          <div id="recovery_form">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input id="recovery_mail" type="text" class="form-control" name="recovery_mail" placeholder="Email" >
            </div> 
            <div class="form-group">        
              <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" name="submit_recovery_mail" class="btn btn-success" onclick="submitRecoveryMail()">Submit Email Address</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<!-- Third Container (Grid) -->
<span id="signup"></span>
<div class="container-fluid bg-3 text-center" style="background-image: url('icons/signup_bg.png');background-size: 100% 100%; background-repeat: no-repeat; color: black;">    
  <h3 class="margin">Sign Up for An Account!</h3><br>
  <div class="row">
    <div class="col-sm-5">
      <h3 class="margin">We have things we don't know we do. You can help cure the ignorance.</h3>
      <h3 class="margin">Create an account, let us be aware of your <i>stuff</i>. Africa needs your services/products.</h3>
    </div>
    <div class="col-sm-2">
      
    </div>
    <div class="col-sm-5 text-left"> 
      <form action="codesPHP/sign_up.php" class="form-horizontal" method="POST" id="signup-form">
        <div class="form-group">
          <div class="col-sm-5">
            <input type="text" class="form-control" id="firstname" name="firstname" placeholder="First name" 
            value="<?php print(isset($firstname) ? $firstname : "") ?>">
          </div>
          <div class="col-sm-5">          
            <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Last name" 
            value="<?php print(isset($lastname) ? $lastname : "") ?>">
          </div>
        </div>
        <div class="form-group"> 
          <div class="col-sm-5">          
            <select class="form-control" id="gender" name="gender">
              <option selected disabled>Gender</option>
              <option>Male</option>
              <option>Female</option>
            </select>
          </div>
          <div class="col-sm-5">          
            <select class="form-control" id="country" name="country">
              <option selected disabled>Country</option>
              <?php
              for ($i=0; $i < count($countries); $i++) { 
                printf("<option>%s</option>", $countries[$i]);
              }
              ?>
            </select>
            <input type="hidden" name="thisCountry">
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-10">          
            <input type="email" class="form-control" id="email" name="email" placeholder="Email" 
            value="<?php print(isset($email) ? $email : "") ?>">
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-10">
            <span style="font-size:12px;"><i>*include country code</i></span>
            <input type="phone" class="form-control" id="phone" name="phone" placeholder="Phone" 
            value="<?php print(isset($phone) ? $phone : "") ?>">
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-10">          
            <input type="password" class="form-control" id="mypass" name="password" placeholder="Password" value="">
          </div>
        </div>
        <div class="form-group" id="confirmPassword">
          <div class="col-sm-10">          
            <input type="password" class="form-control" id="mypass2" onkeyup="checkPassword()" placeholder="Re-type password" value="">
            <span class="glyphicon glyphicon-remove form-control-feedback" id="passwordError" style="display: none;"></span>
            <span class="glyphicon glyphicon-ok form-control-feedback" id="passwordOk" style="display: none;"></span>
          </div>
        </div>
        <div class="form-group">        
          <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-default" name="sign_up">Sign Up</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">

  function checkPassword() {

    if (!(document.getElementById('mypass').value == document.getElementById('mypass2').value)) {
      document.getElementById('confirmPassword').className = "form-group has-error has-feedback";
      document.getElementById('passwordError').style.display = "block";
      document.getElementById('passwordOk').style.display = "none";
    } else {
      document.getElementById('confirmPassword').className = "form-group has-success has-feedback";
      document.getElementById('passwordError').style.display = "none";
      document.getElementById('passwordOk').style.display = "block";
    }

  }

</script>

<!-- Fourth container explore -->
<div class="container-fluid bg-1 text-center" id="explore" style="color: black;">
  <h3 class="margin text-center">Explore</h3>
  <!--<p>Governor of the Month</p>
        <p>#1 Trending</p>
        <p>#1 Trending</p>
        <p>Tourist site</p>-->

<?php
  $counter = 0;
  $statement2 = $data_servant->pdo->prepare("SELECT * FROM posts ORDER BY post_id DESC LIMIT 8");
  $statement2->execute();
  while ($latestPosts = $statement2->fetch()) {
    $sql1 = "SELECT hobb_people.firstname, hobb_people.lastname FROM hobb_people WHERE hobb_people.email = ?";
    $statement1 = $data_servant->pdo->prepare($sql1);
    $statement1->execute(array($latestPosts['p_username']));
    while ($postInfo = $statement1->fetch()) {

      // Get likes
      $stmt = $data_servant->pdo->prepare("SELECT count(p_username) FROM post_reactions WHERE post_id = ? LIMIT 1");
      $stmt->execute(array($latestPosts['post_id']));
      $like = $stmt->fetch();

      ++$counter;
      if (($counter % 4) == 0) {
        ?>
        <div class="row">
        <?php
      }
      
      # post latest posts
      if (($latestPosts['post_photo'] != "") && ($latestPosts['post_video'] == "")) {
        # post with photo
        ?>
          <div class='col-sm-3'>
            <div class='thumbnail'>
              <p><?php print($postInfo['firstname']." ".$postInfo['lastname']); ?></p><hr>
              <img src='images/<?php print($latestPosts['post_photo']); ?>' alt='Paris' width='100%' height='100%'>
              <hr>
              <p style="font-size: 12px;">
                <img src="icons/greenthumb.png" width="20" height="20">
                <sup> . </sup><?php printf("%s", $like['count(p_username)']); ?>
              </p>
            </div>  
          </div>
          
        <?php
      } elseif (($latestPosts['post_video'] != "") && ($latestPosts['post_photo'] == "")) {
        # post with video
        ?>
          <div class='col-sm-3'>
            <div class='thumbnail'>
              <p><?php print($postInfo['firstname']." ".$postInfo['lastname']); ?></p><hr>
              <video width='100%' height='relative' controls >;
                <source src='videos/<?php print($latestPosts['post_video']); ?>' type='video/mp4'>
                <source src='videos/<?php print($latestPosts['post_video']); ?>' type='video/ogg'>
                Your browser does not support the video tag.
              </video>
              <hr>
              <p style="font-size: 12px;">
                <img src="icons/greenthumb.png" width="20" height="20">
                <sup> . </sup><?php printf("%s", $like['count(p_username)']); ?>
              </p>
            </div>  
          </div>
          
        <?php
      } else {
        # post with text
        ?>
          <div class='col-sm-3'>
            <div class='thumbnail'>
              <p><?php print($postInfo['firstname']." ".$postInfo['lastname']); ?></p>
              <hr><p><?php print($latestPosts['post_text']); ?></p>
              <hr>
              <p style="font-size: 12px;">
                <img src="icons/greenthumb.png" width="20" height="20">
                <sup> . </sup><?php printf("%s", $like['count(p_username)']); ?>
              </p>
            </div>  
          </div>
          
        <?php
      }
      
      if (($counter % 4) == 0) {
          ?>
          </div>
          <?php
        }
    }
  }
?>

</div>
<?php
if (isset($_GET['msg']) && strpos($_GET['msg'], 'PROCEED') > 0) {
    ?>
    <script type="text/javascript">
        document.getElementById('username').disabled = true;
        document.getElementById('password').disabled = true;
    </script>
<?php
}
?>

<script type="text/javascript">
  $("#signup-form, #recovery_mail").keypress(
    function(event){
     if (event.which == '13') {
        event.preventDefault();
      }
  });
</script>

<!-- JAVASCRIPT -->
<?php include_once 'js/script.php'; ?>

<?php require_once 'codesPHP/foot.php'; ?>


