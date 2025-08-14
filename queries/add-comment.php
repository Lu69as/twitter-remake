<?php
    require_once "./functions.php";
    $conn = getDBConnection();

    if (!empty($_POST['textpost'])) {
        try {
            $post = $conn->prepare("INSERT INTO comments (text, postId, userId) VALUES (?, ?, ?)");
            $post->bind_param("sss", $_POST['textpost'], $_POST['postid'], $_POST['commentuser']);
            $post->execute(); $post->close();
            header("Location: ".$_SERVER['HTTP_REFERER']);
        }
        catch (mysqli_sql_exception $e) {
            error_log($e->getMessage());
            echo "Something went wrong.";
            echo '<br><a href="../">Return to homepage</a>';
        }
    }
    else header("Location: ../");
    exit;