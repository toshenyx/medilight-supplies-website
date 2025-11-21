<?php
session_start();
header('Content-Type: application/json');

// Database connection
$host = 'localhost';
$dbname = 'medilight_db'; // Change to your database name
$username = 'root'; // Change to your database username
$password = ''; // Change to your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['cart_id']) || empty($input['cart_id'])) {
    echo json_encode(['success' => false, 'message' => 'Cart ID is required']);
    exit;
}

$cart_id = $input['cart_id'];

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Verify that the cart item belongs to the logged-in user before deleting
    // Using 'id' instead of 'cart_id' to match your table structure
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = :cart_id AND user_id = :user_id");
    $stmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Item removed from cart successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Item not found or does not belong to user']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error removing item: ' . $e->getMessage()]);
}
?>