<?php require_once 'codesPHP/data.php'; 

# increment log out page clicks
# get current clicks
$s=$data_servant->pdo->prepare("SELECT clicks FROM pages WHERE page = 'see profile' LIMIT 1");
if ($s->execute()) {
  if ($r=$s->fetch()) {
    $st=$data_servant->pdo->prepare("UPDATE pages SET clicks = ".++$r['clicks']." WHERE page = 'see profile'");
    $st->execute();
  }
}

# check for the cookie user
if ((isset($_COOKIE['user']) && !empty($_COOKIE['user'])) || isset($_COOKIE['fb_user'])) {

  # check if page should be refreshed
  if (isset($_GET['user'])) {
    # refresh page
    $view_user = htmlspecialchars($_GET['user']);
  } else {
    # go to home page
    header('location: home.php');
  }

  $username = isset($_COOKIE['user']) ? $_COOKIE['user'] : $_COOKIE['fb_user'];
  # get user's details
  $params = array('firstname', 'lastname', 'profile_pic');
  $table = "hobb_people";
  $condition = "email='" . $username . "' LIMIT 1";
  $user = $data_servant->select($params, $table, $condition);

  # get viewed user
  $sql = "SELECT * FROM hobb_people WHERE email = ? LIMIT 1";
  $statement0 = $data_servant->pdo->prepare($sql);
  $statement0->execute(array($view_user));
  $viewed_user = $statement0->fetch();

} else {
  # redirect to index page if cookie not set
  header('location: index.php');
}
?>
<?php require_once 'codesPHP/head.php'; ?>
  
<style type="text/css">
  .post-text {
    font-size: 15px;
  }
  a {
    color: black;
  }
  a:hover {
    text-decoration: none;
    color: #474e5d;
  }
  .update:hover {
    color: red;
    font-weight: normal;
  }
  .feed {
    font-size: 12px;
  }
</style>
<script type="text/javascript">
  
function thumbsUp(postId, likedBy) {
  // mark the post liked by this user
  var agent = new XMLHttpRequest();
  
  if (agent) {
    var ds = "codesPHP/like.php?postId="+postId+"&likedBy="+likedBy; // data source
    agent.open("GET", ds, true);
    
    agent.onreadystatechange = function(){
      if(agent.readyState == 4 && agent.status == 200){
        if (agent.responseText == '1') {
          // get the clicked thumbs up button and change it to green thumb
          $('#up-'+postId)
            .attr('src', 'icons/greenthumb.png')
            .width(22)
            .height(22);
        } else {
          alert(agent.responseText);
        }
      }
    }
  }
  agent.send(null);

}
function interest() {
  document.getElementById('interest-field').innerHTML = "<input type='text' class='form-control' id='interested' name='interested' placeholder='interested in?'><span class='btn btn-default btn-sm' onclick='addInterest()' style='cursor: pointer;'>+</span>";
}
function addInterest(){ 
  var user = document.getElementById('userpass').value;
  var areaOfInterest = document.getElementById('interested').value;
  
  var agent = new XMLHttpRequest();

  if (agent) {
    var ds = "codesPHP/add_interest.php?user="+user+"&aoi="+areaOfInterest; // data source
    agent.open("GET", ds, true);

    agent.onreadystatechange = function(){
      //alert('here');
    }
  }
  agent.send(null);
  
}
</script>
<script type="text/javascript">
  function addInterest() { 
  // add this to interest field
  if (agent) {
    agent.onreadystatechange = function(){
      if(agent.readyState == 4 && agent.status == 200){ 
        if (agent.responseText == '1') {
          document.getElementById('new-field').innerHTML = "<a href='home.php?sort=".areaOfInterest."'><span class='label label-success'>"+ areaOfInterest +"</span></a><br>";
          //document.getElementById('interest-field').innerHTML = "<span class='btn btn-default btn-sm' onclick='interest()' style='cursor: pointer;'>Add</span>";
        }
      }
    }
  }
  agent.send(null);
}
</script>

<input type="hidden" id="userpass" value="<?php echo($_COOKIE['user']); ?>">
 
