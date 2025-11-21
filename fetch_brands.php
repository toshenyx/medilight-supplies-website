<?php
header("Content-Type: application/json");

// DATABASE CONNECTION
$conn = new mysqli("localhost", "root", "", "my_app");

// Check connection
if ($conn->connect_error) {
    echo json_encode([]);
    exit;
}

// Fetch brands
$sql = "SELECT brand_name, image_url, page_url FROM brands";
$result = $conn->query($sql);

$brands = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $brands[] = $row;
    }
}

echo json_encode($brands);
$conn->close();
?>
