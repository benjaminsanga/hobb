<?php require_once 'codesPHP/data.php'; 

# increment log out page clicks
# get current clicks
$s=$data_servant->pdo->prepare("SELECT clicks FROM pages WHERE page = 'profile' LIMIT 1");
if ($s->execute()) {
  if ($r=$s->fetch()) {
    $st=$data_servant->pdo->prepare("UPDATE pages SET clicks = ".++$r['clicks']." WHERE page = 'profile'");
    $st->execute();
  }
}

# check for the cookie user
if (isset($_COOKIE['user']) && !empty($_COOKIE['user'])) {

  # check if page should be refreshed
  if (isset($_GET['ref'])) {
    # refresh page
    $refresh_value = htmlspecialchars($_GET['ref']);
    header('location: home.php#'.$refresh_value);
  }

  $username = $_COOKIE['user'];
  # get user's details
  $params = array('firstname', 'lastname', 'profile_pic', 'email', 'phone', 'country', 'gender');
  $table = "hobb_people";
  $condition = "email='" . $_COOKIE['user'] . "' LIMIT 1";
  $user = $data_servant->select($params, $table, $condition);

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
  .feed {
    font-size: 12px;
  }
</style>

<div class="container text-center main">  
  <div class="row">
    <div class="col-sm-2 well">      
      <p>Hobbers</p>
      <div class="hobbers-tab-pane well">
        <div>
          <?php
          # get emails of connected people
          $sql = "SELECT second_username FROM connected_people WHERE first_username = ?";
          $stmt = $data_servant->pdo->prepare($sql);
          $stmt->execute(array($username));
          while ($connected = $stmt->fetch()) {
            $st = $data_servant->pdo->prepare("SELECT firstname, lastname, email FROM hobb_people WHERE email = ?");
            $st->execute(array($connected['second_username']));
            while ($row = $st->fetch()) {
              # copy details
              if (!($row['email'] == $_COOKIE['user'])) {
                printf("<p style='font-size:12px;'><a href='see_profile.php?user=%s'>%s %s</a></p>", $row['email'], $row['firstname'], $row['lastname']);
              }
            }
          }
          # get emails of connected people
          $sql = "SELECT first_username FROM connected_people WHERE second_username = ?";
          $stmt = $data_servant->pdo->prepare($sql);
          $stmt->execute(array($username));
          while ($connected = $stmt->fetch()) {
            $st = $data_servant->pdo->prepare("SELECT firstname, lastname, email FROM hobb_people WHERE email = ?");
            $st->execute(array($connected['first_username']));
            while ($row = $st->fetch()) {
              # copy details
              if (!($row['email'] == $_COOKIE['user'])) {
                printf("<p style='font-size:12px;'><a href='see_profile.php?user=%s'>%s %s</a></p>", $row['email'], $row['firstname'], $row['lastname']);
              }
            }
          }
          ?>
        </div>
      </div>              
      
      <p><img src="icons/noty-icon.png" width="30" height="relative"></p>
      <div class="hobbers-tab-pane text-left">
        <div id="noty-feed" class="noty-feed">
        </div>      
      </div>

    </div>

    <div class="col-sm-7">
      <div class="row">
        <div class="col-sm-12">
          <div class="text-center">
            <div>
              <h4 class="margin"><?php printf("%s %s", $user[0]['firstname'], $user[0]['lastname']); ?></h4>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-12">
          <div class="well">
            <img src="<?php printf("profile_photos/%s", $user[0]['profile_pic']); ?>" width="100%" height="100%" alt="Profile photo">

            <form action="codesPHP/change_photo.php" method="POST" enctype="multipart/form-data">
              <label for="new_photo" class="custom-file-upload">select new photo</label>
              <input type="file" name="new_photo" id="new_photo" accept="image/*" />
              <input type="submit" name="change_profile_photo" value="change">
            </form>
          </div>
          <div class="text-left well">
            <h4>Bio</h4>
            <p><?php printf("Contacts: %s, %s", $user[0]['phone'], $user[0]['email']); ?></p>
            <p><?php printf("Gender: %s", $user[0]['gender']); ?></p>
            <p><?php printf("Country: %s", $user[0]['country']); ?></p>
          </div>
        </div>
      </div>

      <p>posts</p>

<?php
$sql = "SELECT *
        FROM hobb_people
        INNER JOIN posts 
        ON hobb_people.email = posts.p_username 
        WHERE email = ?
        ORDER BY posts.post_id DESC";
$stmt1 = $data_servant->pdo->prepare($sql);
$stmt1->execute(array($_COOKIE['user']));
while ($peopleAndPosts = $stmt1->fetch()) {
  # get and comments reactions on post

  $sql = "SELECT count(p_username) FROM post_reactions WHERE post_id = ? LIMIT 1";
  $stmt2 = $data_servant->pdo->prepare($sql);
  $stmt2->execute(array($peopleAndPosts['post_id']));
  if ($likes = $stmt2->fetch()) {
    # print posts with reactions
    if (($peopleAndPosts['post_photo']) != "" && ($peopleAndPosts['post_video']) == "" && ($peopleAndPosts['post_text'] == "")) {
      # photo only
    ?>
      <div class="row">
        <div class="col-sm-12">
          <div class="well">
            <div class="text-left">
              <p>
                <span>
                  <img src="profile_photos/<?php printf("%s", $peopleAndPosts['profile_pic']); ?>" width=20 height=20 >&nbsp;
                  <?php printf("%s %s", $peopleAndPosts['firstname'], $peopleAndPosts['lastname']); ?>
                </span>
              </p>
            </div><hr>

            <div class="row">
              <div class="col-sm-12">
                <img src="images/<?php printf("%s", $peopleAndPosts['post_photo']); ?>" width="90%" height="90%">
              </div>
            </div>

            <hr><div class="text-left">
              <p>
                <span style="font-size: 10px;">
                  <?php printf("%s", $likes['count(p_username)']); ?>
                </span> 
                <span style="">.</span> 
                <img src="icons/greenthumb.png" width="20" height="20">&nbsp;&nbsp;
                <img src="icons/comment.png" width="20" height="20">&nbsp;|&nbsp;
                <span>Mercy: mercy's comment</span></p>
            </div>
          </div>
        </div>
      </div>
    <?php
    } elseif (($peopleAndPosts['post_photo']) != "" && ($peopleAndPosts['post_text']) != "" && ($peopleAndPosts['post_video'] == "")) {
      # photo and text only
    ?>
      <div class="row">
        <div class="col-sm-12">
          <div class="well">
            <div class="text-left">
              <p>
                <span>
                  <img src="profile_photos/<?php printf("%s", $peopleAndPosts['profile_pic']); ?>" width=20 height=20 >&nbsp;
                  <?php printf("%s %s", $peopleAndPosts['firstname'], $peopleAndPosts['lastname']); ?>
                </span>
              </p>
            </div><hr>

            <div class="row">
              <div class="col-sm-7">
                <img src="images/<?php printf("%s", $peopleAndPosts['post_photo']); ?>" width="100%" height="100%">
              </div>
              
              <div class="col-sm-5">
              <p><?php printf("%s", $peopleAndPosts['post_text']); ?></p>
              </div>
            </div>
            <hr><div class="text-left">
              <p>
                <span style="font-size: 10px;">
                  <?php printf("%s", $likes['count(p_username)']); ?>
                </span> 
                <span style="">.</span> 
                <img src="icons/greenthumb.png" width="20" height="20">&nbsp;&nbsp;
                <img src="icons/comment.png" width="20" height="20">&nbsp;|&nbsp;
                <span>Mercy: mercy's comment</span></p>
            </div>
          </div>
        </div>
      </div>
    <?php
    } elseif (($peopleAndPosts['post_video']) != "" && ($peopleAndPosts['post_photo']) == "" && ($peopleAndPosts['post_text'] == "")) {
      # video only
    ?>
      <div class="row">
        <div class="col-sm-12">
          <div class="well">
            <div class="text-left">
              <p>
                <span>
                  <img src="profile_photos/<?php printf("%s", $peopleAndPosts['profile_pic']); ?>" width=20 height=20 >&nbsp;
                  <?php printf("%s %s", $peopleAndPosts['firstname'], $peopleAndPosts['lastname']); ?>
                </span>
              </p>
            </div><hr>

            <div class="row">
              <div class="col-sm-12">
                <video width="90%" height="relative" controls >;
                  <source src="videos/<?php printf("%s", $peopleAndPosts['post_video']); ?>" type="video/mp4">
                  <source src="videos/<?php printf("%s", $peopleAndPosts['post_video']); ?>" type="video/ogg">
                  Your browser does not support the video tag.
                </video>
              </div>
            </div>

            <hr><div class="text-left">
              <p>
                <span style="font-size: 10px;">
                  <?php printf("%s", $likes['count(p_username)']); ?>
                </span> 
                <span style="">.</span> 
                <img src="icons/greenthumb.png" width="20" height="20">&nbsp;&nbsp;
                <img src="icons/comment.png" width="20" height="20">&nbsp;|&nbsp;
                <span>Mercy: mercy's comment</span></p>
            </div>
          </div>
        </div>
      </div>
    <?php
    } elseif (($peopleAndPosts['post_video']) != "" && ($peopleAndPosts['post_text']) != "" && ($peopleAndPosts['post_photo'] == "")) {
      # video and text only
    ?>
      <div class="row">
        <div class="col-sm-12">
          <div class="well">
            <div class="text-left">
              <p>
                <span>
                  <img src="profile_photos/<?php printf("%s", $peopleAndPosts['profile_pic']); ?>" width=20 height=20 >&nbsp;
                  <?php printf("%s %s", $peopleAndPosts['firstname'], $peopleAndPosts['lastname']); ?>
                </span>
              </p>
            </div><hr>

            <div class="row">
              <div class="col-sm-7">
                <video width="100%" height="relative" controls >;
                  <source src="videos/<?php printf("%s", $peopleAndPosts['post_video']); ?>" type="video/mp4">
                  <source src="videos/<?php printf("%s", $peopleAndPosts['post_video']); ?>" type="video/ogg">
                  Your browser does not support the video tag.
                </video>
              </div>
              
              <div class="col-sm-5">
              <p><?php printf("%s", $peopleAndPosts['post_text']); ?></p>
              </div>
            </div>
            <hr><div class="text-left">
              <p>
                <span style="font-size: 10px;">
                  <?php printf("%s", $likes['count(p_username)']); ?>
                </span> 
                <span style="">.</span> 
                <img src="icons/greenthumb.png" width="20" height="20">&nbsp;&nbsp;
                <img src="icons/comment.png" width="20" height="20">&nbsp;|&nbsp;
                <span>Mercy: mercy's comment</span></p>
            </div>
          </div>
        </div>
      </div>
    <?php
    } elseif (($peopleAndPosts['post_text']) != "" && ($peopleAndPosts['post_photo']) == "" && ($peopleAndPosts['post_video'] == "")) {
      # text only
    ?>
      <div class="row">
        <div class="col-sm-12">
          <div class="well">
            <div class="text-left">
              <p>
                <span>
                  <img src="profile_photos/<?php printf("%s", $peopleAndPosts['profile_pic']); ?>" width=20 height=20 >&nbsp;
                  <?php printf("%s %s", $peopleAndPosts['firstname'], $peopleAndPosts['lastname']); ?>
                </span>
              </p>
            </div><hr>

            <div class="row">
              <div class="col-sm-12">
                <p><?php printf("%s", $peopleAndPosts['post_text']); ?></p>
              </div>
            </div>

            <hr><div class="text-left">
              <p>
                <span style="font-size: 10px;">
                  <?php printf("%s", $likes['count(p_username)']); ?>
                </span> 
                <span style="">.</span> 
                <img src="icons/greenthumb.png" width="20" height="20">&nbsp;&nbsp;
                <img src="icons/comment.png" width="20" height="20">&nbsp;|&nbsp;
                <span>Mercy: mercy's comment</span></p>
            </div>
          </div>
        </div>
      </div>
    <?php
    }
  }
}
?>
    
    </div>

    <!-- ASIDE DIV -->
    <div class="col-sm-3">
      <!--div class="thumbnail">
        <p>Fri. 27 Nov 2015</p>
        <p>$1 = &#8358;360</p>
        <p>Jos - 21c</p>
      </div>      
      <div class="well">
        <p>ADS VACANCY</p>
      </div>
      <div class="well">
        <p>ADS VACANCY</p>
      </div-->

      <p>Trending #1</p>
<?php
$statement = $data_servant->pdo->prepare("SELECT *, count(p_username) AS like_occurence FROM post_reactions GROUP BY post_id ORDER BY like_occurence DESC LIMIT 1");
$statement->execute();
$counter = 0;
while ($tops = $statement->fetch()):
  # get top 5 details from posts and hobb_people tables
  $sql = "SELECT hobb_people.firstname, posts.post_photo, posts.post_video, posts.post_text FROM hobb_people INNER JOIN posts ON hobb_people.email=posts.p_username WHERE posts.post_id = ?";
  $statement1 = $data_servant->pdo->prepare($sql);
  $statement1->execute(array($tops['post_id']));

  while ($top5 = $statement1->fetch()):
    # get top 5 posts
    if (($top5['post_photo']) != "" && ($top5['post_video'] == "")) {
      # POST CONTAINS PHOTO
  ?>
    <div class="thumbnail">
      <p>
        <a href="see_profile.php?user=<?php print($tops['p_username']); ?>">
          <?php printf("#%d %s", ++$counter, $top5['firstname']); ?>
        </a>
      </p><hr>
      <img src="images/<?php printf("%s", $top5['post_photo']); ?>" alt="post photo" width="100%" height="100%">
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
    } elseif (($top5['post_video']) != "" && ($top5['post_photo'] == "")) {
      # POST CONTAINS VIDEO
  ?>
    <div class="thumbnail">
      <p>
        <a href="see_profile.php?user=<?php print($tops['p_username']); ?>">
          <?php printf("#%d %s", ++$counter, $top5['firstname']); ?>
        </a>
      </p><hr>
      <video width="100%" height="relative" controls >;
        <source src="videos/<?php printf("%s", $top5['post_video']); ?>" type="video/mp4">
        <source src="videos/<?php printf("%s", $top5['post_video']); ?>" type="video/ogg">
        Your browser does not support the video tag.
      </video>
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
    }  elseif (($top5['post_text'] != "") && ($top5['post_video'] == "") && ($top5['post_photo'] == "")) {
      # POST CONTAINS TEXT ONLY
    ?>
      <div class="thumbnail">
        <p>
          <a href="see_profile.php?user=<?php print($tops['p_username']); ?>">
            <?php printf("#%d %s", ++$counter, $top5['firstname']); ?>
          </a>
        </p><hr>
        <p><?php printf("%s", $top5['post_text']) ?></p>
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
