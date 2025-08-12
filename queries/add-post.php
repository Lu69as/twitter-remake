<?php
    require_once "./functions.php";
    $conn = getDBConnection();

    if (!empty($_POST['textpost'])) {
        try {
            $post = $conn->prepare("INSERT INTO posts (text, userId) VALUES (?, ?)");
            $post->bind_param("ss", $_POST['textpost'], $_POST['postuser']);
            $post->execute(); $post->close();

            $like = $conn->prepare("INSERT INTO likes VALUES (?, ?)");
            $like->bind_param("ss", $_POST['textpost'], $_POST['postuser']);
            $like->execute(); $like->close();
            header("Location: {$_SERVER['HTTP_REFERER']}");
        }
        catch (mysqli_sql_exception $e) {
            error_log($e->getMessage());
            echo "Something went wrong.";
            echo '<br><a href="'.$_SERVER['HTTP_REFERER'].'">Return to homepage</a>';
        }
    }
    else header("Location: {$_SERVER['HTTP_REFERER']}");
    exit;