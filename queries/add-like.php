<?php
    require_once "./functions.php";
    $conn = getDBConnection();

    if(isset($_POST['likes'])) {
        if(isset($_COOKIE["user"])) {
            if(empty($_POST['hasLiked'])) {
                try {
                    $stmt = $conn->prepare("INSERT INTO likes VALUES (?, ?)");
                    $stmt->bind_param("ss", $_POST['postId'], $_COOKIE["user"]);
                    $stmt->execute(); $stmt->close();
                }
                catch (mysqli_sql_exception $e) {
                    error_log($e->getMessage());
                    echo "Something went wrong: " . $e->getMessage();
                }
            }
            else $conn->query("delete from likes where postId = ".$_POST['postId']." and userId = '".$_COOKIE["user"]."'");
        }
    }
    if(isset($_POST['comments'])) {
        echo 'commented post';
    }
    else header("Location: {$_SERVER['HTTP_REFERER']}");
    exit;