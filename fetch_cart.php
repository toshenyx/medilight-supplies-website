<?php
session_start();
header('Content-Type: application/json');

// Hide errors from breaking JSON
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

include 'db.php'; // make sure this path is correct

$user_id = 1; // replace with session user id if available

try {
    // Fetch cart items for this user
    $sql = "SELECT c.id AS cart_id, p.id AS product_id, p.product_name, p.price, p.image_url, c.quantity 
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $cart_items = [];
    while($row = $result->fetch_assoc()){
        $cart_items[] = $row;
    }

    echo json_encode($cart_items);

    $stmt->close();
    $conn->close();
} catch(Exception $e) {
    echo json_encode(["status"=>"error", "message"=>$e->getMessage()]);
    exit;
}
?>
