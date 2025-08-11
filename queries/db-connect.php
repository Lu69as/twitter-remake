<?php
    function getDBConnection() {
        static $conn;
        if ($conn === null) {
            $conn = new mysqli("127.0.0.1", "root", "", "twitter-remake");
            if ($conn->connect_error) {
                die("Tilkoblingsfeil: " . $conn->connect_error);
            }
        }
        return $conn;
    } 
    
    function addPostsHtml($query) {
        $connInner = getDBConnection();
        $result = $connInner->query($query);
        $output = "";
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $output .= '<div class="post" id="'.$row["postId"].'">
                    <a class="profile_pic" href="./profile/?user='.$row["userId"].'" style="background-image:url('.$row["profilePic"].')"></a>
                    <div class="content">
                        <p class="user">
                            <span class="userName">'.$row["userName"].'</span>
                            <span class="userId">'.$row["userId"].'</span>
                            <span class="date">'.$row["posted"].'</span>
                        </p>
                        <p class="text">'.str_replace("\n", "<br>", $row["text"]).'</p>
                    </div>
                </div>';
            };
        } else { $output = "0 results"; }
        return $output;
    }