<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = $_POST['username'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate required fields
    if (empty($username) || empty($new_password) || empty($confirm_password)) {
        $response = [
            'success' => false,
            'message' => 'Please fill in all required fields.'
        ];
    } elseif ($new_password !== $confirm_password) {
        $response = [
            'success' => false,
            'message' => 'New passwords do not match.'
        ];
    } elseif (strlen($new_password) < 6) {
        $response = [
            'success' => false,
            'message' => 'Password must be at least 6 characters long.'
        ];
    } else {
        try {
            $conn = getConnection();
            
            // Check if user exists
            $sql = "SELECT id FROM users WHERE username = ? OR email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Hash the new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                // Update the password
                $sql = "UPDATE users SET password = ? WHERE username = ? OR email = ?";
                $stmt = $conn->prepare($sql);
                $result = $stmt->execute([$hashed_password, $username, $username]);
                
                if ($result) {
                    $response = [
                        'success' => true,
                        'message' => 'Password reset successfully. You can now log in with your new password.'
                    ];
                } else {
                    $response = [
                        'success' => false,
                        'message' => 'Failed to reset password. Please try again.'
                    ];
                }
            } else {
                $response = [
                    'success' => false,
                    'message' => 'User not found. Please check your username/email.'
                ];
            }
        } catch(PDOException $e) {
            $response = [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
} else {
    // Redirect to password reset page if accessed directly
    header('Location: password.html');
    exit;
}
?>