<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <?php
        require_once "../queries/db-connect.php";
        $conn = getDBConnection();

        $queries = array();
        parse_str($_SERVER['QUERY_STRING'], $queries);
        $pageUser = count($queries) >= 1 ? $queries["user"] : $_COOKIE["user"];
    ?>
    <title><?php echo $pageUser ?></title>
</head>
<body>
    <section class="mainWidget">
        <?php
            $pageUserQuery = "SELECT userName, created, description, profilePic
                FROM users where userId = '". $pageUser ."'";
            $pageUserResult = $conn->query($pageUserQuery);
            
            while($row = $pageUserResult->fetch_assoc()) {
                $createdDate = date_parse($row["created"]);
                echo '<div class="profilePage">
                    <div class="pfp" style="background-image:url('. $row["profilePic"] .')"></div>
                    <h1 class="userName">'. $row["userName"] .'<span class="userId"> • '. $pageUser .'</span></h1>
                    <h3 class="userCreated">Started Blobbing™️in <span>'. $createdDate["year"] .'</span></h3>
                    <p class="description">'. $row["description"] .'</p>
                </div>';
            };
        ?>
    </section>
</body>
</html>