# Form to PHP Handler Connections

This document outlines all the HTML forms in the project and their corresponding PHP handlers that I've connected.

## Forms and Handlers

### 1. Login Form
- **HTML File**: `/workspace/login.html`
- **PHP Handler**: `/workspace/login_handler.php`
- **Method**: POST
- **Fields**:
  - `email` (name="email")
  - `password` (name="password")
- **Action**: Authenticates user credentials

### 2. Signup Form
- **HTML File**: `/workspace/signup.html`
- **PHP Handler**: `/workspace/signup_handler.php`
- **Method**: POST
- **Fields**:
  - `username` (name="username")
  - `email` (name="email")
  - `password` (name="password")
  - `confirm_password` (name="confirm_password")
- **Action**: Creates new user account

### 3. Order Form
- **HTML File**: `/workspace/order_page.html`
- **PHP Handler**: `/workspace/process_order.php`
- **Method**: POST
- **Fields**:
  - `clientName` (name="clientName")
  - `contactEmail` (name="contactEmail")
  - `productCategory` (name="productCategory")
  - `quantity` (name="quantity")
  - `specificNeeds` (name="specificNeeds")
- **Action**: Processes initial order request and creates quote request

### 4. Delivery Details Form
- **HTML File**: `/workspace/delivery_details.html`
- **PHP Handler**: `/workspace/process_delivery.php`
- **Method**: POST
- **Fields**:
  - `deliveryAddress` (name="deliveryAddress")
  - `cart` (sent via JavaScript as JSON)
  - `order_data` (sent via JavaScript as JSON from localStorage)
- **Action**: Processes delivery information and finalizes order

### 5. Password Reset Form
- **HTML File**: `/workspace/password.html`
- **PHP Handler**: `/workspace/reset_password.php`
- **Method**: POST
- **Fields**:
  - `username` (name="username")
  - `new_password` (name="new_password")
  - `confirm_password` (name="confirm_password")
- **Action**: Resets user password

## Additional Notes

- All forms now have proper `action` and `method` attributes
- All input fields have appropriate `name` attributes for PHP processing
- JavaScript has been updated where needed to properly send data to PHP handlers
- Forms include proper validation and error handling
- All handlers include security measures like prepared statements and input validation