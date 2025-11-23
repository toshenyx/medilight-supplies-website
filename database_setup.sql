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
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled', 'quote_requested') DEFAULT 'pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    shipping_address TEXT,
    billing_address TEXT,
    contact_email VARCHAR(100),
    contact_phone VARCHAR(20),
    special_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Table for order items
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Insert sample data into categories
INSERT INTO categories (name, description, image_path) VALUES
('Medical Equipment', 'Laboratory • Theatre • Sterilization • Wards • Dental • Imaging • Dialysis • Consumables', 'images/lab.jpg'),
('Analytical Scientific Equipment', 'Pharmaceutical • Environment • Food • Oil & Gas • Life Sciences', 'images/eqp.jpg'),
('Emergency & Safety Gear', 'Safety gears, evacuation, and resettlement items for disaster preparedness and emergency services', 'images/gear.jpg'),
('Dental Equipment', 'Complete range of dental supplies and equipment', 'images/den.jpg'),
('Radiography Equipment', 'Digital radiography systems and imaging equipment', 'images/radio1.png'),
('Orthopedic Implants', 'Orthopedic implants and surgical supplies', 'images/orrtho1.png'),
('Pharmaceuticals', 'Essential pharmaceuticals and injectables', 'images/B.Braun.png');

-- Insert sample data into brands
INSERT INTO brands (name, description, logo_path) VALUES
('B. Braun', 'Leading manufacturer of medical devices and pharmaceuticals', 'images/B.Braun.png'),
('Medtronic', 'Global leader in medical technology, services and solutions', 'images/MEDTRONIC01.jpg'),
('Zimmer Biomet', 'Orthopedic and musculoskeletal healthcare solutions', 'images/Zimmer-Biomet.png');

