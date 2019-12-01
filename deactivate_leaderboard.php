<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Origin,Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'config/config.php';
global $mysqli;
$res = $mysqli->query("UPDATE constants SET leaderboard_availability=2");
if($res){
    http_response_code(200);
    $response['message']="Leaderboard Deactivated.";
} else{
    http_response_code(400);
    $response['message']="Leaderboard Deactivation Failed!";
}
$response = json_encode($response);
echo $response;
?>