<?php
require_once 'config.php';

class UserFunctions {
    private $conn;

    public function __construct() {
        $this->conn = getConnection();
    }

    // Create a new user
    public function createUser($username, $email, $password, $first_name, $last_name, $phone = null, $company_name = null) {
        try {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO users (username, email, password, first_name, last_name, phone, company_name) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$username, $email, $hashed_password, $first_name, $last_name, $phone, $company_name]);
            
            return $this->conn->lastInsertId();
        } catch(PDOException $e) {
            throw new Exception("Error creating user: " . $e->getMessage());
        }
    }

    // Get user by ID
    public function getUserById($id) {
        try {
            $sql = "SELECT id, username, email, first_name, last_name, phone, company_name, address, city, country, user_type, created_at, updated_at FROM users WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            throw new Exception("Error getting user: " . $e->getMessage());
        }
    }

    // Get all users
    public function getAllUsers() {
        try {
            $sql = "SELECT id, username, email, first_name, last_name, phone, company_name, user_type, created_at, updated_at FROM users ORDER BY created_at DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            throw new Exception("Error getting users: " . $e->getMessage());
        }
    }

    // Update user
    public function updateUser($id, $username, $email, $first_name, $last_name, $phone = null, $company_name = null) {
        try {
            $sql = "UPDATE users SET username = ?, email = ?, first_name = ?, last_name = ?, phone = ?, company_name = ? WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([$username, $email, $first_name, $last_name, $phone, $company_name, $id]);
            
            return $result;
        } catch(PDOException $e) {
            throw new Exception("Error updating user: " . $e->getMessage());
        }
    }

    // Delete user
    public function deleteUser($id) {
        try {
            $sql = "DELETE FROM users WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([$id]);
            
            return $result;
        } catch(PDOException $e) {
            throw new Exception("Error deleting user: " . $e->getMessage());
        }
    }

    // Check if user exists by username or email
    public function userExists($username, $email = null) {
        try {
            if ($email) {
                $sql = "SELECT id FROM users WHERE username = ? OR email = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$username, $email]);
            } else {
                $sql = "SELECT id FROM users WHERE username = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$username]);
            }
            
            return $stmt->rowCount() > 0;
        } catch(PDOException $e) {
            throw new Exception("Error checking user existence: " . $e->getMessage());
        }
    }

    // Authenticate user
    public function authenticateUser($username, $password) {
        try {
            $sql = "SELECT id, username, email, password, first_name, last_name, user_type FROM users WHERE username = ? OR email = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$username, $username]);
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                return $user;
            }
            
            return false;
        } catch(PDOException $e) {
            throw new Exception("Error authenticating user: " . $e->getMessage());
        }
    }

    // Get user by email
    public function getUserByEmail($email) {
        try {
            $sql = "SELECT id, username, email, password, first_name, last_name, user_type FROM users WHERE email = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$email]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            throw new Exception("Error getting user by email: " . $e->getMessage());
        }
    }
}

// Example usage:
/*
$userFunctions = new UserFunctions();

// Create a new user
try {
    $userId = $userFunctions->createUser('newuser', 'newuser@example.com', 'password123', 'New', 'User', '+254712345678', 'New Company');
    echo "User created with ID: $userId\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Get all users
$users = $userFunctions->getAllUsers();
print_r($users);
*/
?>