<?php 
require_once 'codesPHP/data.php'; 

# increment log out page clicks
# get current clicks
$s=$data_servant->pdo->prepare("SELECT clicks FROM pages WHERE page = 'explore' LIMIT 1");
if ($s->execute()) {
  if ($r=$s->fetch()) {
    $st=$data_servant->pdo->prepare("UPDATE pages SET clicks = ".++$r['clicks']." WHERE page = 'explore'");
    $st->execute();
  }
}

# check for the cookie user
if ((isset($_COOKIE['user']) && !empty($_COOKIE['user'])) || isset($_COOKIE['fb_user'])) {
  $username = isset($_COOKIE['user']) ? $_COOKIE['user'] : $_COOKIE['fb_user'];

} else {
  # redirect to index page if cookie not set
  header('location: index.php');
}
?>

<?php require_once 'codesPHP/head.php'; ?>

<style type="text/css">
  a {
    color: black;
  }
  a:hover {
    text-decoration: none;
    color: #474e5d;
  }
</style>

<!-- blank thumb is for unliked post, black thumb is for showing number of post, green thumb is for like post -->
<div class="container text-center main">    
  <p class="margin">Explore</p>
  

<?php
  # get posts
  $sql = "SELECT * FROM posts ORDER BY post_id DESC LIMIT 9 ";
  $statement = $data_servant->pdo->prepare($sql);
  $statement->execute();
  $lastRow = 0;
  $counter = 0;
  
  while ($post = $statement->fetch()) {
    # get user's details
    $sql1 = "SELECT hobb_people.firstname, hobb_people.lastname FROM hobb_people WHERE hobb_people.email = ?";
    $statement1 = $data_servant->pdo->prepare($sql1);
    $statement1->execute(array($post['p_username']));

    while ($postInfo = $statement1->fetch()) {
      # get number of likes for this post
      $stmt = $data_servant->pdo->prepare("SELECT count(post_reactions.p_username) FROM post_reactions WHERE post_reactions.post_id = ?");
      $stmt->execute(array($post['post_id']));
      while ($reactions = $stmt->fetch()) {
        # diplay posts
        if ((++$counter % 4) == 0) {
            ?>
            <div class="row">
            <?php
          }

            ?>
                <div class="col-sm-3">
                  <div class="thumbnail">
                    <h5>
                      <a href="see_profile.php?user=<?php print($post['p_username']); ?>">
                        <?php printf("%s %s", $postInfo['firstname'], $postInfo['lastname']); ?>
                      </a>
                    </h5><hr>
            <?php
              if (($post['post_video'] != "") && ($post['post_photo'] == "")) {
                # video and/or text only
            ?>
                    <a href="single_post.php?p_id=<?php print($post['post_id']); ?>">
                      <video width="100%" height="relative" controls >;
                        <source src="videos/<?php printf("%s", $post['post_video']); ?>" type="video/mp4">
                        <source src="videos/<?php printf("%s", $post['post_video']); ?>" type="video/ogg">
                        Your browser does not support the video tag.
                      </video>
                    </a>
            <?php
            } elseif (($post['post_photo'] != "") && ($post['post_video'] == "")) {
              # photo and/or text only
            ?>
                    <a href="single_post.php?p_id=<?php print($post['post_id']); ?>">
                      <img src="images/<?php printf("%s", $post['post_photo']) ?>" alt="" width="100%" height="100%">
                    </a>
            <?php
            } elseif (($post['post_text']) != "" && ($post['post_photo'] == "") && ($post['post_video'] == "")) {
              # text only
            ?>
                    <p><?php printf("%s", $post['post_text']) ?></p>
            <?php
            } // end of checking file type
            ?>
                    <hr>
                    <p style="font-size: 12px;">
                      <img src="icons/greenthumb.png" width="20" height="20">
                      <sup> . </sup><?php printf("%s", $reactions['count(post_reactions.p_username)']); ?>
                    </p>
                  </div>  
                </div>
                <div class="col-sm-.5"></div>
            <?php
            if (($counter % 4) == 0) {
            ?>
            </div>
            <?php
          }
        $lastRow = (int)$post['post_id'];
      } # eof reactions llop
  } # eof post info loop
} # eof get posts
?>
  </div>
</div>

<!-- JAVASCRIPT -->
<?php include_once 'js/script.php'; ?>

  <!--DIV TO SHOW GOVERNOR OF THE MONTH, BEST POST OF THE WEEK...-->

<?php require_once 'codesPHP/foot.php'; ?>
