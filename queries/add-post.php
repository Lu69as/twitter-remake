<?php
    require_once "./functions.php";
    $conn = getDBConnection();

    if (!empty($_POST['textpost'])) {
        try {
            $post = $conn->prepare("INSERT INTO posts (text, userId) VALUES (?, ?)");
            $post->bind_param("ss", $_POST['textpost'], $_POST['postuser']);
            $post->execute(); $post->close(); $postId = $conn->insert_id;

            $blobsArr = [];
            $blobsResult = $conn->query("SELECT * FROM blobs");
            while ($row = $blobsResult->fetch_assoc()) $blobsArr[] = $row["blobId"];

            foreach (explode("|", $_POST['blobs_selected']) as $blob) {
                $blob = str_replace("§", "", $blob);
                if (!in_array($blob, $blobsArr)) {
                    $blob_create = $conn->prepare("INSERT INTO blobs VALUES (?)");
                    $blob_create->bind_param("s", $blob);
                    $blob_create->execute(); $blob_create->close();
                }
                $blob_connect = $conn->prepare("INSERT INTO post_blobs VALUES (?, ?)");
                $blob_connect->bind_param("ss", $postId, $blob);
                $blob_connect->execute(); $blob_connect->close();
            }

            header("Location: ".$_SERVER['HTTP_REFERER']);
        }
        catch (mysqli_sql_exception $e) {
            error_log($e->getMessage());
            echo "Something went wrong.";
            echo '<br><a href="../">Return to homepage</a>';
        }
    }
    else header("Location: ".$_SERVER['HTTP_REFERER']);
    exit;