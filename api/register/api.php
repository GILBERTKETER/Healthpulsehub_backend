<?php

include "../../connection/db_conn.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
function generateRandomCode($min = 100000, $max = 999999)
{
    // Generate a random number within the specified range
    return mt_rand($min, $max);
}

function emailExists($email)
{
    global $connection;
    $existence = "SELECT * FROM pharmaceutical_company WHERE Org_Email = ?";
    $stmt1 = $connection->prepare($existence);
    $stmt1->bind_param('s', $email);
    $stmt1->execute();
    $rs = $stmt1->get_result();
    return $rs;
}
function registerUser($Company_ID, $Company_Name, $Phone_Number, $Registration_Number, $Org_Email, $password)
{
    global $connection;

    try {
        $verificationCode = generateRandomCode();
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        if ($verificationCode) {
            $subject = "Verification code";
            $message = "Please use the code $verificationCode to verify your account and proceed to your dashboard!";

            $mail = new PHPMailer(true);
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
                $mail->addAddress($Org_Email);
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body = $message;
                $sent = $mail->send();
                if ($sent) {
                    $sql = "INSERT INTO pharmaceutical_company (Company_ID , Company_Name, Phone_Number, Registration_Number, Org_Email, Password, verification_code) VALUES (?, ?, ?, ?, ?, ?,?)";
                    $stmt = $connection->prepare($sql);
                    $stmt->bind_param("isiissi", $Company_ID, $Company_Name, $Phone_Number, $Registration_Number, $Org_Email, $hashedPassword, $verificationCode);
                    $stmt->execute();
                    $stmt->close();

                    echo json_encode(["success" => true, "message" => "Registration was successfully.Verification code sent!"]);
                } else {
                    echo json_encode(["success" => false, "error" => "Error registering user: "]);
                }
            } catch (Exception $e) {
                echo json_encode(["success" => false, "error" => "Error sending verification code: ", $e]);
            }
        } else {
            echo json_encode(["success" => false, "error" =>"Error sending code!"]);
        }


        // Close the statement
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
            $email = $userData['email_org'];
            $exists = emailExists($email);
            if ($exists->num_rows > 0) {
                echo json_encode(["success" => false, "error" => "Email already registered!"]);
            } else {

                registerUser(
                    $userData['company_id'],
                    $userData['Company_Name'],
                    $userData['Phone_Number'],
                    $userData['Registration_Number'],
                    $userData['email_org'],
                    $userData['password']
                );
            }
        }
    } else {
        echo json_encode(["success" => false, "error" => "Invalid request method"]);
    }
} catch (Exception $error) {
    echo json_encode(["success" => false, "error" => $error->getMessage()]);
}

$connection->close();
