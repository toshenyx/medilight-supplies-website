<?php
require_once '../config.php';
require_once '../cart_functions.php';
require_once '../user_functions.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

session_start();

$cartFunctions = new CartFunctions();
$userFunctions = new UserFunctions();

// Get user ID from session if logged in, otherwise use session ID
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$session_id = session_id();

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            // Get cart items
            if ($user_id) {
                $cartItems = $cartFunctions->getCartItems($user_id);
                $total = $cartFunctions->getCartTotal($user_id);
                $count = $cartFunctions->getCartCount($user_id);
            } else {
                $cartItems = $cartFunctions->getCartItemsBySession($session_id);
                $total = $cartFunctions->getCartTotalBySession($session_id);
                $count = $cartFunctions->getCartCountBySession($session_id);
            }
            
            echo json_encode([
                'success' => true,
                'items' => $cartItems,
                'total' => $total,
                'count' => $count
            ]);
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            $action = $input['action'] ?? '';
            
            switch ($action) {
                case 'add':
                    $product_id = $input['product_id'];
                    $quantity = $input['quantity'] ?? 1;
                    
                    if ($user_id) {
                        $result = $cartFunctions->addToCart($user_id, $product_id, $quantity);
                    } else {
                        $result = $cartFunctions->addToCartBySession($session_id, $product_id, $quantity);
                    }
                    
                    if ($result) {
                        echo json_encode(['success' => true, 'message' => 'Item added to cart']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to add item to cart']);
                    }
                    break;
                    
                case 'update':
                    $cart_id = $input['cart_id'];
                    $quantity = $input['quantity'];
                    
                    $result = $cartFunctions->updateCartItemQuantity($cart_id, $quantity);
                    
                    if ($result) {
                        echo json_encode(['success' => true, 'message' => 'Cart updated']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to update cart']);
                    }
                    break;
                    
                case 'remove':
                    $cart_id = $input['cart_id'];
                    
                    $result = $cartFunctions->removeFromCart($cart_id);
                    
                    if ($result) {
                        echo json_encode(['success' => true, 'message' => 'Item removed from cart']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to remove item from cart']);
                    }
                    break;
                    
                default:
                    echo json_encode(['success' => false, 'message' => 'Invalid action']);
                    break;
            }
            break;
            
        case 'DELETE':
            // Clear entire cart
            if ($user_id) {
                $result = $cartFunctions->clearCart($user_id);
            } else {
                $result = $cartFunctions->clearCartBySession($session_id);
            }
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Cart cleared']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to clear cart']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>