<div class="container text-center main">    
  <div class="row">
    <div class="col-sm-2 well">
      <div>
        <?php if (!empty($user[0]['profile_pic'])) {
            ?>
            <a href="profile.php">
            <img src="<?php printf("profile_photos/%s", $user[0]['profile_pic']); ?>" class="img-circle" height="60" width="60" alt="Profile photo" id="profile-pic">
            </a>
            <?php
          } else {
            ?>
            <img src="<?php print($_COOKIE['pc']); ?>" class="img-circle" alt="Profile photo" id="profile-pic">
            <?php
          } ?>
      </div>
      <hr>
      <?php
      # if not logged in through facebook
      if (isset($_COOKIE['user'])) {
        # show these
        ?>
      <div>
        <h5 title="My Areas of Interest, click on an area to sort feeds">Interests</h5>
        <p>
        <?php 
          # get user's interests fields
          $stmt = $data_servant->pdo->prepare("SELECT `interested_in` FROM `interests` WHERE `username`=?");
          $stmt->execute(array($username));
          while ($row = $stmt->fetch()) {
            $interestIn = $row['interested_in'];
            $interest = "<p id='{$interestIn}' onmouseover='showRemove(\"{$interestIn}\")' onmouseout='hideRemove(\"{$interestIn}\")'>";
            $interest .= "<a href='home.php?sort=".strtolower($interestIn)."'><span class='label label-default'>".$interestIn."</span></a>";
            $interest .= "<span id='{$interestIn}-x' style='padding-left:5px;cursor:pointer;opacity:0.5;font-size:15px;visibility:hidden;' title='Remove' onclick='removeInterest(\"{$interestIn}\", \"{$username}\")'>x</span>";
            $interest .= "</p>";
            print($interest);
          }
        ?>
          <div id="new-field"></div>
          <div  id="interest-field">
            <span class="btn btn-default btn-sm" onclick="interest()" style="cursor: pointer;">Add</span>
          </div>
        </p>
      </div>
      <hr>
      <div class="margin">
        <p><img src="icons/noty-icon.png" width="20" height="relative"></p>
        <div class="hobbers-tab-pane text-left">
          <div id="noty-feed" class="noty-feed">
          </div>      
        </div>
      </div>

      <div class="alert alert-success fade in">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
        <p><strong>Ads!</strong></p>
        People are looking at your profile. Find out who.
      </div>
      <?php
      }
      ?>
    </div>

    <!-- FEEDS DIV -->
    <div class="col-sm-7"> 
      <div class="row">
        <div class="col-sm-12">
          <div class="text-center">
            <h4 class="margin"><?php printf("%s %s's page", $viewed_user['firstname'], $viewed_user['lastname']); ?></h4>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-12">
          <div class="well">
            <img src="<?php printf("profile_photos/%s", $viewed_user['profile_pic']); ?>" width="100%" height="100%" alt="Profile photo">
          </div>
          <hr>
          <div class="text-left well">
            <h4>Bio</h4>
            <p><?php printf("Contacts: %s, %s", $viewed_user['phone'], $viewed_user['email']); ?></p>
            <p><?php printf("Gender: %s", $viewed_user['gender']); ?></p>
            <p><?php printf("Country: %s", $viewed_user['country']) ?></p>
          </div>
        </div>
      </div>


      <h5 id="margin">Posts</h5>
<?php
$sql = "SELECT *
        FROM hobb_people INNER JOIN posts ON hobb_people.email = posts.p_username WHERE hobb_people.email = ? ORDER BY posts.post_id DESC";
