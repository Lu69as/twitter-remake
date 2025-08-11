<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Twitter Remake</title>
</head>
<?php
    require_once __DIR__ . "/queries/db-connect.php";
    $conn = getDBConnection();

    // setcookie("user", "lu69as", time() - (86400 * 12), "/");
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
                        <button class="invalid" type="submit">Sign up!</button>
                    </form>
                    <form class="log_in" style="display:none" action="./queries/log-in.php" method="post"><div>
                        <div><label for="userId">User ID*</label>
                            <input required="yes" maxlength="30" type="text" name="userId" class="userId" placeholder="exampler123"></div>
                        <div><label for="password">Password*</label>
                            <input required="yes" maxlength="40" type="password" name="password" placeholder="Pass123"></div></div>
                        <button class="invalid" type="submit">Sign up!</button><input type="hidden" name="log_in_formType" value="true">
                    </form>
                </div>';
            } else {
                $activeUserPFPQuery = "SELECT profilePic FROM users where userId = '". $_COOKIE["user"] ."'";
                $activeUserPFPResult = $conn->query($activeUserPFPQuery);

                echo '<div class="post_creator">
                    <form action="./queries/add-post.php" method="post">
                        <a href="./profile/" class="profile" style="background-image:url('. $activeUserPFPResult->fetch_assoc()["profilePic"] .')"></a>
                        <input type="hidden" name="postuser" id="postuser" value="'. $_COOKIE["user"] .'">
                        <textarea name="textpost" id="textpost" maxlength="255"></textarea>
                        <button type="submit">Post</button>
                    </form>
                    <p class="characters"><span class="charUsed">0</span> / <span class="maxChar"></span></p>
                </div>';
            }
        ?>

        <div class="posts"><?php
            $mainPostsQuery = "SELECT posts.postId, posts.text, posts.posted, posts.userId, users.userName, users.profilePic
                FROM posts JOIN users ON posts.userId = users.userId;";
            $mainPostsResult = $conn->query($mainPostsQuery);

            if ($mainPostsResult->num_rows > 0) {
                while($row = $mainPostsResult->fetch_assoc()) {
                    echo '<div class="post" id="'.$row["postId"].'">
                        <div class="profile_pic" style="background-image:url('.$row["profilePic"].')"></div>
                        <div class="content">
                            <p class="user">
                                <span class="userName">'.$row["userName"].'</span>
                                <span class="userId">'.$row["userId"].'</span>
                                <span class="date">'.$row["posted"].'</span>
                            </p>
                            <p class="text">'.str_replace("\n", "<br>", $row["text"]).'</p>
                        </div>
                    </div>';
                };
            } else { echo "0 results"; }
        ?></div>
    </section>
    <script src="./script.js"></script>
</body>
<?php $conn->close(); ?>
</html>