<?php
session_start();

// Database connection
$host = 'localhost';
$dbname = 'my_app';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Get and sanitize form data
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;
    $customer_type = trim($_POST['customerType']);
    $full_name = trim($_POST['fullName']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $kra_pin = isset($_POST['kraPin']) ? trim($_POST['kraPin']) : NULL;
    $tax_exempt = isset($_POST['kraTaxExempt']) ? 1 : 0;
    
    // Delivery details
    $delivery_address = trim($_POST['deliveryAddress']);
    $city = trim($_POST['city']);
    $county = trim($_POST['county']);
    $postal_code = trim($_POST['postalCode']);
    $delivery_notes = trim($_POST['deliveryNotes']);
    
    // Payment details
    $payment_method = trim($_POST['paymentMethod']);
    $total_amount = floatval($_POST['totalAmount']);
    $voucher_code = isset($_POST['voucherCode']) ? trim($_POST['voucherCode']) : NULL;
    
    // Validate required fields
    if (empty($customer_type) || empty($full_name) || empty($email) || empty($phone) || 
        empty($delivery_address) || empty($city) || empty($county) || empty($payment_method)) {
        die("Error: All required fields must be filled out.");
    }
    
    // Handle file upload for tax exemption certificate
    $exemption_cert_path = NULL;
    if ($tax_exempt && isset($_FILES['exemptionCert']) && $_FILES['exemptionCert']['error'] == 0) {
        $upload_dir = 'uploads/exemption_certificates/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = pathinfo($_FILES['exemptionCert']['name'], PATHINFO_EXTENSION);
        $new_filename = 'cert_' . time() . '_' . uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . $new_filename;
        
        if (move_uploaded_file($_FILES['exemptionCert']['tmp_name'], $upload_path)) {
            $exemption_cert_path = $upload_path;
        }
    }
    
    try {
        $pdo->beginTransaction();
        
        // Insert order
        $stmt = $pdo->prepare("
            INSERT INTO orders (
                user_id, customer_type, full_name, email, phone, 
                kra_pin, tax_exempt, exemption_cert_path,
                delivery_address, city, county, postal_code, delivery_notes,
                payment_method, total_amount, voucher_code, order_status, created_at
            ) VALUES (
                :user_id, :customer_type, :full_name, :email, :phone,
                :kra_pin, :tax_exempt, :exemption_cert_path,
                :delivery_address, :city, :county, :postal_code, :delivery_notes,
                :payment_method, :total_amount, :voucher_code, 'Pending', NOW()
            )
        ");
        
        $stmt->execute([
            ':user_id' => $user_id,
            ':customer_type' => $customer_type,
            ':full_name' => $full_name,
            ':email' => $email,
            ':phone' => $phone,
            ':kra_pin' => $kra_pin,
            ':tax_exempt' => $tax_exempt,
            ':exemption_cert_path' => $exemption_cert_path,
            ':delivery_address' => $delivery_address,
            ':city' => $city,
            ':county' => $county,
            ':postal_code' => $postal_code,
            ':delivery_notes' => $delivery_notes,
            ':payment_method' => $payment_method,
            ':total_amount' => $total_amount,
            ':voucher_code' => $voucher_code
        ]);
        
        $order_id = $pdo->lastInsertId();
        
        // Fetch cart items and insert into order_items
        if ($user_id) {
            $cart_stmt = $pdo->prepare("
                SELECT c.product_id, c.quantity, p.product_name, p.price 
                FROM cart c 
                JOIN products p ON c.product_id = p.id 
                WHERE c.user_id = :user_id
            ");
            $cart_stmt->execute([':user_id' => $user_id]);
            $cart_items = $cart_stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($cart_items as $item) {
                $subtotal = $item['price'] * $item['quantity'];
                
                $item_stmt = $pdo->prepare("
                    INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price, subtotal)
                    VALUES (:order_id, :product_id, :product_name, :quantity, :unit_price, :subtotal)
                ");
                
                $item_stmt->execute([
                    ':order_id' => $order_id,
                    ':product_id' => $item['product_id'],
                    ':product_name' => $item['product_name'],
                    ':quantity' => $item['quantity'],
                    ':unit_price' => $item['price'],
                    ':subtotal' => $subtotal
                ]);
            }
            
            // Clear cart after order
            $clear_cart = $pdo->prepare("DELETE FROM cart WHERE user_id = :user_id");
            $clear_cart->execute([':user_id' => $user_id]);
        }
        
        // Process payment based on method
        if ($payment_method === 'mpesa') {
            $mpesa_phone = trim($_POST['mpesaPhone']);
            // TODO: Integrate M-Pesa STK Push API here
            $payment_stmt = $pdo->prepare("
                INSERT INTO payments (order_id, payment_method, amount, phone_number, status, created_at)
                VALUES (:order_id, 'M-Pesa', :amount, :phone, 'Pending', NOW())
            ");
            $payment_stmt->execute([
                ':order_id' => $order_id,
                ':amount' => $total_amount,
                ':phone' => $mpesa_phone
            ]);
        } elseif ($payment_method === 'card') {
            // TODO: Integrate payment gateway (e.g., Stripe, Flutterwave)
            $payment_stmt = $pdo->prepare("
                INSERT INTO payments (order_id, payment_method, amount, status, created_at)
                VALUES (:order_id, 'Card', :amount, 'Pending', NOW())
            ");
            $payment_stmt->execute([
                ':order_id' => $order_id,
                ':amount' => $total_amount
            ]);
        }
        
        $pdo->commit();
        
        // Redirect to thankyou page with order_id and total
        header("Location: thank_you.html?order_id=$order_id&total=$total_amount");
        exit();
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error processing order: " . $e->getMessage());
    }
    
} else {
    header("Location: checkout.php");
    exit();
}
?>
