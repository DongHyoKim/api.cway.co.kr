<?php
/*
$serverName = "10.1.1.52";
$connectionOptions = array(
    "Database" => "norvt2",
    "Uid" => "norvt",
    "PWD" => "plokijuh14"
);
*/
$serverName = "219.255.132.117,8433";
$connectionOptions = array(
    "Database" => "CPT00113001",
    "Uid" => "sa",
    "PWD" => "!@12saintwo"
);
//Establishes the connection
$conn = sqlsrv_connect($serverName, $connectionOptions);
if($conn)
    echo "Connected!"
?>