$stmt1 = $data_servant->pdo->prepare($sql);
$stmt1->execute(array($view_user));
while ($peopleAndPosts = $stmt1->fetch()) {
  # get and comments reactions on post

  $sql = "SELECT count(p_username) FROM post_reactions WHERE post_id = ? LIMIT 1";
  $stmt2 = $data_servant->pdo->prepare($sql);
  $stmt2->execute(array($peopleAndPosts['post_id']));
  if ($likes = $stmt2->fetch()) {
    # print posts with reactions
    ?>
      <div class="row">
        <div class="col-sm-12">
          <div class="well">
            <div class="text-left">
              <p>
                <span>
                  <img src="profile_photos/<?php printf("%s", $peopleAndPosts['profile_pic']); ?>" width=30 height=30 >&nbsp;
                  <?php printf("%s %s", $peopleAndPosts['firstname'], $peopleAndPosts['lastname']); ?>
                </span>
              </p>
            </div>

            <div class="row">

    <?php 
      if (($peopleAndPosts['post_photo']) != "" && ($peopleAndPosts['post_video']) == "" && ($peopleAndPosts['post_text'] == "")) {
        # photo only
      ?>
                  
              <div class="col-sm-12">
                <a href="single_post.php?p_id=<?php print($peopleAndPosts['post_id']); ?>">
                  <img src="images/<?php printf("%s", $peopleAndPosts['post_photo']); ?>" width="100%" height="100%">
                </a>
              </div>
      <?php
      } elseif (($peopleAndPosts['post_photo']) != "" && ($peopleAndPosts['post_text']) != "" && ($peopleAndPosts['post_video'] == "")) {
        # photo and text only
      ?>
              <div class="col-sm-7">
                <a href="single_post.php?p_id=<?php print($peopleAndPosts['post_id']); ?>">
                  <img src="images/<?php printf("%s", $peopleAndPosts['post_photo']); ?>" width="100%" height="100%">
                </a>
              </div>
              
              <div class="col-sm-5">
                <p><?php printf("%s", $peopleAndPosts['post_text']); ?></p>
              </div>

      <?php
      } elseif (($peopleAndPosts['post_video']) != "" && ($peopleAndPosts['post_photo']) == "" && ($peopleAndPosts['post_text'] == "")) {
        # video only
      ?>

              <div class="col-sm-12">
                <a href="single_post.php?p_id=<?php print($peopleAndPosts['post_id']); ?>">
                  <video width="100%" height="relative" controls >;
                    <source src="videos/<?php printf("%s", $peopleAndPosts['post_video']); ?>" type="video/mp4">
                    <source src="videos/<?php printf("%s", $peopleAndPosts['post_video']); ?>" type="video/ogg">
                    Your browser does not support the video tag.
                  </video>
                </a>
              </div>

      <?php
      } elseif (($peopleAndPosts['post_video']) != "" && ($peopleAndPosts['post_text']) != "" && ($peopleAndPosts['post_photo'] == "")) {
        # video and text only
      ?>

              <div class="col-sm-7">
                <a href="single_post.php?p_id=<?php print($peopleAndPosts['post_id']); ?>">
                  <video width="100%" height="relative" controls >;
                    <source src="videos/<?php printf("%s", $peopleAndPosts['post_video']); ?>" type="video/mp4">
                    <source src="videos/<?php printf("%s", $peopleAndPosts['post_video']); ?>" type="video/ogg">
                    Your browser does not support the video tag.
                  </video>
                </a>
              </div>
              
              <div class="col-sm-5">
                <p><?php printf("%s", $peopleAndPosts['post_text']); ?></p>
              </div>

      <?php
      } elseif (($peopleAndPosts['post_text']) != "" && ($peopleAndPosts['post_photo']) == "" && ($peopleAndPosts['post_video'] == "")) {
        # text only
      ?>

            <div class="col-sm-12">
              <p><?php printf("%s", $peopleAndPosts['post_text']); ?></p>
              <a href="single_post.php?p_id=<?php print($peopleAndPosts['post_id']); ?>">see more</a>
            </div>

      <?php
      }
    ?>

            </div>

            <hr>
            <div class="text-left">
              <p>
                <span style="font-size: 10px;">
                  <?php printf("%s", $likes['count(p_username)']); ?>
                </span> 
                <span style="">.</span> 
                <img src="icons/greenthumb.png" width="20" height="20">&nbsp;&nbsp;
                <img src="icons/comment.png" width="20" height="20">&nbsp;|&nbsp;
                <span></span></p>
            </div>
          </div>
        </div>
      </div>

    <?php

    } // likes check end
  } // end of people and posts loop

?>
       
      <div class="row">
        <div class="col-sm-12 text-center">
            <i class="fa fa-spinner fa-spin"></i>
        </div>
      </div>  
    </div>

    <!--TOP 5 POSTS-->
    <div class="col-sm-3" id="main-aside">
      <!--div class="thumbnail">
        <p>Fri. 27 Nov 2015</p>
        <p>$1 = &#8358;360</p>
        <p>Jos - 21c</p>
      </div-->

      <p><b>Trending 5</b></p>
