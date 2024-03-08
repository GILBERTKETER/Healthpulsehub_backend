<?php
include("../../connection/db_conn.php");
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $verificationDetails = json_decode(file_get_contents('php://input'), true);
        $code = $verificationDetails['code'];
        $email = $verificationDetails['email'];

        $emailCheckQuery = "SELECT * FROM pharmaceutical_company WHERE Org_Email = ?";
        $stmt1 = $connection->prepare($emailCheckQuery);
        $stmt1->bind_param("s", $email);
        $stmt1->execute();
        $result1 = $stmt1->get_result();

        if ($row1 = $result1->fetch_assoc()) {
            $verificationCode = $row1['Reset_code'];
            if ($code == $verificationCode) {
                $updateQuery = "UPDATE pharmaceutical_company SET Reset_code = 0 WHERE Org_Email = ?";
                $stmt2 = $connection->prepare($updateQuery);
                $stmt2->bind_param("s", $email);
                $stmt2->execute();
                $stmt2->close();

                http_response_code(200);
            } else {
                http_response_code(400);
            }
        } else {
            http_response_code(404);
        }

        $stmt1->close();
    }
} catch (Exception $error) {
    http_response_code(500);
}
