<?php
require_once 'config.php';

class ProductFunctions {
    private $conn;

    public function __construct() {
        $this->conn = getConnection();
    }

    // Create a new product
    public function createProduct($name, $description, $price, $category_id, $brand_id = null, $image_path = null, $stock_quantity = 0, $specifications = null, $warranty_info = null) {
        try {
            $sql = "INSERT INTO products (name, description, price, category_id, brand_id, image_path, stock_quantity, specifications, warranty_info) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$name, $description, $price, $category_id, $brand_id, $image_path, $stock_quantity, $specifications, $warranty_info]);
            
            return $this->conn->lastInsertId();
        } catch(PDOException $e) {
            throw new Exception("Error creating product: " . $e->getMessage());
        }
    }

    // Get product by ID
    public function getProductById($id) {
        try {
            $sql = "SELECT p.id, p.name, p.description, p.price, p.category_id, p.brand_id, p.image_path, p.stock_quantity, p.specifications, p.warranty_info, p.created_at, p.updated_at, c.name as category_name, b.name as brand_name FROM products p LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN brands b ON p.brand_id = b.id WHERE p.id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            throw new Exception("Error getting product: " . $e->getMessage());
        }
    }

    // Get all products
    public function getAllProducts() {
        try {
            $sql = "SELECT p.id, p.name, p.description, p.price, p.category_id, p.brand_id, p.image_path, p.stock_quantity, p.specifications, p.warranty_info, p.created_at, p.updated_at, c.name as category_name, b.name as brand_name FROM products p LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN brands b ON p.brand_id = b.id WHERE p.is_active = TRUE ORDER BY p.created_at DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            throw new Exception("Error getting products: " . $e->getMessage());
        }
    }

    // Get products by category
    public function getProductsByCategory($category_id) {
        try {
            $sql = "SELECT p.id, p.name, p.description, p.price, p.category_id, p.brand_id, p.image_path, p.stock_quantity, p.specifications, p.warranty_info, p.created_at, p.updated_at, c.name as category_name, b.name as brand_name FROM products p LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN brands b ON p.brand_id = b.id WHERE p.category_id = ? AND p.is_active = TRUE ORDER BY p.created_at DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$category_id]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            throw new Exception("Error getting products by category: " . $e->getMessage());
        }
    }

    // Get products by brand
    public function getProductsByBrand($brand_id) {
        try {
            $sql = "SELECT p.id, p.name, p.description, p.price, p.category_id, p.brand_id, p.image_path, p.stock_quantity, p.specifications, p.warranty_info, p.created_at, p.updated_at, c.name as category_name, b.name as brand_name FROM products p LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN brands b ON p.brand_id = b.id WHERE p.brand_id = ? AND p.is_active = TRUE ORDER BY p.created_at DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$brand_id]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            throw new Exception("Error getting products by brand: " . $e->getMessage());
        }
    }

    // Update product
    public function updateProduct($id, $name, $description, $price, $category_id, $brand_id = null, $image_path = null, $stock_quantity, $specifications = null, $warranty_info = null) {
        try {
            $sql = "UPDATE products SET name = ?, description = ?, price = ?, category_id = ?, brand_id = ?, image_path = ?, stock_quantity = ?, specifications = ?, warranty_info = ? WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([$name, $description, $price, $category_id, $brand_id, $image_path, $stock_quantity, $specifications, $warranty_info, $id]);
            
            return $result;
        } catch(PDOException $e) {
            throw new Exception("Error updating product: " . $e->getMessage());
        }
    }

    // Delete product (soft delete by setting is_active to false)
    public function deleteProduct($id) {
        try {
            $sql = "UPDATE products SET is_active = FALSE WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([$id]);
            
            return $result;
        } catch(PDOException $e) {
            throw new Exception("Error deleting product: " . $e->getMessage());
        }
    }

    // Search products by name
    public function searchProducts($searchTerm) {
        try {
            $sql = "SELECT p.id, p.name, p.description, p.price, p.category_id, p.brand_id, p.image_path, p.stock_quantity, p.specifications, p.warranty_info, p.created_at, p.updated_at, c.name as category_name, b.name as brand_name FROM products p LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN brands b ON p.brand_id = b.id WHERE p.name LIKE ? AND p.is_active = TRUE ORDER BY p.created_at DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(["%$searchTerm%"]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            throw new Exception("Error searching products: " . $e->getMessage());
        }
    }

    // Update stock quantity
    public function updateStock($product_id, $quantity) {
        try {
            $sql = "UPDATE products SET stock_quantity = ? WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([$quantity, $product_id]);
            
            return $result;
        } catch(PDOException $e) {
            throw new Exception("Error updating stock: " . $e->getMessage());
        }
    }

    // Get all categories
    public function getAllCategories() {
        try {
            $sql = "SELECT * FROM categories ORDER BY name ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            throw new Exception("Error getting categories: " . $e->getMessage());
        }
    }

    // Get all brands
    public function getAllBrands() {
        try {
            $sql = "SELECT * FROM brands ORDER BY name ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            throw new Exception("Error getting brands: " . $e->getMessage());
        }
    }

    // Get featured products (products with higher stock or special status)
    public function getFeaturedProducts($limit = 8) {
        try {
            $sql = "SELECT p.id, p.name, p.description, p.price, p.category_id, p.brand_id, p.image_path, p.stock_quantity, p.specifications, p.warranty_info, p.created_at, p.updated_at, c.name as category_name, b.name as brand_name FROM products p LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN brands b ON p.brand_id = b.id WHERE p.is_active = TRUE AND p.stock_quantity > 0 ORDER BY p.created_at DESC LIMIT ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$limit]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            throw new Exception("Error getting featured products: " . $e->getMessage());
        }
    }
}

// Example usage:
/*
$productFunctions = new ProductFunctions();

// Create a new product
try {
    $productId = $productFunctions->createProduct('New Medical Device', 'Description of the new medical device', 500000.00, 1, 1, 'images/new_device.jpg', 10, 'Specifications here', '2 years warranty');
    echo "Product created with ID: $productId\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Get all products
$products = $productFunctions->getAllProducts();
print_r($products);
*/
?>