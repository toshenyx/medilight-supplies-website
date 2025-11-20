<?php
require_once 'order_page_functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $clientName = $_POST['clientName'] ?? '';
    $contactEmail = $_POST['contactEmail'] ?? '';
    $productCategory = $_POST['productCategory'] ?? '';
    $quantity = $_POST['quantity'] ?? 1;
    $specificNeeds = $_POST['specificNeeds'] ?? '';
    
    // Since the form doesn't have contact phone and delivery address fields yet,
    // we'll use the client name as delivery address for now
    $contactPhone = $_POST['contactPhone'] ?? '';
    $deliveryAddress = $_POST['deliveryAddress'] ?? $clientName;

    // Validate required fields
    if (empty($clientName) || empty($contactEmail) || empty($productCategory)) {
        $response = [
            'success' => false,
            'message' => 'Please fill in all required fields.'
        ];
    } else {
        // Process the order
        $orderPageFunctions = new OrderPageFunctions();
        $result = $orderPageFunctions->processOrderForm(
            $clientName,
            $contactEmail,
            $contactPhone,
            $productCategory,
            $quantity,
            $specificNeeds,
            $deliveryAddress
        );
        
        $response = $result;
    }
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
} else {
    // Redirect to order page if accessed directly
    header('Location: order_page.html');
    exit;
}
?>