<?php

require_once('../../../connection/db_conn.php');

// Initialize an empty array to store the fetched data
$doctors = array();

// SQL query to fetch data from the Doctor table
$sql = "SELECT Doctor_ID, Full_Name, Speciality, Y_O_E FROM Doctor";

// Execute the query
$result = $connection->query($sql);

// Check if the query was successful
if ($result) {
    // Fetch the result as an associative array
    while ($row = $result->fetch_assoc()) {
        // Add each row to the $doctors array
        $doctors[] = $row;
    }

    // Free the result set
    $result->free();
} else {
    // If the query fails, return an error message
    echo json_encode(["error" => "Failed to fetch data"]);
    exit();
}

// Close the database connection
$connection->close();

// Encode the $doctors array as JSON and echo it
echo json_encode($doctors);
?>
