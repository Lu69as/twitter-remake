<?php
    require_once "./functions.php";
    $conn = getDBConnection();

    if(isset($_POST['likes'])) {
        if(isset($_COOKIE["user"])) {
            if(empty($_POST['hasLiked'])) {
                try { $stmt;
                    if (isset($_POST['commentId'])) {
                        $stmt = $conn->prepare("INSERT INTO likes (postId, commentId, userId) VALUES (?, ?, ?)");
                        $stmt->bind_param("sss", $_POST['postId'], $_POST['commentId'], $_COOKIE["user"]);
                    } else {
                        $stmt = $conn->prepare("INSERT INTO likes (postId, userId) VALUES (?, ?)");
                        $stmt->bind_param("ss", $_POST['postId'], $_COOKIE["user"]);
                    } 
                    $stmt->execute(); $stmt->close();
                    header("Location: ".$_SERVER['HTTP_REFERER']);
                }
                catch (mysqli_sql_exception $e) {
                    error_log($e->getMessage());
                    echo "Something went wrong: " . $e->getMessage();
                }
            }
            else {
                if (!isset($_POST['commentId']))
                    $conn->query("DELETE from likes where postId=".$_POST['postId']." and userId='".$_COOKIE["user"]."';");
                else
                    $conn->query("DELETE from likes where postId=".$_POST['postId']." and commentId=".$_POST['commentId']." and userId='".$_COOKIE["user"]."';");
                header("Location: ".$_SERVER['HTTP_REFERER']);
            }
        }
        else header("Location: ".$_SERVER['HTTP_REFERER']); exit;
    }
    else header("Location: ".$_SERVER['HTTP_REFERER']); exit;