<?php
require("../../../connection/db_conn.php");
function delete_user($id)
{
    global $connection;

    //check if the doctor still exists
    $check = "SELECT * FROM doctor WHERE Doctor_ID = ?";
    $stmt2 = $connection->prepare($check);
    $stmt2->bind_param('i', $id);
    $stmt2->execute();
    $result1 = $stmt2->get_result();
    return $result1;
}
try {

    $post_Data = file_get_contents('php://input');
    $data = json_decode($post_Data, true);

    $dat_id = $data['Doctor_ID'];
    $feedback = delete_user($dat_id);

    if ($feedback->num_rows > 0) {
        $query = "DELETE FROM doctor WHERE Doctor_ID = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param('i', $dat_id);
        $result = $stmt->execute();
        echo json_encode(["success" => true, "message" => "Doctor Deleted successfully"]);
    } else {
        echo json_encode(["success" => false, "error" => "Error Deleteing the doctor"]);
    }
} catch (Exception $error) {
    echo json_encode(["success" => false, "error" => "Unknown Error"]);
}
