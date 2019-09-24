<?php
function F_json_validate(){
$json = json_decode($_POST['request'],true);
if (is_array($json)){
    return $json;
}
return false;
}
?>