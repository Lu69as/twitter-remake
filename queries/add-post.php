<?php
    require_once "db-connect.php";
    $conn = getDBConnection();

    if (!empty($_POST['textpost'])) {
        $query = "insert into posts (text, userId) values ( '". $_POST['textpost'] ."', '". $_POST['postuser'] ."' )";
        if ($conn->query($query) === true) header("Location: {$_SERVER['HTTP_REFERER']}");
        else echo "Error: " . $query . "<br>" . $conn->error;
    }
    else header("Location: {$_SERVER['HTTP_REFERER']}");
    
    $conn->close();
    exit;