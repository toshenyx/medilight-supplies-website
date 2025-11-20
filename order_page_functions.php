<?php
require_once 'config.php';
require_once 'order_functions.php';

class OrderPageFunctions {
    private $orderFunctions;

    public function __construct() {
        $this->orderFunctions = new OrderFunctions();
    }

    // Process order form submission
    public function processOrderForm($clientName, $contactEmail, $contactPhone, $productCategory, $quantity, $specificNeeds, $deliveryAddress) {
        try {
            // For now, we'll create a quote request since this matches the order form functionality
            // In a real system, you might want to create an actual order if products are specified
            
            // First, we need a user ID. For now, we'll use a guest user or create one if needed
            // For this example, we'll create a quote request without a specific user (could be linked to a guest account)
            
            // Since we don't have a logged in user in this context, we'll need to create a temporary user
            // or use a guest user ID. For now, let's use user ID 1 as a placeholder
            $userId = 1; // This should be replaced with actual user authentication
            
            // Create quote request
            $quoteRequestId = $this->orderFunctions->createQuoteRequest(
                $userId,
                $productCategory,
                $quantity,
                $specificNeeds,
                $contactEmail,
                $contactPhone,
                $deliveryAddress
            );
            
            return [
                'success' => true,
                'order_id' => $quoteRequestId,
                'message' => 'Quote request submitted successfully. Our team will contact you shortly.'
            ];
        } catch(Exception $e) {
            return [
                'success' => false,
                'message' => 'Error processing order: ' . $e->getMessage()
            ];
        }
    }

    // Process delivery details and finalize order
    public function processDeliveryDetails($userId, $orderId, $deliveryAddress, $items) {
        try {
            // Get the existing order to update its details
            $order = $this->orderFunctions->getOrderById($orderId);
            
            if (!$order) {
                return [
                    'success' => false,
                    'message' => 'Order not found.'
                ];
            }
            
            // Update the order with delivery details
            $sql = "UPDATE orders SET shipping_address = ?, status = 'pending' WHERE id = ?";
            $conn = getConnection();
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute([$deliveryAddress, $orderId]);
            
            if ($result) {
                return [
                    'success' => true,
                    'order_id' => $orderId,
                    'message' => 'Order confirmed successfully. A representative will contact you shortly.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to update order with delivery details.'
                ];
            }
        } catch(Exception $e) {
            return [
                'success' => false,
                'message' => 'Error processing delivery details: ' . $e->getMessage()
            ];
        }
    }

    // Get product categories for the order form
    public function getProductCategories() {
        try {
            $conn = getConnection();
            $sql = "SELECT id, name FROM categories WHERE name IN ('Medical Equipment', 'Analytical Scientific Equipment', 'Emergency & Safety Gear', 'Dental Equipment', 'Radiography Equipment', 'Orthopedic Implants', 'Pharmaceuticals') ORDER BY name";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            throw new Exception("Error getting product categories: " . $e->getMessage());
        }
    }
}

// Example usage:
/*
$orderPageFunctions = new OrderPageFunctions();

// Process order form
$result = $orderPageFunctions->processOrderForm(
    'John Doe',
    'john@example.com',
    '+254712345678',
    'Medical Equipment',
    2,
    'Siemens MRI Model X, required by Q1 2026',
    'P.O. Box 22174 – 00100, Nairobi, Kenya'
);

if ($result['success']) {
    echo "Order processed successfully. Order ID: " . $result['order_id'];
} else {
    echo "Error: " . $result['message'];
}
*/
?>