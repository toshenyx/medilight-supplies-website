<?php
header('Content-Type: application/json');

// Database connection parameters
$host = "localhost";
$dbname = "my_app";
$username = "root"; // replace with your DB username
$password = "";     // replace with your DB password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Assuming you have a table `wishlist` and it stores user_id, product_id
    // And a table `products` with product info
    $userId = 1; // For now, hardcoded. Later you can use session.

    $stmt = $pdo->prepare("
        SELECT w.id AS wishlist_id, p.id, p.product_name, p.price, p.image_url
        FROM wishlist w
        JOIN products p ON w.product_id = p.id
        WHERE w.user_id = :user_id
    ");
    $stmt->execute(['user_id' => $userId]);

    $wishlistItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($wishlistItems);

} catch(PDOException $e) {
    echo json_encode([]);
}
