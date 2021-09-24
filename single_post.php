<?php require_once 'codesPHP/data.php'; 

# increment log out page clicks
# get current clicks
$s=$data_servant->pdo->prepare("SELECT clicks FROM pages WHERE page = 'single post' LIMIT 1");
if ($s->execute()) {
  if ($r=$s->fetch()) {
    $st=$data_servant->pdo->prepare("UPDATE pages SET clicks = ".++$r['clicks']." WHERE page = 'single post'");
    $st->execute();
  }
}

# check for the cookie user
if ((isset($_COOKIE['user']) && !empty($_COOKIE['user'])) || isset($_COOKIE['fb_user'])) {

  # check if post id is submitted
  if (isset($_GET['p_id'])) {
    # get post id
    $p_id = htmlspecialchars($_GET['p_id']);
  } else {
    #go to home page
    header('location: home.php');
  }

  $username = isset($_COOKIE['user']) ? $_COOKIE['user'] : $_COOKIE['fb_user'];
  # get user's details
  $params = array('firstname', 'lastname', 'profile_pic');
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
  .update:hover {
    color: red;
    font-weight: normal;
  }
  .feed {
    font-size: 12px;
  }
</style>

<input type="hidden" id="userpass" value="<?php echo($_COOKIE['user']); ?>">
<input type="hidden" id="usernames" value="<?php printf('%s %s', $user[0]['firstname'], $user[0]['lastname']); ?>">
 
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
    <?php

    # get comments on post
    $sql2 = "SELECT count('p_username') AS like_occurence FROM post_reactions WHERE post_id = ?";
    $stmt2 = $data_servant->pdo->prepare($sql2);
    $stmt2->execute(array($p_id));
    $reactions = $stmt2->fetch();

    # get all users that like this post
    $users_likes = array();
    $stmt6 = $data_servant->pdo->prepare("SELECT p_username FROM post_reactions WHERE post_id = ?");
    $stmt6->execute(array($p_id));
    while ($likes = $stmt6->fetch()) {
      # populate the users likes array
      $users_likes[] = $likes['p_username'];
    }

    $sql = "SELECT * FROM hobb_people INNER JOIN posts ON hobb_people.email = posts.p_username WHERE posts.post_id = ? LIMIT 1";
    $stmt = $data_servant->pdo->prepare($sql);
    $stmt->execute(array($p_id));
    if ($details = $stmt->fetch()) {
      # get comments on post
      $comments = array();
      $stmt4 = $data_servant->pdo->prepare("SELECT hobb_people.firstname, hobb_people.lastname, hobb_people.email, post_comments.comment FROM post_comments INNER JOIN hobb_people ON post_comments.p_username=hobb_people.email WHERE `post_id` = ? ORDER BY post_comments.pc_id ASC");
      $stmt4->execute(array($details['post_id']));
      while($com = $stmt4->fetch()) {
        $comments[] = $com;
      }

  ?>

      <div class="col-sm-7"> 
        <div class="row">
          <div class="col-sm-12">
            <div class="well">
              <div class="text-left">
                <p>
                  <span>
                    <img src="profile_photos/<?php printf("%s", $details['profile_pic']); ?>" width=30 height=30 >&nbsp;
                    <a href="see_profile.php?user=<?php print($details['email']); ?>">
                      <?php printf("%s %s", $details['firstname'], $details['lastname']); ?>
                    </a>
                  </span>
                </p>
              </div>

              <div class="row">

    <?php  if (($details['post_photo'] != "") && ($details['post_text'] == "") && ($details['post_video'] == "")) {
        # photo only
        ?>
        <div class="col-sm-12">
          <img src="images/<?php printf("%s", $details['post_photo']); ?>" width="100%" height="100%">
        </div>
        <?php
      } elseif (($details['post_photo'] != "") && ($details['post_text'] != "") && ($details['post_video'] == "")) {
        # photo and text only
        ?>
        <div class="col-sm-7">
            <img src="images/<?php print $details['post_photo']; ?>" width="100%" height="100%">
        </div>
        
        <div class="col-sm-5">
        <p class="post-text">
          <?php printf("%s", $details['post_text']); ?>
        </p>
        </div>
        <?php
      } elseif (($details['post_video'] != "") && ($details['post_photo'] == "") && ($details['post_text'] == "")) {
        # video only 
        ?>
        <div class="col-sm-12">
          <video width="100%" height="relative" controls >;
            <source src="videos/<?php printf("%s", $details['post_video']); ?>" type="video/mp4">
            <source src="videos/<?php printf("%s", $details['post_video']); ?>" type="video/ogg">
            Your browser does not support the video tag.
          </video>
        </div>
        <?php
      } elseif (($details['post_video'] != "") && ($details['post_text'] != "") && ($details['post_photo'] == "")) {
        # video and text only
        ?>
        <div class="col-sm-7">
            <video width="100%" height="relative" controls >;
            <source src="videos/<?php printf("%s", $details['post_video']); ?>" type="video/mp4">
            <source src="videos/<?php printf("%s", $details['post_video']); ?>" type="video/ogg">
            Your browser does not support the video tag.
          </video>
        </div>
        
        <div class="col-sm-5">
        <p class="post-text">
          <?php printf("%s", $details['post_text']); ?>
        </p>
        </div>
        <?php
      } else {
        # text only
        ?>
        <div class="col-sm-12">
          <?php printf("%s", $details['post_text']); ?>
        </div>
        <?php
      }

    ?>
              </div>

              <hr><div class="text-left">
                <p>
                  <span id="num_likes_<?php print($p_id); ?>" style="font-size: 10px;">
                    <?php printf("%s", $reactions['like_occurence']); ?>
                  </span> 
                  <span style="">.</span> 
                  <?php
                  if (in_array($username, $users_likes)) {
                    # show green thumb
                  ?>
                    <img src="icons/greenthumb.png" 
                    id="up-<?php print($p_id); ?>" 
                    width="20" height="20">&nbsp;&nbsp;
                  <?php
                  } else {
                    // show gray thumb
                  ?>
                    <img src="icons/thumbs-up.png" 
                    id="up-<?php print($p_id); ?>" 
                    onclick="thumbsUp(<?php print("'{$p_id}', '{$username}'"); ?>)" 
                    width="20" height="20">&nbsp;&nbsp;
                  <?php
                  }
                  ?>
                  <!-- THE COMMENT BUTTON -->
                  <img src="icons/comment.png" width="20" height="20"  id="com-<?php print($p_id); ?>"/>&nbsp;|&nbsp;
                </p>

                <!-- THE OTHER COMMENTS -->
                <div class="row">
                  <!-- use commenting-postId to identify this div specifically -->
                  <div class="col-sm-12">
                    <?php
                     if (count($comments) > 0) {
                        for ($i=0; $i < count($comments); $i++) { 
                          printf("<p class='small-text'><b>%s %s</b>: %s</p>", $comments[$i]['firstname'], $comments[$i]['lastname'], $comments[$i]['comment']); 
                        }
                      }
                    ?>
                    <!-- NEW COMMENT -->
                    <div id="new_comment_<?php print($p_id); ?>"></div>

                    <input style="width: 80%;font-size: 15px;padding: 5px;" type="text" 
                    id="comment_box_<?php print($p_id); ?>" placeholder="say something about this post...">

                    <button onclick="commentOnPost(<?php print("'{$p_id}', '{$username}'"); ?>)" class="btn btn-default btn-sm" id="comment">Comment</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <hr>
      </div>
<?php
    }
    ?>

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
  $sql = "SELECT hobb_people.firstname, posts.post_photo, posts.post_video, posts.post_text, posts.p_username FROM hobb_people INNER JOIN posts ON hobb_people.email=posts.p_username WHERE posts.post_id = ?";
  $statement1 = $data_servant->pdo->prepare($sql);
  $statement1->execute(array($tops['post_id']));

  while ($top5 = $statement1->fetch()):
    # get top 5 posts
    if (($top5['post_photo'] != "") && ($top5['post_video'] == "")) {
      # POST CONTAINS PHOTO
  ?>
    <div class="thumbnail">
      <p>
        <a href="see_profile.php?user=<?php print($top5['p_username']); ?>">
          <?php printf("#%d %s", ++$counter, $top5['firstname']); ?>
        </a>
      </p>
      <a href="single_post.php?p_id=<?php print($tops['post_id']); ?>">
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
        <a href="see_profile.php?user=<?php print($top5['p_username']); ?>">
          <?php printf("#%d %s", ++$counter, $top5['firstname']); ?>
        </a>
      </p>
      <a href="single_post.php?p_id=<?php print($tops['post_id']); ?>">
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
          <a href="see_profile.php?user=<?php print($top5['p_username']); ?>">
            <?php printf("#%d %s", ++$counter, $top5['firstname']); ?>
          </a>
        </p>
        <p><?php printf("%s", $top5['post_text']) ?></p>
        <p><a href="single_post.php?p_id=<?php print($tops['post_id']); ?>">view</a></p>
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
<?php require_once 'js/script.php'; ?>

<!-- FOOTER -->
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
