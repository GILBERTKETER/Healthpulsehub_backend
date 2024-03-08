<?php
require_once "../../../connection/db_conn.php";

$jsonData = file_get_contents('php://input');

$data = json_decode($jsonData, true);

if ($data === null) {
   
    echo json_encode([
        'success' => false,
        'error' => 'Invalid JSON data'
    ]);
    exit;
}

$identification = $data['Identification'];
$experience = $data['Experience'];
$fullname = $data['fullname'];
$speciality = $data['speciality'];

$stmt = $connection->prepare("UPDATE doctor SET Y_O_E = ?, Full_Name = ?, Speciality = ? WHERE Doctor_ID = ?");
$stmt->bind_param("issi", $experience, $fullname, $speciality, $identification);

if ($stmt->execute()) {

    echo json_encode([
        'success' => true,
        'message' => 'Doctor updated successfully'
    ]);
} else {
  
    echo json_encode([
        'success' => false,
        'error' => 'Doctor not updated'
    ]);
}

$stmt->close();
$connection->close();
