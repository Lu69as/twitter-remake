<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="apple-touch-icon" sizes="180x180" href="../img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../img/favicon/favicon-16x16.png">
    <link rel="manifest" href="../img/favicon/site.webmanifest">
    <link rel="stylesheet" href="../style.css">
    <?php
        require_once "../queries/functions.php";
        $conn = getDBConnection();

        $queries = array();
        parse_str($_SERVER['QUERY_STRING'], $queries);
        if (!isset($queries["post"])) header("Location: ../");
        $pagePost = $queries["post"];
        
        // if(isset($_POST['log_out'])) {
        //     setcookie("user", "", time() - (86400 * 12), "/");
        //     header("Location: ../");
        //     exit;
        // };
        // if(isset($_POST['delete_user'])) {
        //     $conn->query("DELETE FROM likes WHERE postId IN (SELECT postId FROM posts WHERE userId='".$pageUser."') or userId='".$pageUser."';");
        //     $conn->query("DELETE from posts where userId = '". $pageUser ."';");
        //     $conn->query("DELETE from users where userId = '". $pageUser ."';");
        //     setcookie("user", "", time() - (86400 * 12), "/");
        //     header("Location: ../");
        //     exit;
        // };
    ?>
    <title><?php echo $pagePost ?></title>
</head>
<body>
    <section class="mainWidget">
        <a class="backBtn btn1" href="../">Back</a>
        <div class="mainPost"><?php
            echo addPostsHtml("SELECT p.postId, p.text, p.posted, u.userId, u.userName, u.profilePic, (
                SELECT COUNT(*) FROM likes l WHERE l.postId = p.postId AND l.commentId IS NULL ) AS likes_on_post, (
                SELECT COUNT(*) FROM comments c WHERE c.postId = p.postId) AS comment_count, (
                SELECT c.text FROM comments c LEFT JOIN likes l2 ON l2.commentId = c.commentId WHERE c.postId = p.postId
                GROUP BY c.commentId ORDER BY COUNT(l2.commentId) DESC, c.posted ASC LIMIT 1 ) AS top_comment_text,
                GROUP_CONCAT(b.blobId ORDER BY b.blobId SEPARATOR '|') AS blobs
                FROM posts p JOIN users u ON p.userId = u.userId LEFT JOIN post_blobs pb ON p.postId = pb.postId
                LEFT JOIN blobs b ON pb.blobId = b.blobId WHERE p.postId =".$pagePost." group by p.postId", "../");
        ?></div><?php
            if(isset($_COOKIE["user"])) {
                $activeUserPFPQuery = "SELECT profilePic FROM users where userId = '". $_COOKIE["user"] ."'";
                $activeUserPFPResult = $conn->query($activeUserPFPQuery);

                echo '<div class="post_creator comment_creator">
                    <form action="../queries/add-comment.php" method="post">
                        '. file_get_contents('../img/icons/down-right-arrow.svg') .'
                        <input type="hidden" name="commentuser" value="'.$_COOKIE["user"].'">
                        <input type="hidden" name="postid" value="'.$pagePost.'">
                        <textarea name="textpost" maxlength="255"></textarea>
                        <div class="btnTxt">
                            <button class="btn1" type="submit">Post</button>
                            <p class="characters"><span class="charUsed">0</span> / <span class="maxChar"></span></p>
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
                    <button data-sort="likes_count"><?php echo file_get_contents('../img/icons/heart.svg') ?></button>
                    <button data-sort="posted"><?php echo file_get_contents('../img/icons/calendar.svg') ?></button>
                    <button class="orderBy <?php echo $orderBy ?>"><?php echo file_get_contents('../img/icons/arrow-up-down.svg') ?></button>
                </div>
            </nav>
            <?php
                echo "<script>document.querySelector(`.sorting [data-sort='".$sortBy."']`).style.opacity = '.8';
                    document.querySelector(`.sorting .orderBy_".$orderBy."`).style.opacity = '.8'</script>";

                $query = "SELECT c.postId, c.commentId, c.text, c.userId, c.posted, u.userName, u.profilePic, COUNT(l.userId) AS likes_count
                    FROM comments c JOIN users u ON c.userId = u.userId LEFT JOIN likes l ON l.commentId = c.commentId
                    WHERE c.postId = ". $pagePost ." GROUP BY c.commentId ORDER BY ".$sortBy." ".$orderBy.";";
                $result = $conn->query($query);
                $output = "";
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $hasLiked = false;
                        
                        if (isset($_COOKIE["user"])) {
                            $hasLikedQuery = "select * from likes where commentId = ".$row["commentId"]." and userId = '".$_COOKIE["user"]."'";
                            $hasLikedResult = $conn->query($hasLikedQuery);
                            if (isset($hasLikedResult->fetch_assoc()["commentId"])) $hasLiked = true;
                        }

                        echo '<div class="comment" id="comment'.$row["commentId"].'">
                            <a class="profile_pic" href="../profile/?user='.$row["userId"].'" style="background-image:url('.$row["profilePic"].')"></a>
                            <div class="content">
                                <p class="user">
                                    <span class="userName">'.$row["userName"].'</span>
                                    <span class="userId">@'.$row["userId"].'</span>
                                    <span class="date"> • '.timeAgo($row["posted"]).'</span>
                                </p>
                                <p class="text">'.str_replace("\n", "<br>", $row["text"]).'</p>
                            </div>
                            <form action="../queries/add-like.php" method="post" class="interactions">
                                <input type="hidden" name="commentId" value="'.$row["commentId"].'">
                                <input type="hidden" name="postId" value="'.$row["postId"].'">
                                <input type="hidden" name="hasLiked" value="'.$hasLiked.'">
                                <button type="submit" name="likes" class="'.($hasLiked ? 'hasLiked' : '').'">'
                                    .file_get_contents('../img/icons/heart.svg').'<p>'.$row["likes_count"].'</p></button>
                            </form>
                        </div>';
                    };
                } else { echo '<p class="comment">There are no comments on this page for now</p>'; }
            ?>
        </div>        
    </section>
    <script src="../script.js"></script>
</body>
</html>