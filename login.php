<?php

include_once 'config/config.php';
include_once 'functions/F_json_validate.php';
global $mysqli;


$response = array();
$ip = (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER["REMOTE_ADDR"];

$rawBody = file_get_contents("php://input");
$data = array(); // Initialize default data array
$data = json_decode($rawBody,true); // Then decode it
$username = $mysqli->escape_string($data['username']);
$password = $mysqli->escape_string($data['password']);

if ($mysqli->query("SELECT GET_LOCK('$username',1) ")) {

    $mysqli->query("SELECT username FROM users WHERE username='$username'");
    if ($mysqli->affected_rows > 0) {
        $response['status'] = 400;
        http_response_code(400);

    } else {
        $response['status'] = 200;
        $token = sha1($username . $password);
        $mysqli->query("INSERT INTO users VALUES('$username','$password','$token')");
        $response['token'] = $token;

        http_response_code(200);
    }
}else {
    $mysqli->query("INSERT INTO warnings VALUES ('$username')");
    $response["status"] = 406;
    http_response_code(406);
    $response["message"] = "لطفا کمی آهسته تر";
}
header('Content-Type: application/json; charset=UTF-8');
echo json_encode($response);


