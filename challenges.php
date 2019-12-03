<?php

header("Access-Control-Allow-Origin: http://localhost:8080");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Origin,Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'config/config.php';
include_once 'functions/F_get_challenges.php';
global $mysqli;

$ip = (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER["REMOTE_ADDR"];

$rawBody = file_get_contents("php://input");
$data = array();
$data = json_decode($rawBody, true);
$token = $mysqli->escape_string($data['token']);
$response = array();
$result = $mysqli->query("SELECT token FROM users WHERE token='$token' LIMIT 1");
if($result->num_rows>0){
    $response['challenges']=F_get_challenges();
}else{
    http_response_code(401);
    $response['message'] = "Access Unauthorized.";
}
$response = json_encode($response);
echo $response;
