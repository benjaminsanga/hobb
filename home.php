<?php 
require_once 'codesPHP/data.php';

# increment log out page clicks
# get current clicks
$s=$data_servant->pdo->prepare("SELECT clicks FROM pages WHERE page = 'home' LIMIT 1");
if ($s->execute()) {
  if ($r=$s->fetch()) {
    $st=$data_servant->pdo->prepare("UPDATE pages SET clicks = ".++$r['clicks']." WHERE page = 'home'");
    $st->execute();
  }
}

# check for the cookie user
if ((isset($_COOKIE['user']) && !empty($_COOKIE['user'])) || isset($_COOKIE['fb_user'])) {
  
  if (isset($_COOKIE['fb_user'])) { $username = $_COOKIE['fb_user']; }

  # check if page should be refreshed
  if (isset($_GET['ref'])) {
    # refresh page
    $refresh_value = htmlspecialchars($_GET['ref']);
    header('location: home.php#'.$refresh_value);
  }

  if (isset($_COOKIE['user'])) {
    $username = $_COOKIE['user'];
    # get user's details
    $params = array('firstname', 'lastname', 'profile_pic');
    $table = "hobb_people";
    $condition = "email='" . $_COOKIE['user'] . "' LIMIT 1";
    $user = $data_servant->select($params, $table, $condition);
  }

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
        
      </div><hr>
      <?php
      # if not logged in through facebook
      if (isset($_COOKIE['user']) && !isset($_COOKIE['fb_user'])) {
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
      <p><img src="icons/noty-icon.png" width="20" height="relative" title="notifications"></p>
      <div class="hobbers-tab-pane text-left">
        <div id="noty-feed" class="noty-feed">
        </div>      
      </div>

      <div class="alert alert-success fade in">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
        <p><strong>Ads!</strong></p>
        Get people to know you
      </div>
        <?php
      }
      ?>
      
    </div>

    <!-- FEEDS DIV -->
    <div class="col-sm-7"> 
      <div class="row">
        <div class="col-sm-12 text-center">
          <div>
            <h5>Feeds</h5>
          </div>
        </div>
      </div>

<?php
$sql = "";
$sort = "";

if (isset($_GET['sort'])) {
  $sort = htmlspecialchars(trim(stripslashes($_GET['sort'])));
  # sort by the get variable
  $sql = "SELECT * FROM `posts` WHERE `post_text` LIKE  '%{$sort}%' ORDER BY `post_id` DESC LIMIT 10"; // select posts
} else {
  $sql = "SELECT * FROM `posts` ORDER BY `post_id` DESC LIMIT 20"; // select posts
}

$data_servant->pdo->quote($sql);
$stmt = $data_servant->pdo->prepare($sql);
$stmt->execute();
while ($row = $stmt->fetch()) {

  # get this user's details
  $stmt2 = $data_servant->pdo->prepare("SELECT * FROM `hobb_people` WHERE `email` = ? LIMIT 1");
  $stmt2->execute(array($row['p_username']));
  $thisUser = $stmt2->fetchAll();

  # get reactions on post
  $stmt3 = $data_servant->pdo->prepare("SELECT *, count(p_username) AS like_occurence FROM post_reactions WHERE post_id = ? LIMIT 1");
  $stmt3->execute(array($row['post_id']));
  $reactions = $stmt3->fetch();

  # get comments on post
  $comments = array();
  $stmt4 = $data_servant->pdo->prepare("SELECT hobb_people.firstname, hobb_people.lastname, hobb_people.email, post_comments.comment FROM post_comments INNER JOIN hobb_people ON post_comments.p_username=hobb_people.email WHERE `post_id` = ? ORDER BY post_comments.pc_id ASC LIMIT 3");
  $stmt4->execute(array($row['post_id']));
  while($com = $stmt4->fetch()) {
    $comments[] = $com;
  }

  #check if current user is connected to the post creator
  $connected_people = array();
  $stmt5 = $data_servant->pdo->prepare("SELECT `first_username`, `second_username` FROM `connected_people` WHERE `first_username` = ? OR `second_username` = ?");
  $stmt5->execute(array($row['p_username'], $row['p_username']));

  while ($thisGuy = $stmt5->fetch()) {
    # join people connected to the connected people array
    $connected_people[] = $thisGuy['first_username'];
    $connected_people[] = $thisGuy['second_username'];
  }

  # get all users that like this post
  $users_likes = array();
  $stmt6 = $data_servant->pdo->prepare("SELECT p_username FROM post_reactions WHERE post_id = ?");
  $stmt6->execute(array($row['post_id']));
  while ($likes = $stmt6->fetch()) {
    # populate the users likes array
    $users_likes[] = $likes['p_username'];
  }
?>
<div class="row">
    <div class="col-sm-12">
      <div class="well">
        <div class="text-left">
          <p>
            <span>
              <a href="see_profile.php?user=<?php print($row['p_username']); ?>">
                <img src="<?php printf("profile_photos/%s", $thisUser[0]['profile_pic']); ?>" width=30 height=30 >&nbsp;
                <?php printf("%s %s", $thisUser[0]['firstname'], $thisUser[0]['lastname']); ?>
              </a>
              . <span style="font-size: 12px;"><?php print $row['time_of_post']; ?></span>
            </span>
            <?php
               if (!in_array($username, $connected_people) && !empty($_COOKIE['user'])) {
                  # this user is not friends with poster and this user is logged in thru hobb
                ?>
                  <button id="add-<?php print($row['post_id']); ?>" class="btn btn-default btn-md" 
                  onclick="addFriend(<?php print("'{$username}', '{$thisUser[0]['email']}', '{$row['post_id']}')"); ?>" 
                  style="float: right;"><img src="icons/add-hobb.png" width="15" height="relative"><span style="font-size: 12px;"> hobb</span></button>
                <?php
               }
            ?>
          </p>
        </div>
<?php if (($row['post_photo'] != "") && ($row['post_text'] == "") && ($row['post_video'] == "")) {
    # POST CONTAINS PHOTO ONLY
?>
        <div class="row" id="<?php printf("div-%s", $row['post_id']); ?>" >
          <div class="col-sm-12">
            <?php
            if (in_array($username, $users_likes)) {
              # do not allow double click like
            ?>
              <img src="images/<?php print $row['post_photo']; ?>" width="80%" height="" >
            <?php
            } else {
              # allow double click like
            ?>
              <img src="images/<?php print $row['post_photo']; ?>" width="80%" height="" 
              ondblclick="initLike(<?php print("'{$row['post_id']}', '{$username}'"); ?>)" 
              id="pic-<?php print($row['post_id']); ?>" >
            <?php
            }
            ?>
          </div>
        </div>
<?php
  } elseif (($row['post_photo'] != "") && ($row['post_text'] != "") && ($row['post_video'] == "")) {
    # POST CONTAINS PHOTO AND TEXT ONLY
?>
        <div class="row" id="<?php printf("div-%s", $row['post_id']); ?>" >
          <div class="col-sm-7">
            <img src="images/<?php print $row['post_photo']; ?>" width="100%" height="100%">
          </div>
          
          <div class="col-sm-5">
          <p class="post-text">
            <?php printf("%s", $row['post_text']); ?>
          </p>
          </div>
        </div>
<?php
  } elseif (($row['post_video'] != "") && ($row['post_text'] == "") && ($row['post_photo'] == "")) {
    # POST CONTAINS A VIDEO ONLY
?>
        <div class="row" id="<?php printf("div-%s", $row['post_id']); ?>" >
          <div class="col-sm-12">
            <video width="80%" height="relative" controls >
              <source src="videos/<?php printf("%s", $row['post_video']); ?>" type="video/mp4">
              <source src="videos/<?php printf("%s", $row['post_video']); ?>" type="video/ogg">
              Your browser does not support the video tag.
            </video>
          </div>
        </div>
<?php
  } elseif (($row['post_video'] != "") && ($row['post_text'] != "") && ($row['post_photo'] == "")) {
    # POST CONTAINS VIDEO AND TEXT ONLY
?>
        <div class="row" id="<?php printf("div-%s", $row['post_id']); ?>" >
          <div class="col-sm-7">
            <video width="100%" height="relative" controls >;
              <source src="videos/<?php printf("%s", $row['post_video']); ?>" type="video/mp4">
              <source src="videos/<?php printf("%s", $row['post_video']); ?>" type="video/ogg">
              Your browser does not support the video tag.
            </video>
          </div>
          
          <div class="col-sm-5">
          <p class="post-text">
            <?php printf("%s", $row['post_text']); ?>
          </p>
          </div>
        </div>
<?php
  } elseif (($row['post_text'] != "") && ($row['post_photo'] == "") && ($row['post_video'] == "")) {
    # POST CONTAINS TEXT ONLY
?>
        <div class="row" id="<?php printf("div-%s", $row['post_id']); ?>" >
          <div class="col-sm-12">
            <p class="post-text">
              <?php printf("%s", $row['post_text']); ?>
              <br>
            </p>
          </div>
        </div>
<?php
}
?>
        <hr><div class="text-left">
          <p>
            <span id="num_likes_<?php print($row['post_id']); ?>" style="font-size: 10px;">
              <?php printf("%s", $reactions['like_occurence']); ?>
            </span> 

            <span style="">.</span> 

            <!-- LIKE BUTTON -->
            <?php
            if (in_array($username, $users_likes)) {
              # show green thumb
            ?>
              <img src="icons/greenthumb.png" 
              id="up-<?php print($row['post_id']); ?>" 
              width="20" height="20" title="Like">&nbsp;&nbsp;
            <?php
            } else {
              // show gray thumb
            ?>
              <img src="icons/thumbs-up.png" 
              id="up-<?php print($row['post_id']); ?>" 
              onclick="thumbsUp(<?php print("'{$row['post_id']}', '{$username}'"); ?>)" 
              width="20" height="20">&nbsp;&nbsp;
            <?php
            }
            ?>

            <!-- THE COMMENT BUTTON -->
            <img src="icons/comment.png" width="20" height="20"  id="com-<?php print($row['post_id']); ?>"
            onclick="writeComment(<?php print("'{$row['post_id']}'"); ?>)" title="Comment" />&nbsp;|&nbsp;
            
            <script type="text/javascript">
              function writeComment(postId) {
                document.getElementById("one_comment_"+postId).innerHTML = "";
                // show 2 latest comments and the text area for commenting
                document.getElementById("commenting-"+postId).style.display = 'block';
              }
            </script>

            <!-- THE ONE COMMENT -->
            <span id="one_comment_<?php print($row['post_id']); ?>" class="small-text">
              <?php
              if (count($comments) > 0) {
                printf("<a href='see_profile.php?user=%s'>%s %s</a>: %s...", $comments[0]['email'], $comments[0]['firstname'], $comments[0]['lastname'], substr($comments[0]['comment'], 0, 60)); 
              }
              ?>     
            </span>
            <a href="single_post.php?p_id=<?php print($row['post_id']); ?>" style="float: right;" title="More">
              <img src="icons/more.png" width="18" height="relative">
            </a>
          </p>

          <!-- THE OTHER COMMENTS -->
          <div id="commenting-<?php print($row['post_id']); ?>" class="row" style="display: none;">
            <hr>
            <!-- use commenting-postId to identify this div specifically -->
            <div class="col-sm-12">
              <?php
              $otherComments = "";
              $counter=0;
              while($counter < count($comments) && $counter < 2) {
                $otherComments .= "<p class='small-text'><a href='see_profile.php?user={$comments[$counter]['email']}'>{$comments[$counter]['firstname']} {$comments[$counter]['lastname']}</a>: {$comments[$counter]['comment']}</p>";
                ++$counter;
              }
            
              print($otherComments);
              ?>

              <!-- NEW COMMENT -->
              <div id="new_comment_<?php print($row['post_id']); ?>"></div>

              <input style="width: 80%;font-size: 15px;padding: 5px;" type="text" 
              id="comment_box_<?php print($row['post_id']); ?>" placeholder="say something about this post...">

              <button onclick="commentOnPost(<?php print("'{$row['post_id']}', '{$username}'"); ?>)" class="btn btn-default btn-sm" id="comment">Comment</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php
} // end of while for $stmt, fetch posts
?> 

<!-- LIKE BUTTON -->
<!--div style="display: none;" id="like-div"-->
  <img src="icons/thumbs-up.png" width="0" id="like-btn" style="display: none;" >
<!--/div-->
       
      <div class="row">
        <div class="col-sm-12 text-center">
            <!--i class="fa fa-spinner fa-spin"></i-->
            <img src="icons/ajax-loader.png" width="50" height="relative">
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
<?php include_once 'js/script.php'; ?>

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
