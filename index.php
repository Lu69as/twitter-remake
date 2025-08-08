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
?>
<body>
    <section class="mainWidget">
        <div class="post_creator">
            <form action="./queries/add-post.php" method="post">
                <a href="./profile/" class="profile"></a>
                <textarea name="textpost" id="textpost" maxlength="255"></textarea>
                <button type="submit">Post</button>
            </form>
            <p class="characters"><span class="charUsed">0</span> / <span class="maxChar"></span></p>
        </div>
        <div class="posts">
            <div class="post">
                <div class="profile_pic" style="background-image:url(https://lukasokken.com/img/me-noBg.png)"></div>
                <div class="content">
                    <p class="user">
                        <span class="name">Lu69as</span>
                        <span class="userId">lukasmolly</span>
                        <span class="date">15. July</span>
                    </p>
                    <p class="text">
                        I am ready to rumble
                    </p>
                </div>
            </div>
            <?php
                $query = "SELECT posts.postId, posts.text, posts.posted, posts.userId, users.name, users.profilePic
                    FROM posts 
                    JOIN users ON posts.userId = users.userId;";
                $result = $conn->query($query);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo '<div class="post" id="'.$row["postId"].'">
                            <div class="profile_pic" style="background-image:url('.$row["profilePic"].')"></div>
                            <div class="content">
                                <p class="user">
                                    <span class="name">'.$row["name"].'</span>
                                    <span class="userId">'.$row["userId"].'</span>
                                    <span class="date">'.$row["posted"].'</span>
                                </p>
                                <p class="text">'.str_replace("\n", "<br>", $row["text"]).'</p>
                            </div>
                        </div>';
                    };
                }
                else { echo "0 results"; }
            ?>
        </div>
    </section>
    <script src="./script.js"></script>
</body>
<?php
    $conn->close();
?>
</html>