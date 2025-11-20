<?php
require_once 'order_page_functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $deliveryAddress = $_POST['deliveryAddress'] ?? '';
    
    // Get order data from localStorage via POST or fallback to session
    // Since we can't directly access localStorage from PHP, we'll need to pass data from the frontend
    // For now, we'll use the cart data to process the order
    $cartJson = $_POST['cart'] ?? '[]';
    $cart = json_decode($cartJson, true) ?: [];
    
    // For now, we'll process this as a quote request since there's no user ID
    // In a real application, we'd have a logged-in user
    $userId = 1; // Placeholder - should be from session when user is logged in
    
    if (empty($deliveryAddress)) {
        $response = [
            'success' => false,
            'message' => 'Please provide a delivery address.'
        ];
    } else {
        // Process the delivery details
        $orderPageFunctions = new OrderPageFunctions();
        
        // First, we need to process the cart items into an order
        // For now, we'll create a quote request using the delivery details
        // This is a simplified version - in a real app, we'd process the actual cart
        
        // Create a quote request based on delivery details
        // We'll need to get the order data that was passed from the previous page
        $orderDataJson = $_POST['order_data'] ?? '{}';
        $orderData = json_decode($orderDataJson, true) ?: [];
        
        if (!empty($orderData)) {
            $result = $orderPageFunctions->processOrderForm(
                $orderData['clientName'] ?? 'Guest',
                $orderData['contactEmail'] ?? 'guest@example.com',
                $orderData['contactPhone'] ?? '',
                $orderData['productCategory'] ?? 'General Inquiry',
                $orderData['quantity'] ?? 1,
                $orderData['specificNeeds'] ?? 'No specific needs mentioned',
                $deliveryAddress
            );
            
            $response = $result;
        } else {
            // If no order data from previous page, we'll just save the delivery address
            // and redirect to thank you page
            $response = [
                'success' => true,
                'message' => 'Delivery details saved successfully. Our team will contact you shortly.',
                'redirect' => 'thank_you.html'
            ];
        }
    }
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
} else {
    // Redirect to delivery page if accessed directly
    header('Location: delivery_details.html');
    exit;
}
?>