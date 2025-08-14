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

        if (!isset($_COOKIE["user"]) && !isset($queries["user"])) header("Location: ../");
        $pageUser = isset($queries["user"]) ? $queries["user"] : $_COOKIE["user"];
        
        if(isset($_POST['log_out'])) {
            setcookie("user", "", time() - (86400 * 12), "/");
            header("Location: ../");
            exit;
        };
        if(isset($_POST['delete_user'])) {
            $conn->query("DELETE FROM likes WHERE postId IN (SELECT postId FROM posts WHERE userId='".$pageUser."') or userId='".$pageUser."';");
            $conn->query("DELETE FROM comments where userId='". $pageUser ."';");
            $conn->query("DELETE from posts where userId='". $pageUser ."';");
            $conn->query("DELETE from users where userId='". $pageUser ."';");
            setcookie("user", "", time() - (86400 * 12), "/");
            header("Location: ../");
            exit;
        };
    ?>
    <title><?php echo $pageUser ?></title>
</head>
<body>
    <section class="mainWidget">
        <a class="backBtn btn1" href="../">Back</a>
        <?php
            $pageUserQuery = "SELECT userName, created, description, profilePic
                FROM users where userId='". $pageUser ."'";
            $pageUserResult = $conn->query($pageUserQuery);
            
            while($row = $pageUserResult->fetch_assoc()) {
                $createdDate = date_parse($row["created"]);
                echo '<div class="profilePage">
                    <div class="pfp" style="background-image:url('. $row["profilePic"] .')"></div>
                    <h1 class="userName">'. $row["userName"] .'<span class="userId"> • @'. $pageUser .'</span></h1>
                    <h3 class="userCreated">Started Blobbing™️<span>'. date("F", strtotime($row["created"]))
                        .' '. date("Y", strtotime($row["created"])) .'</span></h3>
                    <p class="description">'. $row["description"] .'</p>
                </div>';
            }; $sortBy = "posted"; $orderBy = "DESC";
            if (isset($queries["sort"])) $sortBy = $queries["sort"];
            if (isset($queries["order"])) $orderBy = $queries["order"];
        ?>

        <div class="posts_container">
            <nav>
                <h1>Blob - home</h1>
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
                    SELECT COUNT(*) FROM likes l WHERE l.postId=p.postId AND l.commentId IS NULL ) AS likes_on_post, (
                    SELECT COUNT(*) FROM comments c WHERE c.postId=p.postId) AS comment_count, (
                    SELECT c.text FROM comments c LEFT JOIN likes l2 ON l2.commentId=c.commentId WHERE c.postId=p.postId
                    GROUP BY c.commentId ORDER BY COUNT(l2.commentId) DESC, c.posted ASC LIMIT 1 ) AS top_comment_text
                    FROM posts p JOIN users u ON p.userId=u.userId WHERE u.userId='".$pageUser."' ORDER BY ".$sortBy." ".$orderBy.";", "../");
            ?>
        </div>

        <?php
            if (isset($_COOKIE["user"]) && $pageUser == $_COOKIE["user"]) {
                echo '<form method="post" class="userAdminBtns">
                    <input class="btn1 sign_out" type="submit" name="log_out" value="Sign Out"/>
                    <input class="btn1 delete" type="submit" name="delete_user" value="Delete User"/>
                </form>';
            };
        ?>
        
    </section>
    <script src="../script.js"></script>
</body>
</html>