<?php
$hostname = "localhost";
$username = "root";
$password = "";
$database = "healthpulsehub";
try {
    $connection = new mysqli($hostname, $username, $password, $database);
} catch (Exception $error) {
    echo $error->getMessage();
}
