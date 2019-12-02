<?php

header("Access-Control-Allow-Origin: http://localhost:8080");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Origin,Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'functions/F_benchmark.php';
include_once 'config/config.php';
global $mysqli;
global $performance;

$ip = (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER["REMOTE_ADDR"];

$rawBody = file_get_contents("php://input");
$data = array(); // Initialize default data array
$data = json_decode($rawBody, true); // Then decode itv
$username = $mysqli->escape_string($data['username']);
$password = $mysqli->escape_string($data['password']);
$response = array();

//    $mysqli->query("INSERT INTO activities(username,ctf_code,submit_time,question_number,type) VALUES ('$username','$rawBody','sign_in')");
$sign_in_result = $mysqli->query("SELECT token FROM users WHERE username='$username' AND password='$password' LIMIT 1");
if ($sign_in_result->num_rows == 1) {

    $user_row = $sign_in_result->fetch_assoc();
    $token = $user_row['token'];
    http_response_code(200);

    $response['token'] = $token;
    $response['username'] = $username;

} else {
    http_response_code(400);
    $response['error'] = "User does not exist!";
}

$response = json_encode($response);
echo $response;
F_benchmark($performance);