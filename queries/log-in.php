<?php
    require_once "./functions.php";
    $conn = getDBConnection();

    if (empty($_POST['log_in_formType'])) {
        try {
            $dsc = strlen($_POST['desc']) > 0 ? $_POST['desc'] : 'Descriptions are hard...';
            $pfp = strlen($_POST['profilePic']) > 0 ? $_POST['profilePic'] : "https://upload.wikimedia.org/wikipedia/commons/a/ac/Default_pfp.jpg";

            $stmt = $conn->prepare("INSERT INTO users (userId, password, userName, description, profilePic) VALUES (?,?,?,?,?)");
            $stmt->bind_param("sssss", $_POST['userId'], $_POST['password'], $_POST['userName'], $dsc, $pfp);
            $stmt->execute(); $stmt->close();
            setcookie("user", $_POST['userId'], time() + (86400 * 12), "/");
            header("Location: ../");
        }
        catch (mysqli_sql_exception $e) {
            error_log($e->getMessage());
            echo "Something went wrong." . $e->getMessage();
            echo '<br><a href="../">Return to homepage</a>';
        }
    }
    else {
        $mainPostsQuery = "SELECT * from users where userID = '".$_POST['userId']."' and password = '".$_POST['password']."'";
        $mainPostsResult = $conn->query($mainPostsQuery);

        if ($mainPostsResult->num_rows > 0) {
            setcookie("user", $_POST['userId'], time() + (86400 * 12), "/");
            header("Location: ../");
        }
        else { 
            echo "Username or password is incorrect, try again";
            echo '<br><a href="../">Return to homepage</a>';
        }
    }

    $conn->close();
    exit;