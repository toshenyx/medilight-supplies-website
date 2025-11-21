<?php
session_start();
header('Content-Type: application/json');
include 'db.php';

// Mock user ID
$user_id = 1;

$data = json_decode(file_get_contents('php://input'), true);
$wishlist_id = $data['wishlist_id'];

// Remove from wishlist
$stmt = $conn->prepare("DELETE FROM wishlist WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $wishlist_id, $user_id);

if($stmt->execute()){
    echo json_encode(["status" => "success", "message" => "Removed from wishlist"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to remove from wishlist"]);
}

$stmt->close();
$conn->close();
?>
