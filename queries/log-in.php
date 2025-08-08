<?php
    require_once "db-connect.php";
    $conn = getDBConnection();

    if (!empty($_POST['userId'])) {
        $query = "insert into users values ('". $_POST['userId'] ."','". $_POST['password'] ."','". $_POST['userName'] ."','". 
            (strlen($_POST['desc']) > 0 ? $_POST['desc'] : 'Beskrivelser er vanskelige...') ."','".
            (strlen($_POST['profilePic']) > 0 ? $_POST['profilePic'] : __DIR__ . "/img/Default_pfp.svg") ."')";

        if ($conn->query($query) === true) {
            setcookie("user", $_POST['userId'], time() + (86400 * 12), "/");
            header("Location: {$_SERVER['HTTP_REFERER']}");
        }
        else echo "Error: " . $query . "<br>" . $conn->error;
    }
    else header("Location: {$_SERVER['HTTP_REFERER']}");

    $conn->close();
    exit;