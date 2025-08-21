<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="apple-touch-icon" sizes="180x180" href="./img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="./img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="./img/favicon/favicon-16x16.png">
    <link rel="manifest" href="./img/favicon/site.webmanifest">

    <link rel="stylesheet" href="./style.css">
    <title>Blob</title>
</head>
<?php
    require_once "./queries/functions.php";
    $conn = getDBConnection();

    $queries = array();
    parse_str($_SERVER['QUERY_STRING'], $queries);
?>
<body>
    <section class="mainWidget">
        <?php
            if(!isset($_COOKIE["user"])) {
                $userIdListQuery = "SELECT userId FROM users";
                $userIdListResult = $conn->query($userIdListQuery);

                echo '<div class="login_select"><p class="userId_list">';
                if ($userIdListResult->num_rows > 0)
                    while($row = $userIdListResult->fetch_assoc()) { echo $row["userId"] . '|'; };

                echo '</p><div class="login_tabs">
                        <div class="sign_up">Sign up</div>
                        <div class="log_in" style="opacity:.7">Log in</div>
                    </div>
                    <form class="sign_up" action="./queries/log-in.php" method="post" ><div>
                        <div><label for="userId">User ID*</label>
                            <input required="yes" maxlength="30" type="text" name="userId" class="userId" placeholder="exampler123"></div>
                        <div><label for="password">Password*</label>
                            <input required="yes" maxlength="40" type="password" name="password" placeholder="Pass123"></div></div><div>
                        <div><label for="userName">Username*</label>
                            <input required="yes" maxlength="30" type="text" name="userName" placeholder="The Great Exampler"></div>
                        <div><label for="profilePic">Profile picture</label>
                            <input maxlength="255" type="text" name="profilePic" placeholder="https://example.com/profile.jpg"></div>
                        </div><label for="desc">Description</label><textarea name="desc" id="desc" 
                            maxlength="255" placeholder="I am a great example."></textarea>
                        <button class="invalid btn1" type="submit">Sign up!</button>
                    </form>
                    <form class="log_in" style="display:none" action="./queries/log-in.php" method="post"><div>
                        <div><label for="userId">User ID*</label>
                            <input required="yes" maxlength="30" type="text" name="userId" class="userId" placeholder="exampler123"></div>
                        <div><label for="password">Password*</label>
                            <input required="yes" maxlength="40" type="password" name="password" placeholder="Pass123"></div></div>
                        <button class="invalid btn1" type="submit">Sign up!</button><input type="hidden" name="log_in_formType" value="true">
                    </form>
                </div>';
            } else {
                $activeUserPFPQuery = "SELECT profilePic FROM users where userId = '".$_COOKIE["user"]."'";
                $activeUserPFPResult = $conn->query($activeUserPFPQuery);

                echo '<div class="post_creator">
                    <form action="./queries/add-post.php" method="post">
                        <div class="row1">
                            <a href="./profile/?user='.$_COOKIE["user"].'" class="profile" style="background-image:
                                url('.$activeUserPFPResult->fetch_assoc()["profilePic"].')"></a>
                            <input type="hidden" name="postuser" id="postuser" value="'. $_COOKIE["user"] .'">
                            <textarea name="textpost" id="textpost" maxlength="255"></textarea>
                            <button class="btn1" type="submit">Post</button>
                        </div>
                        <div class="row2">
                            <p class="characters"><span class="charUsed">0</span> / <span class="maxChar"></span></p>
                            <input class="blobs" placeholder="Add blobs to your post!">
                        </div>
                        <div class="row3 blobs_selected">
                            <input name="blobs_selected" type="hidden">
                        </div>
                    </form>
                </div>';
            } $sortBy = "posted"; $orderBy = "DESC";
            if (isset($queries["sort"])) $sortBy = $queries["sort"];
            if (isset($queries["order"])) $orderBy = $queries["order"];
        ?>

        <div class="posts_container">
            <nav>
                <h1>Blob - home</h1>
                <div class="sorting">
                    <button data-sort="comment_count"><?php echo file_get_contents('./img/icons/comment.svg') ?></button>
                    <button data-sort="likes_on_post"><?php echo file_get_contents('./img/icons/heart.svg') ?></button>
                    <button data-sort="posted"><?php echo file_get_contents('./img/icons/calendar.svg') ?></button>
                    <button class="orderBy <?php echo $orderBy ?>"><?php echo file_get_contents('./img/icons/arrow-up-down.svg') ?></button>
                </div>
            </nav>
            <?php
                echo "<script>document.querySelector(`.sorting [data-sort='".$sortBy."']`).style.opacity = '.8'</script>";
                echo "<script>document.querySelector(`.sorting .orderBy_".$orderBy."`).style.opacity = '.8'</script>";

                echo addPostsHtml("SELECT p.postId, p.text, p.posted, u.userId, u.userName, u.profilePic, (
                    SELECT COUNT(*) FROM likes l WHERE l.postId = p.postId AND l.commentId IS NULL ) AS likes_on_post, (
                    SELECT COUNT(*) FROM comments c WHERE c.postId = p.postId) AS comment_count, (
                    SELECT c.text FROM comments c LEFT JOIN likes l2 ON l2.commentId = c.commentId WHERE c.postId = p.postId
                    GROUP BY c.commentId ORDER BY COUNT(l2.commentId) DESC, c.posted ASC LIMIT 1 ) AS top_comment_text,
                    GROUP_CONCAT(b.blobId ORDER BY b.blobId SEPARATOR '|') AS blobs
                    FROM posts p JOIN users u ON p.userId = u.userId LEFT JOIN post_blobs pb ON p.postId = pb.postId
                    LEFT JOIN blobs b ON pb.blobId = b.blobId group by p.postId ORDER BY ".$sortBy." ".$orderBy, "./");
        ?></div>
    </section>
    <script src="./script.js"></script>
</body>
<?php $conn->close(); ?>
</html>