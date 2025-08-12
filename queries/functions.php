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

    function timeAgo($dateString) {
        $diff = time() - strtotime($dateString);
        if ($diff < 0) return "In the future";

        $units = [
            31536000 => "year",
            2592000  => "month",
            86400    => "day",
            3600     => "hour",
            60       => "minute",
            1        => "second"
        ];

        foreach ($units as $seconds => $name) {
            if ($diff >= $seconds) {
                $value = floor($diff / $seconds);
                return $value . " {$name}" . ($value > 1 ? "s" : "") . " ago";
            }
        }
    }
    
    function addPostsHtml($query, $p) {
        $connInner = getDBConnection();        
        $result = $connInner->query($query);
        $output = "";
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $hasLiked = false;
                
                if (isset($_COOKIE["user"])) {
                    $hasLikedQuery = "select * from likes where postId = ".$row["postId"]." and userId = '".$_COOKIE["user"]."'";
                    $hasLikedResult = $connInner->query($hasLikedQuery);
                    if (isset($hasLikedResult->fetch_assoc()["postId"])) $hasLiked = true;
                }

                $output .= '<div class="post" id="'.$row["postId"].'">
                    <a class="profile_pic" href="'.$p.'profile/?user='.$row["userId"].'" style="background-image:url('.$row["profilePic"].')"></a>
                    <div class="content">
                        <p class="user">
                            <span class="userName">'.$row["userName"].'</span>
                            <span class="userId">@'.$row["userId"].'</span>
                            <span class="date"> • '.timeAgo($row["posted"]).'</span>
                        </p>
                        <p class="text">'.str_replace("\n", "<br>", $row["text"]).'</p>
                    </div>
                    <form action="'.$p.'queries/add-like.php"  method="post" class="interactions">
                        <input type="hidden" name="postId" value="'.$row["postId"].'">
                        <input type="hidden" name="hasLiked" value="'.$hasLiked.'">
                        <button type="submit" name="likes" class="'.($hasLiked ? 'hasLiked' : '').'">'
                            .file_get_contents(''.$p.'img/icons/heart.svg').'<p>'.$row["likes"].'</p></button>
                    </form>
                </div>';
            };
        } else { $output = "0 results"; }
        return $output;
    }

