<?php
require_once 'config.php';

class OrderFunctions {
    private $conn;

    public function __construct() {
        $this->conn = getConnection();
    }

    // Create a new order
    public function createOrder($user_id, $total_amount, $shipping_address, $items, $contact_email = null, $contact_phone = null, $billing_address = null, $special_notes = null) {
        try {
            $this->conn->beginTransaction();
            
            // Generate unique order number
            $order_number = 'MLT-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
            // Insert order
            $sql = "INSERT INTO orders (user_id, order_number, total_amount, shipping_address, contact_email, contact_phone, billing_address, special_notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$user_id, $order_number, $total_amount, $shipping_address, $contact_email, $contact_phone, $billing_address, $special_notes]);
            $order_id = $this->conn->lastInsertId();
            
            // Insert order items
            foreach ($items as $item) {
                $product_id = $item['product_id'];
                $quantity = $item['quantity'];
                $unit_price = $item['unit_price'];
                $total_price = $quantity * $unit_price;
                
                $sql_item = "INSERT INTO order_items (order_id, product_id, quantity, unit_price, total_price) VALUES (?, ?, ?, ?, ?)";
                $stmt_item = $this->conn->prepare($sql_item);
                $stmt_item->execute([$order_id, $product_id, $quantity, $unit_price, $total_price]);
                
                // Update product stock
                $sql_update_stock = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?";
                $stmt_update = $this->conn->prepare($sql_update_stock);
                $stmt_update->execute([$quantity, $product_id]);
            }
            
            $this->conn->commit();
            return $order_id;
        } catch(PDOException $e) {
            $this->conn->rollback();
            throw new Exception("Error creating order: " . $e->getMessage());
        }
    }

    // Create a quote request (special order type)
    public function createQuoteRequest($user_id, $product_category, $quantity, $specific_needs, $contact_email, $contact_phone, $delivery_address) {
        try {
            $this->conn->beginTransaction();
            
            // Generate unique order number
            $order_number = 'MLT-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
            // Insert quote request order
            $sql = "INSERT INTO orders (user_id, order_number, total_amount, shipping_address, contact_email, contact_phone, special_notes, status) VALUES (?, ?, 0, ?, ?, ?, ?, 'quote_requested')";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$user_id, $order_number, $delivery_address, $contact_email, $contact_phone, $specific_needs]);
            $order_id = $this->conn->lastInsertId();
            
