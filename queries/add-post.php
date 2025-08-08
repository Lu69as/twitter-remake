<?php
    include "db-connection.php";
    if (!empty($_POST['textpost'])) {
        $query = "INSERT INTO posts VALUES ('". $_POST['textpost'] ."')";

        if ($conn->query($query) === true) header("Location: {$_SERVER['HTTP_REFERER']}");
        else echo "Error: " . $query . "<br>" . $conn->error;
    } 
    $conn->close();
    else header("Location: {$_SERVER['HTTP_REFERER']}");
    exit;