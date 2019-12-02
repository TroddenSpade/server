<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Origin,Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'config/config.php';
global $mysqli;

$ip = (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER["REMOTE_ADDR"];

$rawBody = file_get_contents("php://input");
$data = array(); // Initialize default data array
$data = json_decode($rawBody, true);
$response = array();
$token = $data['token'];
$ctf = $data['ctf'];
//$type = $data ['type'];
$nop = $data['nop'];
if (!empty($token) &&
//    !empty($type) &&
    !empty($ctf) &&
    !empty($nop)
) {
    $username = "user";
    $user_query_result = $mysqli->query("SELECT username, type FROM users WHERE token = '$token' LIMIT 1");
    if ($user_row = $user_query_result->fetch_assoc()) {
        $username = $user_row['username'];
        $type = $user_row['type'];
        $mysqli->query("INSERT INTO submissions(username,ctf_code,question_number,type)
                                VALUES ('$username','$ctf','$nop','$type')");
        $leaderboard_availability_query_result = $mysqli->query("SELECT leaderboard_availability FROM constants LIMIT 1");
        if ($leaderboard_availability_row = $leaderboard_availability_query_result->fetch_assoc()) {
            if ($leaderboard_availability_row['leaderboard_availability'] == 1) {
                $answer=F_get_answers($nop,$type);
                if ($answer == $ctf) {
                    $mysqli->query("DELETE FROM leaderboard WHERE user = '$username' AND question_number = '$nop'");
                    $mysqli->query("INSERT INTO leaderboard(question_number,user)
                                         VALUES ('$nop','$username')");
                    $response['status']="submitted to leaderboard";
                }else{
                    $response['status']="wrong code real code is :  ";

                }
            } else{
                $response['status']= "leaderboard is unavailable.";
            }
        }
        http_response_code(200);
        $response['message'] = "Code has been submitted successfully.";
    } else {
        http_response_code(400);
        $response['message'] = "User does not exists!";
    }

} else {
    http_response_code(400);
    $response['message'] = "Insufficient data.";
}
$response = json_encode($response);
echo $response;

