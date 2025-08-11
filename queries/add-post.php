<?php
    require_once "db-connect.php";
    $conn = getDBConnection();

    if (!empty($_POST['textpost'])) {
        try {
            $stmt = $conn->prepare("INSERT INTO posts (text, userId) VALUES (?, ?)");
            $stmt->bind_param("ss", $_POST['textpost'], $_POST['postuser']);
            $stmt->execute(); $stmt->close();
            header("Location: {$_SERVER['HTTP_REFERER']}");
        }
        catch (mysqli_sql_exception $e) {
            error_log($e->getMessage());
            echo "Something went wrong.";
            echo '<br><a href="'.$_SERVER['HTTP_REFERER'].'">Return to homepage</a>';
        }
    }
    else header("Location: {$_SERVER['HTTP_REFERER']}");
    
    $conn->close();
    exit;