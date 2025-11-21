<?php
session_start();
header('Content-Type: application/json');

// Database connection
$host = 'localhost';
$dbname = 'my_app';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['voucher_code']) || empty($input['voucher_code'])) {
    echo json_encode(['success' => false, 'message' => 'Voucher code is required']);
    exit;
}

$voucher_code = strtoupper(trim($input['voucher_code']));
$subtotal = floatval($input['subtotal']);

try {
    // Check if voucher exists and is valid
    $stmt = $pdo->prepare("
        SELECT * FROM vouchers 
        WHERE code = :code 
        AND is_active = 1 
        AND (expiry_date IS NULL OR expiry_date >= CURDATE())
        AND (usage_limit IS NULL OR times_used < usage_limit)
        AND (min_purchase_amount IS NULL OR :subtotal >= min_purchase_amount)
    ");
    
    $stmt->execute([
        ':code' => $voucher_code,
        ':subtotal' => $subtotal
    ]);
    
    $voucher = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$voucher) {
        echo json_encode(['success' => false, 'message' => 'Invalid or expired voucher code']);
        exit;
    }
    
    // Calculate discount
    $discount_amount = 0;
    
    if ($voucher['discount_type'] === 'percentage') {
        $discount_amount = ($subtotal * $voucher['discount_value']) / 100;
        
        // Apply max discount cap if set
        if ($voucher['max_discount_amount'] && $discount_amount > $voucher['max_discount_amount']) {
            $discount_amount = $voucher['max_discount_amount'];
        }
    } elseif ($voucher['discount_type'] === 'fixed') {
        $discount_amount = $voucher['discount_value'];
        
        // Discount cannot exceed subtotal
        if ($discount_amount > $subtotal) {
            $discount_amount = $subtotal;
        }
    }
    
    // Update voucher usage count
    $update_stmt = $pdo->prepare("UPDATE vouchers SET times_used = times_used + 1 WHERE id = :id");
    $update_stmt->execute([':id' => $voucher['id']]);
    
    echo json_encode([
        'success' => true,
        'discount_amount' => $discount_amount,
        'voucher_code' => $voucher_code,
        'message' => 'Voucher applied successfully'
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error validating voucher: ' . $e->getMessage()]);
}
?>