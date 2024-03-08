<?php
require_once "../../../connection/db_conn.php";

$sql = "SELECT Company_ID	, Company_Name, Company_Name, Phone_Number	, Phone_Number, Org_Email FROM pharmaceutical_company";
$stmt = $connection->prepare($sql);

if ($stmt->execute()) {
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch data and encode it as JSON
        $data = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($data);
    } else {
        // No data found
        echo "[]";
    }
} else {
    // Error in query execution
    echo json_encode(["error" => "Failed to execute query"]);
}

$stmt->close();
$connection->close();