            $this->conn->commit();
            return $order_id;
        } catch(PDOException $e) {
            $this->conn->rollback();
            throw new Exception("Error creating quote request: " . $e->getMessage());
        }
    }

    // Get order by ID
    public function getOrderById($id) {
        try {
            // Get order details
            $sql = "SELECT o.id, o.user_id, o.order_number, o.total_amount, o.status, o.order_date, o.shipping_address, o.billing_address, o.contact_email, o.contact_phone, o.special_notes, o.created_at, o.updated_at, u.username, u.email, u.first_name, u.last_name, u.company_name FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($order) {
                // Get order items
                $sql_items = "SELECT oi.id, oi.product_id, oi.quantity, oi.unit_price, oi.total_price, p.name as product_name, p.image_path as product_image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?";
                $stmt_items = $this->conn->prepare($sql_items);
                $stmt_items->execute([$id]);
                $order['items'] = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
            }
            
            return $order;
        } catch(PDOException $e) {
            throw new Exception("Error getting order: " . $e->getMessage());
        }
    }

    // Get all orders
    public function getAllOrders() {
        try {
            $sql = "SELECT o.id, o.user_id, o.order_number, o.total_amount, o.status, o.order_date, o.shipping_address, o.contact_email, o.contact_phone, o.created_at, o.updated_at, u.username, u.first_name, u.last_name, u.company_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.order_date DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            throw new Exception("Error getting orders: " . $e->getMessage());
        }
    }

    // Get orders by user ID
    public function getOrdersByUserId($user_id) {
        try {
            $sql = "SELECT o.id, o.order_number, o.total_amount, o.status, o.order_date, o.shipping_address, o.contact_email, o.contact_phone FROM orders o WHERE o.user_id = ? ORDER BY o.order_date DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$user_id]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            throw new Exception("Error getting orders by user: " . $e->getMessage());
        }
    }

    // Get orders by status
    public function getOrdersByStatus($status) {
        try {
            $sql = "SELECT o.id, o.user_id, o.order_number, o.total_amount, o.status, o.order_date, o.shipping_address, o.contact_email, o.contact_phone, o.created_at, o.updated_at, u.username, u.first_name, u.last_name, u.company_name FROM orders o JOIN users u ON o.user_id = u.id WHERE o.status = ? ORDER BY o.order_date DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$status]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            throw new Exception("Error getting orders by status: " . $e->getMessage());
        }
    }

    // Update order status
    public function updateOrderStatus($order_id, $status) {
        try {
            $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'quote_requested'];
            if (!in_array($status, $validStatuses)) {
                throw new Exception("Invalid order status: $status");
            }
            
            $sql = "UPDATE orders SET status = ? WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([$status, $order_id]);
            
            return $result;
        } catch(PDOException $e) {
            throw new Exception("Error updating order status: " . $e->getMessage());
        }
    }

    // Cancel order
    public function cancelOrder($order_id) {
        try {
            // Get order items to restore stock
            $sql_items = "SELECT product_id, quantity FROM order_items WHERE order_id = ?";
            $stmt_items = $this->conn->prepare($sql_items);
            $stmt_items->execute([$order_id]);
            $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
            
            $this->conn->beginTransaction();
            
            // Update order status to cancelled
            $this->updateOrderStatus($order_id, 'cancelled');
            
            // Restore stock for each item
            foreach ($items as $item) {
                $sql_update_stock = "UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?";
                $stmt_update = $this->conn->prepare($sql_update_stock);
                $stmt_update->execute([$item['quantity'], $item['product_id']]);
            }
            
            $this->conn->commit();
            return true;
        } catch(PDOException $e) {
            $this->conn->rollback();
            throw new Exception("Error cancelling order: " . $e->getMessage());
        }
    }

    // Get order total
    public function getOrderTotal($order_id) {
        try {
            $sql = "SELECT SUM(total_price) as total FROM order_items WHERE order_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$order_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['total'];
        } catch(PDOException $e) {
            throw new Exception("Error calculating order total: " . $e->getMessage());
        }
    }

    // Add item to existing order
    public function addItemToOrder($order_id, $product_id, $quantity, $unit_price) {
        try {
            $total_price = $quantity * $unit_price;
            
            $sql = "INSERT INTO order_items (order_id, product_id, quantity, unit_price, total_price) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([$order_id, $product_id, $quantity, $unit_price, $total_price]);
            
            // Update product stock
            $sql_update_stock = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?";
            $stmt_update = $this->conn->prepare($sql_update_stock);
            $stmt_update->execute([$quantity, $product_id]);
            
            return $result;
        } catch(PDOException $e) {
            throw new Exception("Error adding item to order: " . $e->getMessage());
        }
    }

    // Get pending quote requests
    public function getQuoteRequests() {
        try {
            $sql = "SELECT o.id, o.user_id, o.order_number, o.total_amount, o.status, o.order_date, o.special_notes, o.shipping_address, o.contact_email, o.contact_phone, u.username, u.first_name, u.last_name, u.company_name FROM orders o JOIN users u ON o.user_id = u.id WHERE o.status = 'quote_requested' ORDER BY o.order_date DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            throw new Exception("Error getting quote requests: " . $e->getMessage());
        }
    }
}

// Example usage:
/*
$orderFunctions = new OrderFunctions();

// Create a new order
try {
    $items = [
        ['product_id' => 1, 'quantity' => 1, 'unit_price' => 15500000.00],
        ['product_id' => 7, 'quantity' => 1, 'unit_price' => 1500.00]
    ];
    $orderId = $orderFunctions->createOrder(1, 15501500.00, 'P.O. Box 22174 – 00100, Nairobi, Kenya', $items, 'john@example.com', '+254712345678');
    echo "Order created with ID: $orderId\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Get all orders
$orders = $orderFunctions->getAllOrders();
print_r($orders);
*/
?>