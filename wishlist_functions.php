<?php
require_once 'config.php';

class WishlistFunctions {
    private $conn;

    public function __construct() {
        $this->conn = getConnection();
    }

    // Add item to wishlist
    public function addToWishlist($user_id, $product_id) {
        try {
            // Check if product already exists in wishlist for this user
            $sql_check = "SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?";
            $stmt_check = $this->conn->prepare($sql_check);
            $stmt_check->execute([$user_id, $product_id]);
            $existing_item = $stmt_check->fetch(PDO::FETCH_ASSOC);

            if ($existing_item) {
                // Item already in wishlist
                return false;
            } else {
                // Insert new item to wishlist
                $sql = "INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)";
                $stmt = $this->conn->prepare($sql);
                return $stmt->execute([$user_id, $product_id]);
            }
        } catch(PDOException $e) {
            throw new Exception("Error adding to wishlist: " . $e->getMessage());
        }
    }

    // Get wishlist items for a user
    public function getWishlistItems($user_id) {
        try {
            $sql = "SELECT w.id as wishlist_id, w.added_at, p.id as product_id, p.name, p.description, p.price, p.image_path, p.stock_quantity FROM wishlist w JOIN products p ON w.product_id = p.id WHERE w.user_id = ? AND p.is_active = TRUE ORDER BY w.added_at DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$user_id]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            throw new Exception("Error getting wishlist items: " . $e->getMessage());
        }
    }

    // Remove item from wishlist
    public function removeFromWishlist($wishlist_id) {
        try {
            $sql = "DELETE FROM wishlist WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$wishlist_id]);
        } catch(PDOException $e) {
            throw new Exception("Error removing from wishlist: " . $e->getMessage());
        }
    }

    // Remove item from wishlist by user and product ID
    public function removeFromWishlistByUserAndProduct($user_id, $product_id) {
        try {
            $sql = "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$user_id, $product_id]);
        } catch(PDOException $e) {
            throw new Exception("Error removing from wishlist: " . $e->getMessage());
        }
    }

    // Check if item is in wishlist
    public function isInWishlist($user_id, $product_id) {
        try {
            $sql = "SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$user_id, $product_id]);
            
            return $stmt->rowCount() > 0;
        } catch(PDOException $e) {
            throw new Exception("Error checking wishlist: " . $e->getMessage());
        }
    }

    // Get wishlist count for a user
    public function getWishlistCount($user_id) {
        try {
            $sql = "SELECT COUNT(*) as count FROM wishlist WHERE user_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$user_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'] ?: 0;
        } catch(PDOException $e) {
            throw new Exception("Error getting wishlist count: " . $e->getMessage());
        }
    }
}

// Example usage:
/*
$wishlistFunctions = new WishlistFunctions();

// Add item to wishlist
try {
    $result = $wishlistFunctions->addToWishlist(1, 5); // Add product ID 5 to user ID 1's wishlist
    echo "Item added to wishlist: " . ($result ? 'Success' : 'Already exists') . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Get wishlist items
$wishlistItems = $wishlistFunctions->getWishlistItems(1);
print_r($wishlistItems);
*/
?>