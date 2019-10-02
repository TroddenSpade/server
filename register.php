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
$username = $mysqli->escape_string($data['username']);
$password = $mysqli->escape_string($data['password']);

$response = array();
if (
    !empty($username) &&
    !empty($password)
) {
    if ($mysqli->query("SELECT GET_LOCK('$username',1) ")) {

        $mysqli->query("SELECT username FROM users WHERE username='$username'");
        if ($mysqli->affected_rows > 0) {
            http_response_code(400);
            $response['message']="user already exists!";
            //TODO automatic login
        } else {
            $token = sha1($username . $password);
            $type = rand(0,3);

            $mysqli->query("INSERT INTO users(username,password, token,type ) VALUES('$username','$password','$token','$type')");

            http_response_code(200);
            $response['token']=$token;
            $response['username']=$username;
            $response['type']=$type;
        }
    } else {
        $mysqli->query("INSERT INTO warnings VALUES ('$username')");
        http_response_code(406);
        $response['message']="لطفا کمی آهسته تر";

    }
} else {
    http_response_code(400);
    $response['message'] = "Unable to create user. Data is incomplete.";
}
$response = json_encode($response);
echo $response;