<?php
$statement = $data_servant->pdo->prepare("SELECT *, count(p_username) AS like_occurence FROM post_reactions GROUP BY post_id ORDER BY like_occurence DESC LIMIT 5");
$statement->execute();
$counter = 0;
while ($tops = $statement->fetch()):
  # get top 5 details from posts and hobb_people tables
  $sql = "SELECT hobb_people.firstname, posts.post_photo, posts.post_video, posts.post_text FROM hobb_people INNER JOIN posts ON hobb_people.email=posts.p_username WHERE posts.post_id = ?";
  $statement1 = $data_servant->pdo->prepare($sql);
  $statement1->execute(array($tops['post_id']));

  while ($top5 = $statement1->fetch()):
    # get top 5 posts
    if (($top5['post_photo'] != "") && ($top5['post_video'] == "")) {
      # POST CONTAINS PHOTO
  ?>
    <div class="thumbnail">
      <p>
        <a href="see_profile.php?user=<?php print($tops['p_username']); ?>">
          <?php printf("#%d %s", ++$counter, $top5['firstname']); ?>
        </a>
      </p>

      <a href="single_post.php?p_id=<?php print($tops['post_id']) ?>">
        <img src="images/<?php printf("%s", $top5['post_photo']); ?>" alt="post photo" width="100%" height="100%">
      </a>

      <hr><p style="font-size: 12px;">
        <?php printf("%s", $tops['like_occurence']); ?>
        <sup> . </sup>
        <img src="icons/greenthumb.png" width="25" height="25">
      </p>
    </div>      
    <div class="well">
      <p>ADS VACANCY</p>
    </div>
  <?php
    } elseif (($top5['post_video'] != "") && ($top5['post_photo'] == "")) {
      # POST CONTAINS VIDEO
  ?>
    <div class="thumbnail">
      <p>
        <a href="see_profile.php?user=<?php print($tops['p_username']); ?>">
          <?php printf("#%d %s", ++$counter, $top5['firstname']); ?>
        </a>
      </p>

      <a href="single_post.php?p_id=<?php print($tops['post_id']) ?>">
        <video width="100%" height="relative" controls >;
          <source src="videos/<?php printf("%s", $top5['post_video']); ?>" type="video/mp4">
          <source src="videos/<?php printf("%s", $top5['post_video']); ?>" type="video/ogg">
          Your browser does not support the video tag.
        </video>
      </a>

      <hr><p style="font-size: 12px;">
        <?php printf("%s", $tops['like_occurence']); ?>
        <sup> . </sup>
        <img src="icons/greenthumb.png" width="25" height="25">
      </p>
    </div>      
    <div class="well">
      <p>ADS VACANCY</p>
    </div>
  <?php
    } elseif (($top5['post_text'] != "") && ($top5['post_video'] == "") && ($top5['post_photo'] == "")) {
      # POST CONTAINS TEXT ONLY
    ?>
      <div class="thumbnail">
        <p>
          <a href="see_profile.php?user=<?php print($tops['p_username']); ?>">
            <?php printf("#%d %s", ++$counter, $top5['firstname']); ?>
          </a>
        </p>
        <p><?php printf("%s", $top5['post_text']) ?></p>
        <p><a href="single_post.php?p_id=<?php print($tops['post_id']) ?>">view</a></p>
        <hr><p style="font-size: 12px;">
          <?php printf("%s", $tops['like_occurence']); ?>
          <sup> . </sup>
          <img src="icons/greenthumb.png" width="25" height="25">
        </p>
      </div>      
      <div class="well">
        <p>ADS VACANCY</p>
      </div>
    <?php
    }
  ?>
  <?php
  endwhile; // end while select top5

endwhile; // end tops from post reactions table

?>

    </div>
  </div>
</div>

<!-- JAVASCRIPT -->
<?php include_once 'js/script.php'; ?>

<?php require_once 'codesPHP/foot.php'; ?>


<?php
/*
$stdate = date('Y:m:d h:i:s');
$enddate = $row['time_of_post'];
$str = new DateTime($stdate);
$end = new DateTime($enddate);
$time_of_post = $str->diff($end); 
*/

?>
