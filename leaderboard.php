<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Origin,Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'functions/F_benchmark.php';
include_once 'config/config.php';
global $mysqli;
global $performance;

$ip = (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER["REMOTE_ADDR"];
$response = array();

$leaderboard_user_query_result = $mysqli->query("SELECT username FROM users LEFT JOIN
    (SELECT user,Count(question_number) AS score ,MAX(TIMESTAMPDIFF(SECOND,(SELECT start_time FROM constants LIMIT 1),submit_timestamp)) AS sub_time FROM leaderboard GROUP BY user) c2 ON c2.user=users.username
    ORDER BY c2.score DESC, c2.sub_time ");
while ($leaderboard_row = $leaderboard_user_query_result->fetch_assoc()) {
    $user = $list['user'] = $leaderboard_row['username'];
    $leaderboard_score_query_result = $mysqli->query("SELECT user, COUNT(question_number) AS score FROM leaderboard WHERE user='$user' AND question_number>0 GROUP BY user LIMIT 1");
    $score_row = $leaderboard_score_query_result->fetch_assoc();
        $list['score'] = $score_row['score']==null?0:$score_row['score'];
        for ($i = 0; $i <= D_total_number_of_questions; $i++) {
            $leaderboard_question_query_result = $mysqli->query("SELECT TIMESTAMPDIFF(SECOND,(SELECT start_time FROM constants LIMIT 1),submit_timestamp) as sub_time FROM leaderboard WHERE question_number = '$i+1' AND user='$user'");
            if ($leaderboard_question_query_result->num_rows > 0) {
                $question_row = $leaderboard_question_query_result->fetch_assoc();
                $list[$i] = date("H:i", $question_row['sub_time']);
            }else{
                $list[$i]=0;
            }
        }
        http_response_code(200);
    array_push($response, $list);
}

$response = json_encode($response);
echo $response;
F_benchmark($performance);
//$mysqli->query("INSERT INTO activities(username,request,response,type) VALUES ('$username','$rawBody','$response','leader board')");
