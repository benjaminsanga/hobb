<?php require_once 'codesPHP/data.php'; 

# increment log out page clicks
# get current clicks
$s=$data_servant->pdo->prepare("SELECT clicks FROM pages WHERE page = 'settings' LIMIT 1");
if ($s->execute()) {
  if ($r=$s->fetch()) {
    $st=$data_servant->pdo->prepare("UPDATE pages SET clicks = ".++$r['clicks']." WHERE page = 'settings'");
    $st->execute();
  }
}

# check for the cookie user
if (isset($_COOKIE['user']) && !empty($_COOKIE['user'])) {
  $username = $_COOKIE['user'];

} else {
  # redirect to index page if cookie not set
  header('location: index.php');
}
?>
<?php require_once 'codesPHP/head.php'; ?>

<style type="text/css">
  .input {
    width: 200px;
    padding: 7px;
    font-size: 0.9em;
  }
  .table-borderless > tbody > tr > td,
  .table-borderless > tbody > tr > th,
  .table-borderless > tfoot > tr > td,
  .table-borderless > tfoot > tr > th,
  .table-borderless > thead > tr > td,
  .table-borderless > thead > tr > th {
      border: none;
  }
  a {
    color: black;
  }
  a:hover {
    text-decoration: none;
    color: #474e5d;
  }
</style>

<!-- blank thumb is for unliked post, black thumb is for showing number of post, green thumb is for like post -->
<div class="container text-left main">    
  <h1 class="margin">Settings</h1><hr>
  <div class="row margin well">
    <div class="col-sm-4">
      <h3 class="margin">Account</h3>
      <?php 
      $output;
      $statement = $data_servant->pdo->prepare("SELECT * FROM hobb_people WHERE email = ? LIMIT 1");
      $statement->execute(array($_COOKIE['user']));
      if ($userInfo = $statement->fetch()) {
        $output = $userInfo;
      }
      print("<img src='profile_photos/".$userInfo['profile_pic']."' width='relative' height='300'>");
      ?>
      
      <form action="codesPHP/change_photo.php" method="POST" enctype="multipart/form-data">
        <label for="new_photo" class="custom-file-upload">select new photo</label>
        <input type="file" name="new_photo" id="new_photo" accept="image/*" />
        <input type="submit" name="change_photo" value="change" class="btn btn-md btn-success">
      </form>
      <br>

      <div id="user_info">
        <form action="codesPHP/update_profile.php" method="POST">
          <table class="table table-borderless">
            <tr><td>
              <input type="text" name="hobby" class="input" placeholder="Hobby" 
              value="<?php print $output['hobby'] != "" ? $output['hobby'] : ""; ?>">
            </td></tr>
            <tr><td>
              <input type="text" name="dob" class="input" placeholder="Date of birth" 
              value="<?php print ($output['dob'] != "" && $output['dob'] != "0000-00-00") ? $output['dob'] : ""; ?>">
            </td></tr>
            <tr><td>
              <input type="text" name="city" class="input" placeholder="City" 
              value="<?php print $output['city'] != "" ? $output['city'] : ""; ?>">
            </td></tr>
            <tr><td>
              <input type="hidden" name="email" value="<?php print($_COOKIE['user']); ?>"><br>
              <input type="submit" name="update" value="Update"  class="btn btn-md btn-success">
            </td></tr>
          </table>
        </form>
      </div>
    </div>
    <div class="col-sm-4">
      <h3>Background image</h3>
      <form action="codesPHP/change_page_image.php" method="POST" enctype="multipart/form-data">
        <label for="new_page_image" class="custom-file-upload">select new image</label>
        <input type="file" name="new_page_image" id="new_page_image" accept="image/*" /><br>
        <input type="hidden" name="email" value="<?php print($_COOKIE['user']); ?>">
        <input type="submit" name="change_page_image" value="change" class="btn btn-md btn-success">
      </form><br>
      <button class="btn btn-md btn-danger" onclick="takeOutBgImage('<?php print($_COOKIE['user']); ?>')">Remove background image</button>
    </div>
    <div class="col-sm-4"></div>
  </div>
</div>

<script type="text/javascript">
  function takeOutBgImage(email) {
    //alert(email);
    var agent = new XMLHttpRequest();

    if (agent) {
      var ds = "codesPHP/remove_bg_image.php?email="+email; // data source
      agent.open("GET", ds, true);

      agent.onreadystatechange = function(){
        if(agent.readyState == 4 && agent.status == 200){ 
          if (agent.responseText.indexOf('1') > -1) {
            window.location.href = 'settings.php';
          } else {
            alert(agent.responseText);
          }
        }
      }
    }
    agent.send(null);
  }
</script>

<?php require_once 'codesPHP/foot.php'; ?>
