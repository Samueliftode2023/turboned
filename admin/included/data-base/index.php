<?php
$serverName = 'localhost';
$dBUser = 'root';
$dBNume = 'turboned';
$parola ='';

$conectareDB = mysqli_connect($serverName, $dBUser, $parola ,$dBNume);

if ($conectareDB->connect_error) {
    die("Connection failed: " . $conectareDB->connect_error);
}

?>