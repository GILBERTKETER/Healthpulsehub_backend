<?php
include("../../connection/db_conn.php");

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $verificationDetails = json_decode(file_get_contents('php://input'), true);
        $code = $verificationDetails['code'];
        $email = $verificationDetails['email'];

        $emailCheckQuery = "SELECT verification_code FROM pharmaceutical_company WHERE Org_Email = ?";
        $stmt1 = $connection->prepare($emailCheckQuery);
        $stmt1->bind_param("s", $email);
        $stmt1->execute();
        $result1 = $stmt1->get_result();

        if ($row1 = $result1->fetch_assoc()) {
            $verificationCode = $row1['verification_code'];

            if ($code == $verificationCode) {
                $updateQuery = "UPDATE pharmaceutical_company SET verification_code = 0 WHERE Org_Email = ?";
                $stmt2 = $connection->prepare($updateQuery);
                $stmt2->bind_param("s", $email);
                $stmt2->execute();

                echo json_encode(["success" => true, "message" => "Verified!"]);
            } else {
                echo json_encode(["success" => false, "error" => "There was an issue!"]);
            }
        } else {
            echo json_encode(["success" => false, "error" => "Unknown Error!"]);
        }

        $stmt1->close();
        $stmt2->close();
    }
} catch (Exception $error) {
    echo json_encode(["success" => false, "error" => "Server Error!"]);
}
