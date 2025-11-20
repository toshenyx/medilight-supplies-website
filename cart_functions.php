<?php
require_once 'config.php';

class CartFunctions {
    private $conn;

    public function __construct() {
        $this->conn = getConnection();
    }

    // Add item to cart
    public function addToCart($user_id, $product_id, $quantity = 1) {
        try {
            // Check if product exists in cart for this user
            $sql_check = "SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?";
            $stmt_check = $this->conn->prepare($sql_check);
            $stmt_check->execute([$user_id, $product_id]);
            $existing_item = $stmt_check->fetch(PDO::FETCH_ASSOC);

            if ($existing_item) {
                // Update quantity if item already exists in cart
                $new_quantity = $existing_item['quantity'] + $quantity;
                $sql = "UPDATE cart SET quantity = ? WHERE id = ?";
                $stmt = $this->conn->prepare($sql);
                return $stmt->execute([$new_quantity, $existing_item['id']]);
            } else {
                // Insert new item to cart
                $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                return $stmt->execute([$user_id, $product_id, $quantity]);
            }
        } catch(PDOException $e) {
            throw new Exception("Error adding to cart: " . $e->getMessage());
        }
    }

    // Add item to cart by session (for non-logged in users)
    public function addToCartBySession($session_id, $product_id, $quantity = 1) {
        try {
            // Check if product exists in cart for this session
            $sql_check = "SELECT id, quantity FROM cart WHERE session_id = ? AND product_id = ?";
            $stmt_check = $this->conn->prepare($sql_check);
            $stmt_check->execute([$session_id, $product_id]);
            $existing_item = $stmt_check->fetch(PDO::FETCH_ASSOC);

            if ($existing_item) {
                // Update quantity if item already exists in cart
                $new_quantity = $existing_item['quantity'] + $quantity;
                $sql = "UPDATE cart SET quantity = ? WHERE id = ?";
                $stmt = $this->conn->prepare($sql);
                return $stmt->execute([$new_quantity, $existing_item['id']]);
            } else {
                // Insert new item to cart
                $sql = "INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                return $stmt->execute([$session_id, $product_id, $quantity]);
            }
        } catch(PDOException $e) {
            throw new Exception("Error adding to cart: " . $e->getMessage());
        }
    }

    // Get cart items for a user
    public function getCartItems($user_id) {
        try {
            $sql = "SELECT c.id as cart_id, c.quantity, p.id as product_id, p.name, p.description, p.price, p.image_path, p.stock_quantity FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ? AND p.is_active = TRUE";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$user_id]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            throw new Exception("Error getting cart items: " . $e->getMessage());
        }
    }

    // Get cart items for a session
    public function getCartItemsBySession($session_id) {
        try {
            $sql = "SELECT c.id as cart_id, c.quantity, p.id as product_id, p.name, p.description, p.price, p.image_path, p.stock_quantity FROM cart c JOIN products p ON c.product_id = p.id WHERE c.session_id = ? AND p.is_active = TRUE";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$session_id]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            throw new Exception("Error getting cart items: " . $e->getMessage());
        }
    }

    // Update cart item quantity
    public function updateCartItemQuantity($cart_id, $quantity) {
        try {
            if ($quantity <= 0) {
                return $this->removeFromCart($cart_id);
            }
            
            $sql = "UPDATE cart SET quantity = ? WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$quantity, $cart_id]);
        } catch(PDOException $e) {
            throw new Exception("Error updating cart item: " . $e->getMessage());
        }
    }

    // Remove item from cart
    public function removeFromCart($cart_id) {
        try {
            $sql = "DELETE FROM cart WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$cart_id]);
        } catch(PDOException $e) {
            throw new Exception("Error removing from cart: " . $e->getMessage());
        }
    }

    // Clear entire cart for a user
    public function clearCart($user_id) {
        try {
            $sql = "DELETE FROM cart WHERE user_id = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$user_id]);
        } catch(PDOException $e) {
            throw new Exception("Error clearing cart: " . $e->getMessage());
        }
    }

    // Clear entire cart for a session
    public function clearCartBySession($session_id) {
        try {
            $sql = "DELETE FROM cart WHERE session_id = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$session_id]);
        } catch(PDOException $e) {
            throw new Exception("Error clearing cart: " . $e->getMessage());
        }
    }

    // Get cart total for a user
    public function getCartTotal($user_id) {
        try {
            $sql = "SELECT SUM(c.quantity * p.price) as total FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$user_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['total'] ?: 0;
        } catch(PDOException $e) {
            throw new Exception("Error calculating cart total: " . $e->getMessage());
        }
    }

    // Get cart total for a session
    public function getCartTotalBySession($session_id) {
        try {
            $sql = "SELECT SUM(c.quantity * p.price) as total FROM cart c JOIN products p ON c.product_id = p.id WHERE c.session_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$session_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['total'] ?: 0;
        } catch(PDOException $e) {
            throw new Exception("Error calculating cart total: " . $e->getMessage());
        }
    }

    // Get cart count for a user
    public function getCartCount($user_id) {
        try {
            $sql = "SELECT SUM(quantity) as count FROM cart WHERE user_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$user_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'] ?: 0;
        } catch(PDOException $e) {
            throw new Exception("Error getting cart count: " . $e->getMessage());
        }
    }

    // Get cart count for a session
    public function getCartCountBySession($session_id) {
        try {
            $sql = "SELECT SUM(quantity) as count FROM cart WHERE session_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$session_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'] ?: 0;
        } catch(PDOException $e) {
            throw new Exception("Error getting cart count: " . $e->getMessage());
        }
    }
}

// Example usage:
/*
$cartFunctions = new CartFunctions();

// Add item to cart
try {
    $result = $cartFunctions->addToCart(1, 5, 2); // Add product ID 5, quantity 2 to user ID 1's cart
    echo "Item added to cart: " . ($result ? 'Success' : 'Failed') . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Get cart items
$cartItems = $cartFunctions->getCartItems(1);
print_r($cartItems);
*/
?>