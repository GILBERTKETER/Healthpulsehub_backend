<?php
include "../../connection/db_conn.php";

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $postDataRecieved = file_get_contents("php://input");
        $data = json_decode($postDataRecieved, true);
        $hashed = password_hash($data['newPassword'], PASSWORD_DEFAULT);
        $emailAddress = $data['userEmail'];
        $query = "UPDATE pharmaceutical_company SET Password = ? WHERE Org_Email = ?";
        $stmt_setpass = $connection->prepare($query);
        $stmt_setpass->bind_param('ss', $hashed, $emailAddress);
        $updated = $stmt_setpass->execute();
        if ($updated) {
            echo json_encode(["success" => true, "message" => "Password Changed successfully"]);
        } else {
            echo json_encode(["success" => false, "error" => "There was an error"]);
        }

        $connection->close();
    } else {
        echo json_encode(["success" => false, "error" => "Invalid request method"]);
    }
} catch (Exception $error) {
    echo json_encode(["success" => false, "error" => "Unknown error"]);
}
