<?php
header("Content-Type: application/json");

// Database connection
$conn = new mysqli("localhost", "root", "", "my_app");

if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// Optional: filter by brand or category
$brand = isset($_GET['brand']) ? $_GET['brand'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

$sql = "SELECT product_name, description, price, image_url, brand, category, page_url FROM products WHERE 1";

if ($brand != '') {
    $brand = $conn->real_escape_string($brand);
    $sql .= " AND brand='$brand'";
}

if ($category != '') {
    $category = $conn->real_escape_string($category);
    $sql .= " AND category='$category'";
}

$result = $conn->query($sql);
$products = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

echo json_encode($products);
$conn->close();
?>
