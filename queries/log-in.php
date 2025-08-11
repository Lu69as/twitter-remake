<?php
    require_once "db-connect.php";
    $conn = getDBConnection();

    if (empty($_POST['log_in_formType'])) {
        $query = "insert into users values ('". $_POST['userId'] ."','". $_POST['password'] ."','". $_POST['userName'] ."','". 
            (strlen($_POST['desc']) > 0 ? $_POST['desc'] : 'Beskrivelser er vanskelige...') ."','".
            (strlen($_POST['profilePic']) > 0 ? $_POST['profilePic'] : __DIR__ . "/img/Default_pfp.svg") ."')";
    
        if ($conn->query($query) === true) {
            setcookie("user", $_POST['userId'], time() + (86400 * 12), "/");
            header("Location: {$_SERVER['HTTP_REFERER']}");
        }
        else {
            echo "Error: " . $query . "<br>" . $conn->error;
            echo '<br><a href="'.$_SERVER['HTTP_REFERER'].'">Return to homepage</a>';
        }
    }
    else {
        $mainPostsQuery = "SELECT * from users where userID = '".$_POST['userId']."' and password = '".$_POST['password']."'";
        $mainPostsResult = $conn->query($mainPostsQuery);

        if ($mainPostsResult->num_rows > 0) {
            setcookie("user", $_POST['userId'], time() + (86400 * 12), "/");
            header("Location: {$_SERVER['HTTP_REFERER']}");
        }
        else { 
            echo "Username or password is incorrect, try again";
            echo '<br><a href="'.$_SERVER['HTTP_REFERER'].'">Return to homepage</a>';
        }
    }

    $conn->close();
    exit;