<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <?php
        require_once "../queries/db-connect.php";
        $conn = getDBConnection();

        $queries = array();
        parse_str($_SERVER['QUERY_STRING'], $queries);

        if (!isset($_COOKIE["user"]) && count($queries) < 1) header("Location: ../");
        $pageUser = count($queries) >= 1 ? $queries["user"] : $_COOKIE["user"];
        
    ?>
    <title><?php echo $pageUser ?></title>
</head>
<body>
    <section class="mainWidget">
        <a class="backBtn btn1" href="../">Back</a>
        <?php
            $pageUserQuery = "SELECT userName, created, description, profilePic
                FROM users where userId = '". $pageUser ."'";
            $pageUserResult = $conn->query($pageUserQuery);
            
            while($row = $pageUserResult->fetch_assoc()) {
                $createdDate = date_parse($row["created"]);
                echo '<div class="profilePage">
                    <div class="pfp" style="background-image:url('. $row["profilePic"] .')"></div>
                    <h1 class="userName">'. $row["userName"] .'<span class="userId"> • '. $pageUser .'</span></h1>
                    <h3 class="userCreated">Started Blobbing™️<span>'. date("F", strtotime($row["created"]))
                        .' '. date("Y", strtotime($row["created"])) .'</span></h3>
                    <p class="description">'. $row["description"] .'</p>
                </div>';
            }; 
        ?>

        <div class="posts"><?php
            echo addPostsHtml("SELECT posts.postId, posts.text, posts.posted, posts.userId, users.userName, users.profilePic
                FROM posts JOIN users ON posts.userId = users.userId WHERE users.userId = '" . $pageUser . "';");
        ?></div>

        <?php
            if(isset($_POST['log_out'])) {
                setcookie("user", "", time() - (86400 * 12), "/");
                header("Location: ../");
            }
            if(isset($_POST['delete_user'])) {
                $conn->query("delete from posts where userId = '". $pageUser ."';");
                $conn->query("delete from users where userId = '". $pageUser ."';");
                setcookie("user", "", time() - (86400 * 12), "/");
                header("Location: ../");
            }
            if (isset($_COOKIE["user"]) && $pageUser == $_COOKIE["user"]) {
                echo '<form method="post" class="userAdminBtns">
                    <input class="btn1 sign_out" type="submit" name="log_out" value="Sign Out"/>
                    <input class="btn1 delete" type="submit" name="delete_user" value="Delete User"/>
                </form>';
            }
        ?>
        
    </section>
    <script src="../script.js"></script>
</body>
</html>