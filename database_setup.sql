-- Database Setup for MediLight Scientific Solutions
-- This file contains table creation queries and sample data insertion for the medical equipment website

-- Create database
CREATE DATABASE IF NOT EXISTS my_app;
USE my_app;

-- Table for users (customers)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Table for brands
CREATE TABLE brands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    brand_name VARCHAR(100) NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    page_url VARCHAR(255) NOT NULL
);

--insert data query into brands
INSERT INTO brands (brand_name, image_url, page_url)
VALUES
('Medtronic', 'images/MEDTRONIC01.jpg', 'Medtronic.html'),
('Zimmer Biomet', 'images/Zimmer-Biomet.png', 'Zimmer_biomet.html'),
('B. Braun', 'images/B.Braun.png', 'b_braun.html');


-- Table for products
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    brand VARCHAR(100) NOT NULL,
    category VARCHAR(100) NOT NULL,
    page_url VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample data insertion
INSERT INTO products (product_name, description, price, image_url, brand, category) VALUES
('Azure Pacemaker', 'Advanced MRI-conditional pacemaker with long battery life.', 650000, 'images/img6.png', 'Medtronic', 'Devices'),
('Vital Signs Monitor', 'Portable patient monitor with advanced physiological parameter tracking.', 180000, 'images/ortho6.png', 'Medtronic', 'Devices'),
('Signia Surgical Stapler', 'Smart powered stapling system for complex procedures.', 45000, 'images/orrtho1.png', 'Medtronic', 'Devices');

-- Table for shopping cart
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    added_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

--Table for wishlist
CREATE TABLE wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    added_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- Table for orders
CREATE TABLE IF NOT EXISTS orders (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) DEFAULT NULL,
    client_name VARCHAR(255) NOT NULL,
    contact_email VARCHAR(255) NOT NULL,
    product_category VARCHAR(100) NOT NULL,
    quantity INT(11) NOT NULL DEFAULT 1,
    specific_needs TEXT,
    order_status ENUM('Pending', 'Processing', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Pending',
    total_amount DECIMAL(10, 2) DEFAULT 0.00,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for order items
CREATE TABLE IF NOT EXISTS order_items (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    order_id INT(11) NOT NULL,
    product_id INT(11) DEFAULT NULL,
    product_name VARCHAR(255) NOT NULL,
    quantity INT(11) NOT NULL DEFAULT 1,
    unit_price DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    subtotal DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Updated orders table with all checkout fields
CREATE TABLE IF NOT EXISTS orders (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) DEFAULT NULL,
    
    -- Customer Information
    customer_type ENUM('individual', 'company') NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    kra_pin VARCHAR(20) DEFAULT NULL,
    tax_exempt TINYINT(1) DEFAULT 0,
    exemption_cert_path VARCHAR(500) DEFAULT NULL,
    
    -- Delivery Information
    delivery_address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    county VARCHAR(100) NOT NULL,
    postal_code VARCHAR(20) DEFAULT NULL,
    delivery_notes TEXT DEFAULT NULL,
    
    -- Payment & Order Details
    payment_method ENUM('mpesa', 'card') NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    voucher_code VARCHAR(50) DEFAULT NULL,
    order_status ENUM('Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled') NOT NULL DEFAULT 'Pending',
    
    -- Timestamps
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Order items table (products in each order)
CREATE TABLE IF NOT EXISTS order_items (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    order_id INT(11) NOT NULL,
    product_id INT(11) DEFAULT NULL,
    product_name VARCHAR(255) NOT NULL,
    quantity INT(11) NOT NULL DEFAULT 1,
    unit_price DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    subtotal DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Vouchers/Discount codes table
CREATE TABLE IF NOT EXISTS vouchers (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    discount_type ENUM('percentage', 'fixed') NOT NULL,
    discount_value DECIMAL(10, 2) NOT NULL,
    max_discount_amount DECIMAL(10, 2) DEFAULT NULL,
    min_purchase_amount DECIMAL(10, 2) DEFAULT NULL,
    usage_limit INT(11) DEFAULT NULL,
    times_used INT(11) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    expiry_date DATE DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Payments table to track payment transactions
CREATE TABLE IF NOT EXISTS payments (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    order_id INT(11) NOT NULL,
    payment_method ENUM('M-Pesa', 'Card', 'Bank Transfer') NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    phone_number VARCHAR(20) DEFAULT NULL,
    transaction_id VARCHAR(255) DEFAULT NULL,
    status ENUM('Pending', 'Completed', 'Failed', 'Refunded') NOT NULL DEFAULT 'Pending',
    payment_date DATETIME DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Indexes for better performance
CREATE INDEX idx_user_id ON orders(user_id);
CREATE INDEX idx_order_status ON orders(order_status);
CREATE INDEX idx_payment_method ON orders(payment_method);
CREATE INDEX idx_created_at ON orders(created_at);
CREATE INDEX idx_email ON orders(email);

CREATE INDEX idx_order_id ON order_items(order_id);
CREATE INDEX idx_product_id ON order_items(product_id);

CREATE INDEX idx_voucher_code ON vouchers(code);
CREATE INDEX idx_voucher_active ON vouchers(is_active);
CREATE INDEX idx_voucher_expiry ON vouchers(expiry_date);

CREATE INDEX idx_payment_order ON payments(order_id);
CREATE INDEX idx_payment_status ON payments(status);

-- Insert sample vouchers for testing
INSERT INTO vouchers (code, discount_type, discount_value, max_discount_amount, min_purchase_amount, usage_limit, is_active, expiry_date) VALUES
('WELCOME10', 'percentage', 10.00, 5000.00, 1000.00, 100, 1, '2025-12-31'),
('SAVE500', 'fixed', 500.00, NULL, 5000.00, 50, 1, '2025-12-31'),
('BLACKFRIDAY', 'percentage', 20.00, 10000.00, 2000.00, 200, 1, '2025-11-30'),
('NEWYEAR25', 'percentage', 25.00, 15000.00, 10000.00, 50, 1, '2026-01-15');
