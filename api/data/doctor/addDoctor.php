<?php
require_once("../../../connection/db_conn.php");

function cleanInput($input)
{
    $cleaned = trim($input);
    $cleaned = stripslashes($cleaned);
    $cleaned = htmlspecialchars($cleaned);
    return $cleaned;
}

// Assume JSON data is sent in the request body
$data = file_get_contents('php://input');
$jsonData = json_decode($data, true);

$identification = cleanInput($jsonData['Identification']);
$experience = cleanInput($jsonData['Experience']);
$speciality = cleanInput($jsonData['speciality']);
$fullname = cleanInput($jsonData['fullname']);

$sql = "INSERT INTO doctor (Doctor_ID, Full_Name, Y_O_E, Speciality) VALUES (?,?, ?, ?)";
$stmt = $connection->prepare($sql);

// Bind parameters to the prepared statement
$stmt->bind_param("isis", $identification,$fullname, $experience, $speciality);

// $response = array();

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Added Successfully"]);
} else {    echo json_encode(["success" => false, "error" => "There was an error with your data"]);

}

$stmt->close();
$connection->close();
