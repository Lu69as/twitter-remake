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

        if (!isset($queries["blob"])) header("Location: ../");
        $pageBlob = $queries["blob"];
    ?>
    <title>Blob - <?php echo $pageBlob ?></title>
</head>
<body>
    <section class="mainWidget">
        <a class="backBtn btn1" href="../">Back</a>
        <?php
            if(isset($_COOKIE["user"])) {
                $activeUserPFPQuery = "SELECT profilePic FROM users where userId = '".$_COOKIE["user"]."'";
                $activeUserPFPResult = $conn->query($activeUserPFPQuery);

                echo '<div class="post_creator blob_page">
                    <form action="../queries/add-post.php" method="post">
                        <a href="../profile/?user='.$_COOKIE["user"].'" class="profile" style="background-image:
                            url('.$activeUserPFPResult->fetch_assoc()["profilePic"].')"></a>
                        <input type="hidden" name="postuser" id="postuser" value="'. $_COOKIE["user"] .'">
                        <textarea name="textpost" id="textpost" maxlength="255" placeholder="Post to this blob"></textarea>
                        <div class="btnTxt">
                            <button class="btn1" type="submit">Post</button>
                            <p class="characters"><span class="charUsed">0</span> / <span class="maxChar"></span></p>
                        </div>
                        <input name="blobs_selected" type="hidden" value="'.$pageBlob.'">
                    </form>
                </div>';
            }
            $sortBy = "posted"; $orderBy = "DESC";
            if (isset($queries["sort"])) $sortBy = $queries["sort"];
            if (isset($queries["order"])) $orderBy = $queries["order"];
        ?>

        <div class="posts_container">
            <nav>
                <h1>Blob - <?php echo $pageBlob ?></h1>
                <div class="sorting">
                    <button data-sort="comment_count"><?php echo file_get_contents('../img/icons/comment.svg') ?></button>
                    <button data-sort="likes_on_post"><?php echo file_get_contents('../img/icons/heart.svg') ?></button>
                    <button data-sort="posted"><?php echo file_get_contents('../img/icons/calendar.svg') ?></button>
                    <button class="orderBy <?php echo $orderBy ?>"><?php echo file_get_contents('../img/icons/arrow-up-down.svg') ?></button>
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
                    LEFT JOIN blobs b ON pb.blobId = b.blobId WHERE EXISTS ( SELECT 1 FROM post_blobs pb2 JOIN blobs b2 ON pb2.blobId = b2.blobId 
                    WHERE pb2.postId = p.postId AND b2.blobId = '".$pageBlob."') GROUP BY p.postId ORDER BY ".$sortBy." ".$orderBy, "../");
            ?>
        </div>        
    </section>
    <script src="../script.js"></script>
</body>
</html>