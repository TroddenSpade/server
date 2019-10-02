<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Origin,Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'config/config.php';
global $mysqli;

$ip = (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER["REMOTE_ADDR"];
$response = array();

if ($mysqli->query("SELECT GET_LOCK('$username',5) ")) {
    $leaderboard_user_query_result = $mysqli->query("SELECT DISTINCT user FROM leaderboard");
    while ($leaderboard_row = $leaderboard_user_query_result->fetch_assoc()) {
        $user = $list['user'] = $leaderboard_row['user'];
        $leaderboard_score_query_result = $mysqli->query("SELECT user, COUNT(question_number) AS score FROM leaderboard WHERE user='$user' GROUP BY user LIMIT 1");
        if ($score_row = $leaderboard_score_query_result->fetch_assoc()) {
            $list['score'] = $score_row['score'];
            for ($i = 0; $i < D_total_number_of_questions; $i++) {
                $list[$i] = 0;
                $leaderboard_question_query_result = $mysqli->query("SELECT TIMESTAMPDIFF(SECOND,(SELECT start_time FROM constants LIMIT 1),submit_timestamp) as time FROM leaderboard WHERE question_number = '$i' AND user='$user'");
                if ($mysqli->affected_rows > 0) {
                    $question_row = $leaderboard_question_query_result->fetch_assoc();
                    $list[$i] = date("H:i", $question_row['time']);

                }
            }
            http_response_code(200);

        }
        array_push($response, $list);
    }
} else {
    $mysqli->query("INSERT INTO warnings VALUES ('$username')");
    http_response_code(406);
    $response['message'] = "لطفا کمی آهسته تر";

}
$response = json_encode($response);
echo $response;
$mysqli->query("INSERT INTO activities(username,request,response,type) VALUES ('$username','$rawBody','$response','leader board')");
