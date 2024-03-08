<?php
header('Content-Type: application/json');

$data = array(
    'message' => 'Hello from PHP backend!'
);

echo json_encode($data);
?>
