<?php

include "../../connection/db_conn.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

function resendCode($email)
{
    global $connection;

    try {


        // Construct a prepared statement
        $sql = "SELECT * FROM pharmaceutical_company WHERE Org_Email = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $verificationCode = $result['Reset_code'];


        if ($verificationCode) {
            // Construct the reset link with the token

            // Prepare the email content
            $subject = "Reset Code code";
            $message = "Please use the code $verificationCode to verify your account and proceed to your dashboard!";

            // Create a new PHPMailer instance
            $mail = new PHPMailer(true); // Set true to enable exceptions

            try {
                //Server settings
                $mail->SMTPDebug = 0; // 0 = no output, 2 = verbose output
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Your SMTP server
                $mail->Port = 587; // Your SMTP server port
                $mail->SMTPAuth = true;
                $mail->Username = 'gilbertketer759@gmail.com'; // Your SMTP username
                $mail->Password = 'njzsrrhjkijgvstl'; // Your SMTP password
                $mail->SMTPSecure = 'tls'; // tls or ssl

                //Recipients
                $mail->setFrom('gilbertketer759@gmail.com', 'HealthPulseHub'); // Sender email and name
                $mail->addAddress($email); // Recipient email

                //Content
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body = $message;
                $mail->send();
                echo json_encode(["success" => true, "message" => "Reset code sent!"]);
            } catch (Exception $e) {
                echo json_encode(["success" => false, "error" => "Error sending Reset code"]);
            }
        } else {
            echo "An error occured.";
        }

        $stmt->close();
    } catch (Exception $error) {
        echo json_encode(["success" => false, "error" => $error->getMessage()]);
    }
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $postData = file_get_contents("php://input");

        $userData = json_decode($postData, true);

        if ($userData === null) {
            echo json_encode(["success" => false, "error" => "Invalid JSON data"]);
        } else {


            resendCode(
                $userData['email']
            );
        }
    } else {
        echo json_encode(["success" => false, "error" => "Invalid request method"]);
    }
} catch (Exception $error) {
    echo json_encode(["success" => false, "error" => $error->getMessage()]);
}

$connection->close();
