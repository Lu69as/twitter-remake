<?php
    require_once "./functions.php";
    $conn = getDBConnection();

    if (!empty($_POST['textpost'])) {
        try {
            $post = $conn->prepare("INSERT INTO posts (text, userId) VALUES (?, ?)");
            $post->bind_param("ss", $_POST['textpost'], $_POST['postuser']);
            $post->execute(); $post->close();
            header("Location: ../");
        }
        catch (mysqli_sql_exception $e) {
            error_log($e->getMessage());
            echo "Something went wrong.";
            echo '<br><a href="../">Return to homepage</a>';
        }
    }
    else header("Location: ../");
    exit;