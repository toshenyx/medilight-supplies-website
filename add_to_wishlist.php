<?php
session_start();
header('Content-Type: application/json');
include 'db.php'; // include database connection

// Mock user_id for now (replace with login system later)
$user_id = 1; 

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);
$product_id = $data['product_id'];

// Insert into wishlist
$stmt = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
$stmt->bind_param("ii", $user_id, $product_id);

if($stmt->execute()){
    echo json_encode(["status" => "success", "message" => "Product added to wishlist!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to add to wishlist."]);
}

$stmt->close();
$conn->close();
?>
