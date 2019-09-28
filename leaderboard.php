<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Origin,Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'config/config.php';
global $mysqli;

$ip = (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER["REMOTE_ADDR"];
$myArray = array();
if ($result = $mysqli->query("SELECT username,`1`,`2`,`3`,`4`,`5`,`6`,`7`,`8`,`9`,score FROM users ORDER BY score DESC, sum_time DESC")) {

    while($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $myArray[] = $row;
    }
    http_response_code(200);
    echo json_encode($myArray);
}else{
    http_response_code(400);
    echo json_encode(array("message" => "something went wrong"));
            
}