<?php
function F_benchmark($performance)
{
    include_once 'config/config.php';
    global $mysqli;
    $performance = (round(((microtime(true) - $performance) * 1000), 4));
    $mysqli->query("INSERT INTO benchmark(exec_time) VALUES('$performance') ");
}

?>
