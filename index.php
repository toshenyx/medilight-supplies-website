<?php
require_once 'config.php';
require_once 'user_functions.php';
require_once 'product_functions.php';
require_once 'order_functions.php';

// Create instances of our classes
$userFunctions = new UserFunctions();
$productFunctions = new ProductFunctions();
$orderFunctions = new OrderFunctions();

// Simple HTML page to demonstrate functionality
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sample Application</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .form-group { margin: 10px 0; }
        input, select { padding: 5px; margin: 5px; width: 200px; }
        button { padding: 8px 15px; background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #45a049; }
    </style>
</head>
<body>
    <h1>Sample Application Dashboard</h1>
    
    <div class="section">
        <h2>Users</h2>
        <a href="#create-user">Create New User</a> | 
        <a href="#view-users">View All Users</a>
        
        <div id="create-user">
            <h3>Create New User</h3>
            <form method="post" action="">
                <div class="form-group">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                    <input type="text" name="first_name" placeholder="First Name" required>
                </div>
                <div class="form-group">
                    <input type="text" name="last_name" placeholder="Last Name" required>
                    <input type="date" name="date_of_birth" placeholder="Date of Birth">
                </div>
                <button type="submit" name="create_user">Create User</button>
            </form>
        </div>
        
        <?php
        if (isset($_POST['create_user'])) {
            try {
                $userId = $userFunctions->createUser(
                    sanitizeInput($_POST['username']),
                    sanitizeInput($_POST['email']),
                    $_POST['password'],
                    sanitizeInput($_POST['first_name']),
                    sanitizeInput($_POST['last_name']),
                    sanitizeInput($_POST['date_of_birth'])
                );
                echo "<p style='color: green;'>User created successfully with ID: $userId</p>";
            } catch (Exception $e) {
                echo "<p style='color: red;'>Error creating user: " . $e->getMessage() . "</p>";
            }
        }
        ?>
        
        <div id="view-users">
            <h3>All Users</h3>
            <?php
            try {
                $users = $userFunctions->getAllUsers();
                if ($users) {
                    echo "<table>";
                    echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Name</th><th>Date of Birth</th><th>Created At</th></tr>";
                    foreach ($users as $user) {
                        echo "<tr>";
                        echo "<td>" . $user['id'] . "</td>";
                        echo "<td>" . $user['username'] . "</td>";
                        echo "<td>" . $user['email'] . "</td>";
                        echo "<td>" . $user['first_name'] . " " . $user['last_name'] . "</td>";
                        echo "<td>" . $user['date_of_birth'] . "</td>";
                        echo "<td>" . $user['created_at'] . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>No users found.</p>";
                }
            } catch (Exception $e) {
                echo "<p style='color: red;'>Error getting users: " . $e->getMessage() . "</p>";
            }
            ?>
        </div>
    </div>
    
    <div class="section">
        <h2>Products</h2>
        <a href="#create-product">Create New Product</a> | 
        <a href="#view-products">View All Products</a>
        
        <div id="create-product">
            <h3>Create New Product</h3>
            <form method="post" action="">
                <div class="form-group">
                    <input type="text" name="product_name" placeholder="Product Name" required>
                    <textarea name="description" placeholder="Description" required></textarea>
                </div>
                <div class="form-group">
                    <input type="number" step="0.01" name="price" placeholder="Price" required>
                    <select name="category_id" required>
                        <option value="">Select Category</option>
                        <option value="1">Electronics</option>
                        <option value="2">Clothing</option>
                        <option value="3">Books</option>
                        <option value="4">Home & Kitchen</option>
                    </select>
                </div>
                <div class="form-group">
                    <input type="number" name="stock_quantity" placeholder="Stock Quantity" value="0">
                </div>
                <button type="submit" name="create_product">Create Product</button>
            </form>
        </div>
        
        <?php
        if (isset($_POST['create_product'])) {
            try {
                $productId = $productFunctions->createProduct(
                    sanitizeInput($_POST['product_name']),
                    sanitizeInput($_POST['description']),
                    sanitizeInput($_POST['price']),
                    sanitizeInput($_POST['category_id']),
                    sanitizeInput($_POST['stock_quantity'])
                );
                echo "<p style='color: green;'>Product created successfully with ID: $productId</p>";
            } catch (Exception $e) {
                echo "<p style='color: red;'>Error creating product: " . $e->getMessage() . "</p>";
            }
        }
        ?>
        
        <div id="view-products">
            <h3>All Products</h3>
            <?php
            try {
                $products = $productFunctions->getAllProducts();
                if ($products) {
                    echo "<table>";
                    echo "<tr><th>ID</th><th>Name</th><th>Description</th><th>Price</th><th>Category</th><th>Stock</th></tr>";
                    foreach ($products as $product) {
                        echo "<tr>";
                        echo "<td>" . $product['id'] . "</td>";
                        echo "<td>" . $product['name'] . "</td>";
                        echo "<td>" . substr($product['description'], 0, 50) . "...</td>";
                        echo "<td>$" . $product['price'] . "</td>";
                        echo "<td>" . $product['category_name'] . "</td>";
                        echo "<td>" . $product['stock_quantity'] . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>No products found.</p>";
                }
            } catch (Exception $e) {
                echo "<p style='color: red;'>Error getting products: " . $e->getMessage() . "</p>";
            }
            ?>
        </div>
    </div>
    
    <div class="section">
        <h2>Orders</h2>
        <a href="#create-order">Create New Order</a> | 
        <a href="#view-orders">View All Orders</a>
        
        <div id="create-order">
            <h3>Create New Order</h3>
            <form method="post" action="">
                <div class="form-group">
                    <select name="user_id" required>
                        <option value="">Select User</option>
                        <?php
                        try {
                            $users = $userFunctions->getAllUsers();
                            foreach ($users as $user) {
                                echo "<option value=\"" . $user['id'] . "\">" . $user['username'] . "</option>";
                            }
                        } catch (Exception $e) {
                            echo "<option value=\"\">Error loading users</option>";
                        }
                        ?>
                    </select>
                    <input type="text" name="shipping_address" placeholder="Shipping Address" required>
                </div>
                <div class="form-group">
                    <input type="number" step="0.01" name="total_amount" placeholder="Total Amount" required>
                </div>
                <button type="submit" name="create_order">Create Order</button>
            </form>
        </div>
        
        <?php
        if (isset($_POST['create_order'])) {
            // For simplicity, creating an order with dummy items
            // In a real application, you would have a form to select products
            try {
                $items = [
                    ['product_id' => 1, 'quantity' => 1, 'unit_price' => 999.99],
                    ['product_id' => 7, 'quantity' => 1, 'unit_price' => 79.99]
                ];
                
                $orderId = $orderFunctions->createOrder(
                    sanitizeInput($_POST['user_id']),
                    sanitizeInput($_POST['total_amount']),
                    sanitizeInput($_POST['shipping_address']),
                    $items
                );
                echo "<p style='color: green;'>Order created successfully with ID: $orderId</p>";
            } catch (Exception $e) {
                echo "<p style='color: red;'>Error creating order: " . $e->getMessage() . "</p>";
            }
        }
        ?>
        
        <div id="view-orders">
            <h3>All Orders</h3>
            <?php
            try {
                $orders = $orderFunctions->getAllOrders();
                if ($orders) {
                    echo "<table>";
                    echo "<tr><th>ID</th><th>User</th><th>Total</th><th>Status</th><th>Date</th><th>Shipping Address</th></tr>";
                    foreach ($orders as $order) {
                        echo "<tr>";
                        echo "<td>" . $order['id'] . "</td>";
                        echo "<td>" . $order['first_name'] . " " . $order['last_name'] . "</td>";
                        echo "<td>$" . $order['total_amount'] . "</td>";
                        echo "<td>" . $order['status'] . "</td>";
                        echo "<td>" . $order['order_date'] . "</td>";
                        echo "<td>" . substr($order['shipping_address'], 0, 30) . "...</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>No orders found.</p>";
                }
            } catch (Exception $e) {
                echo "<p style='color: red;'>Error getting orders: " . $e->getMessage() . "</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>