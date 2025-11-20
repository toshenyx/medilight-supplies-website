<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: login_handler.php');
    exit();
}

require_once 'config.php';
require_once 'product_functions.php';
require_once 'order_functions.php';
require_once 'user_functions.php';

$productFunctions = new ProductFunctions();
$orderFunctions = new OrderFunctions();
$userFunctions = new UserFunctions();

// Get statistics
$productsCount = count($productFunctions->getAllProducts());
$ordersCount = count($orderFunctions->getAllOrders());
$usersCount = count($userFunctions->getAllUsers());
$pendingOrdersCount = count($orderFunctions->getOrdersByStatus('pending'));

// Get recent orders
$recentOrders = array_slice($orderFunctions->getAllOrders(), 0, 5);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - MediLight Scientific Solutions</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        
        .header {
            background-color: #007bff;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logout-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        .logout-btn:hover {
            background-color: #c82333;
        }
        
        .container {
            display: flex;
        }
        
        .sidebar {
            width: 250px;
            background-color: white;
            padding: 20px 0;
            height: calc(100vh - 70px);
            position: fixed;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        
        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar li {
            padding: 12px 20px;
            border-left: 4px solid transparent;
        }
        
        .sidebar li a {
            text-decoration: none;
            color: #333;
            display: block;
        }
        
        .sidebar li:hover, .sidebar li.active {
            background-color: #e9ecef;
            border-left-color: #007bff;
        }
        
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-card h3 {
            margin: 0;
            color: #6c757d;
            font-size: 16px;
        }
        
        .stat-card .value {
            font-size: 32px;
            font-weight: bold;
            color: #007bff;
            margin: 10px 0;
        }
        
        .section {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .section h2 {
            margin-top: 0;
            color: #333;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            padding: 4px 8px;
            border-radius: 4px;
        }
        
        .status-processing {
            background-color: #cce5ff;
            color: #004085;
            padding: 4px 8px;
            border-radius: 4px;
        }
        
        .status-shipped {
            background-color: #d4edda;
            color: #155724;
            padding: 4px 8px;
            border-radius: 4px;
        }
        
        .status-delivered {
            background-color: #d1ecf1;
            color: #0c5460;
            padding: 4px 8px;
            border-radius: 4px;
        }
        
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
            padding: 4px 8px;
            border-radius: 4px;
        }
        
        .status-quote_requested {
            background-color: #e2e3e5;
            color: #383d41;
            padding: 4px 8px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Admin Dashboard</h1>
        <div class="user-info">
            <span>Welcome, <?php echo $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?>!</span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="sidebar">
            <ul>
                <li class="active"><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="admin_products.php">Products</a></li>
                <li><a href="admin_orders.php">Orders</a></li>
                <li><a href="admin_users.php">Users</a></li>
                <li><a href="admin_categories.php">Categories</a></li>
                <li><a href="admin_brands.php">Brands</a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <div class="stats-container">
                <div class="stat-card">
                    <h3>TOTAL PRODUCTS</h3>
                    <div class="value"><?php echo $productsCount; ?></div>
                </div>
                <div class="stat-card">
                    <h3>TOTAL ORDERS</h3>
                    <div class="value"><?php echo $ordersCount; ?></div>
                </div>
                <div class="stat-card">
                    <h3>TOTAL USERS</h3>
                    <div class="value"><?php echo $usersCount; ?></div>
                </div>
                <div class="stat-card">
                    <h3>PENDING ORDERS</h3>
                    <div class="value"><?php echo $pendingOrdersCount; ?></div>
                </div>
            </div>
            
            <div class="section">
                <h2>Recent Orders</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                            <td><?php echo htmlspecialchars($order['company_name'] ?: $order['first_name'] . ' ' . $order['last_name']); ?></td>
                            <td><?php echo date('M j, Y', strtotime($order['order_date'])); ?></td>
                            <td>Ksh <?php echo number_format($order['total_amount'], 2); ?></td>
                            <td>
                                <span class="status-<?php echo $order['status']; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $order['status'])); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recentOrders)): ?>
                        <tr>
                            <td colspan="5">No recent orders</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="section">
                <h2>Quick Actions</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    <a href="admin_products.php" style="display: block; padding: 15px; background-color: #007bff; color: white; text-align: center; text-decoration: none; border-radius: 5px;">Manage Products</a>
                    <a href="admin_orders.php" style="display: block; padding: 15px; background-color: #28a745; color: white; text-align: center; text-decoration: none; border-radius: 5px;">Manage Orders</a>
                    <a href="admin_users.php" style="display: block; padding: 15px; background-color: #ffc107; color: white; text-align: center; text-decoration: none; border-radius: 5px;">Manage Users</a>
                    <a href="admin_categories.php" style="display: block; padding: 15px; background-color: #17a2b8; color: white; text-align: center; text-decoration: none; border-radius: 5px;">Manage Categories</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>