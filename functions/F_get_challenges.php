<?php
    function F_get_challenges(){
        global $mysqli;
        $challenges = array();
        $challenge_query_result = $mysqli->query("SELECT id,number,body,name,attach_link,attach_name,point,author FROM challenges ORDER BY number ASC");
        while($row = $challenge_query_result->fetch_assoc()){
            $list = array();
            $list['id']=$row['id'];
            $list['number']=$row['number'];
            $list['body']=$row['body'];
            $list['name']=$row['name'];
            $list['attach_link']=$row['attach_link'];
            $list['attach_name']=$row['attach_name'];
            $list['point']=$row['point'];
            $list['author']=$row['author'];
            array_push($challenges,$list);

        }
        return $challenges;
    }
?>