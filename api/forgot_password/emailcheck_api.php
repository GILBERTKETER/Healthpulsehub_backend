<?php

include "../../connection/db_conn.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
$mail = new PHPMailer(true);

function checkEmail($email)
{
    global $connection;
    $sql = "SELECT * FROM pharmaceutical_company WHERE Org_Email = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}
function generateRandomCode($min = 100000, $max = 999999)
{
    // Generate a random number within the specified range
    return mt_rand($min, $max);
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $postData = file_get_contents("php://input");
        $userData = json_decode($postData, true);
        // echo $userData;
        if ($userData === null || !isset($userData['email'])) {
            echo json_encode(["success" => false, "error" => "Invalid User Email"]);
        } else {
            $userEmail = $userData['email'];
            $emailExists = checkEmail($userEmail);

            if ($emailExists) {
                $code = generateRandomCode();
                if ($code) {
                    $subject = "Verification code";
                    $message = "Please use the code $code to change your account password and proceed to your dashboard!";

                    try {
                        $mail->SMTPDebug = 0;
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->Port = 587;
                        $mail->SMTPAuth = true;
                        $mail->Username = 'gilbertketer759@gmail.com';
                        $mail->Password = 'njzsrrhjkijgvstl';
                        $mail->SMTPSecure = 'tls';
                        $mail->setFrom('gilbertketer759@gmail.com', 'HealthPulseHub');
                        $mail->addAddress($userEmail);
                        $mail->isHTML(true);
                        $mail->Subject = $subject;
                        $mail->Body = $message;
                        $sent = $mail->send();
                        echo json_encode(["success" => true, "message" => "Great, please verify!"]);
                    } catch (Exception $e) {
                        echo json_encode(["success" => false, "error" => "Error sending verification code: ", $e]);
                    }
                } else {
                    echo json_encode(["success" => false, "error" => "Error sending code!"]);
                }
                $update = "UPDATE pharmaceutical_company SET Reset_code = ? WHERE Org_Email = ?;";
                $stmt1 = $connection->prepare($update);
                $stmt1->bind_param('is', $code, $userEmail);
                $stmt1->execute();
            } else {
                echo json_encode(["success" => false, "error" => "Sorry, the email does not exist!"]);
            }

            // Close the database connection if needed
            $connection->close();
        }
    } else {
        echo json_encode(["success" => false, "error" => "Invalid request method"]);
    }
} catch (Exception $error) {
    echo json_encode(["success" => false, "error" => "Unknown error"]);
}