-- Insert sample data into products
INSERT INTO products (name, description, price, category_id, brand_id, image_path, stock_quantity, specifications, warranty_info) VALUES
('MRI Scanner', '3.0 Tesla ultra-high field imaging system', 15500000.00, 1, 2, 'images/mri.jpeg', 5, '3.0 Tesla, 128 channels, Advanced imaging software', '5 years comprehensive warranty'),
('DIALYSIS MACHINE', '128-slice multi-detector computed tomography', 8250000.00, 1, 1, 'images/dialysis machine.jpeg', 8, '128-slice, Multi-detector, Advanced filtration system', '3 years comprehensive warranty'),
('Anesthesia Workstation', 'High-end solution for advanced anesthesia care', 4100000.00, 1, 1, 'images/img6.png', 12, 'Digital flowmeter, Advanced monitoring, Integrated ventilation', '2 years comprehensive warranty'),
('Digital X-Ray Unit', 'High-frequency, floor-mounted digital radiography system', 6800000.00, 5, 2, 'images/radio1.png', 7, 'Digital radiography, High-frequency generator, 150kW power', '4 years comprehensive warranty'),
('Mobile C-Arm', 'Compact and versatile mobile fluoroscopy system for surgical suites', 4500000.00, 5, 3, 'images/radio3.png', 6, 'Mobile fluoroscopy, 12-inch image intensifier, Real-time imaging', '3 years comprehensive warranty'),
('Dental x ray', 'Standard 14x17 inch film for traditional imaging (Box of 100)', 12500.00, 4, 1, 'images/radio5.jpg', 50, '14x17 inch, Medical grade, Box of 100', '1 year parts warranty'),
('Metronidazole Injection', 'For the treatment of anaerobic bacterial infections (Box of 50)', 1500.00, 7, 1, 'images/B.Braun.png', 200, '500mg/100ml, Box of 50', '2 years shelf life'),
('Paracetamol 500mg', 'Fever and pain relief. Supplied in bulk packs (1000 tablets)', 850.00, 7, 1, 'images/tubes1.png', 150, '500mg tablets, 1000 tablets per pack', '3 years shelf life'),
('Dextrose 5% IV', 'Intravenous solution for fluid and calorie restoration (Case of 20 bags)', 3200.00, 7, 1, 'images/eqp.jpg', 80, '5% Dextrose in Water, 500ml bags, Case of 20', '2 years shelf life'),
('Locking Bone Plate Set', 'Comprehensive set of titanium plates and self-tapping screws', 120000.00, 6, 3, 'images/orrtho1.png', 25, 'Titanium alloy, Complete surgical set, Sterile packaging', 'Lifetime warranty'),
('Total Hip System', 'Modular femoral stem and acetabular cup components', 450000.00, 6, 3, 'images/ortho6.png', 15, 'Modular design, Titanium alloy, Complete components', 'Lifetime warranty'),
('orthopedic braces', 'High-torque, battery-operated surgical drilling system', 85000.00, 6, 3, 'images/orthopedic9.png', 30, 'Battery operated, High-torque, Cordless design', '2 years comprehensive warranty'),
('Infusomat Space Pump', 'Compact modular infusion pump for critical care', 115000.00, 1, 1, 'images/img1.png.jpg', 40, 'Modular design, Compact size, Programmable', '3 years comprehensive warranty'),
('Introcan Safety Catheter', 'Peripheral IV catheter with a fully automatic safety shield (Box of 50)', 2800.00, 1, 1, 'images/img2.png.jpg', 100, 'Safety shield, 18G x 1.25", Box of 50', '1 year warranty'),
('Novosyn Surgical Suture', 'Braided absorbable suture for general surgery (Box of 36)', 9500.00, 1, 1, 'images/img4.png', 60, 'Braided, Absorbable, 3-0, Box of 36', '2 years shelf life'),
('Azure Pacemaker', 'Advanced MRI-conditional pacemaker with long battery life', 650000.00, 1, 2, 'images/img6.png', 10, 'MRI-conditional, Long battery life, Advanced features', 'Lifetime warranty'),
('Vital Signs Monitor', 'Portable patient monitor with advanced physiological parameter tracking', 180000.00, 1, 2, 'images/ortho6.png', 20, '12-parameter monitoring, Portable, Battery backup', '3 years comprehensive warranty'),
('Signia Surgical Stapler', 'Smart powered stapling system for complex procedures', 45000.00, 1, 2, 'images/orrtho1.png', 35, 'Powered stapling, Smart technology, Various staples', '2 years comprehensive warranty');

-- Insert sample users
INSERT INTO users (username, email, password, first_name, last_name, phone, company_name, user_type) VALUES
('john_doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John', 'Doe', '+254712345678', 'MediCare Hospital', 'customer'),
('jane_smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane', 'Smith', '+254723456789', 'Research Institute', 'customer'),
('admin_user', 'admin@medilight.co.ke', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System', 'Admin', '+254705071699', 'MediLight Scientific', 'admin');

-- Insert sample orders
INSERT INTO orders (user_id, order_number, total_amount, status, shipping_address, contact_email, contact_phone) VALUES
(1, 'MLT-2025-0001', 15500000.00, 'delivered', 'P.O. Box 22174 – 00100, Nairobi, Kenya', 'john@example.com', '+254712345678'),
(2, 'MLT-2025-0002', 185000.00, 'shipped', 'University of Nairobi, Main Campus, Nairobi', 'jane@example.com', '+254723456789'),
(1, 'MLT-2025-0003', 8250000.00, 'processing', 'Aga Khan Hospital, Nairobi', 'john@example.com', '+254712345678');

-- Insert sample order items
INSERT INTO order_items (order_id, product_id, quantity, unit_price, total_price) VALUES
(1, 1, 1, 15500000.00, 15500000.00),  -- MRI Scanner
(2, 16, 1, 115000.00, 115000.00),      -- Infusomat Space Pump
(2, 17, 1, 2800.00, 2800.00),          -- Introcan Safety Catheter
(2, 18, 2, 9500.00, 19000.00),         -- Novosyn Surgical Suture
(3, 2, 1, 8250000.00, 8250000.00);     -- DIALYSIS MACHINE
