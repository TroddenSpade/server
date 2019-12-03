<?php
    function F_get_answers($number){
        global $mysqli;
        $res = $mysqli->query("SELECT answer FROM questions WHERE number='$number' LIMIT 1");
        $row = $res->fetch_assoc();
        return $row['answer'];
    }
?>