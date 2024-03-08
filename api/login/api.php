<?php
session_start();
$_SESSION['LOGGED_IN_EMAIL'] = '';

include "../../connection/db_conn.php";

function loguser($email, $password)
{
    global $connection;
    $userEmail = "SELECT * FROM pharmaceutical_company WHERE Org_Email = ?;";
    $stmt = $connection->prepare($userEmail);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $userPassword = "SELECT Password FROM pharmaceutical_company WHERE Org_Email = ?;";
        $stmt2 = $connection->prepare($userPassword);
        $stmt2->bind_param('s', $email);
        $stmt2->execute();
        $result2 = $stmt2->get_result()->fetch_assoc();
        $DB_password = $result2['Password'];
        $verifiedPassword = password_verify($password, $DB_password);

        if ($verifiedPassword == true) {
            $verificationCodeQuery = "SELECT verification_code FROM pharmaceutical_company WHERE Org_Email = ?;";
            $stmt3 = $connection->prepare($verificationCodeQuery);
            $stmt3->bind_param('s', $email);
            $stmt3->execute();
            $result3 = $stmt3->get_result()->fetch_assoc();
            $verificationCode = $result3['verification_code'];

            if ($verificationCode != 0) {
                echo json_encode(["success" => false, "error" => "Unverified"]);
            } else {
                $_SESSION['LOGGED_IN_EMAIL'] = $email;
                // echo "Session set: {$_SESSION['LOGGED_IN_EMAIL']}";
                echo json_encode(["success" => true, "message" => "Log In was Successful!"]);
            }
        } else {
            echo json_encode(["success" => false, "error" => "Password Incorrect!"]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "Email Doesn't Exist!"]);
    }
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $postData = file_get_contents("php://input");
        $userData = json_decode($postData, true);
        $email_address = $userData['email'];
        if ($userData === null) {
            echo json_encode(["success" => false, "error" => "Invalid User data"]);
        } else {
            loguser(
                $userData['email'],
                $userData['password']
            );

            $connection->close();
        }
    } else {
        echo json_encode(["success" => false, "error" => "Invalid request method"]);
    }
} catch (Exception $error) {
    echo json_encode(["success" => false, "error" => $error->getMessage()]);
}

// echo json_encode(["email_address" => $_SESSION['LOGGED_IN_EMAIL']]);
