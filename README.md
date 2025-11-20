# Sample Application with MySQL and PHP

This project demonstrates a complete setup with MySQL database tables and PHP code to interact with them.

## Database Structure

The application includes the following tables:

1. **users** - Stores user information
2. **products** - Stores product information
3. **categories** - Stores product categories
4. **orders** - Stores order information
5. **order_items** - Stores items in each order

## SQL Setup

The `database_setup.sql` file contains:
- Table creation queries
- Sample data insertion queries
- Foreign key relationships

To set up the database:
1. Execute the queries in `database_setup.sql` in your MySQL server
2. Make sure to update the database credentials in `config.php`

## PHP Files

- `config.php` - Database configuration and connection function
- `user_functions.php` - Functions to manage users (CRUD operations)
- `product_functions.php` - Functions to manage products (CRUD operations)
- `order_functions.php` - Functions to manage orders (CRUD operations)
- `index.php` - A sample web page demonstrating the functionality

## Features

- User management (create, read, update, delete, authenticate)
- Product management (create, read, update, delete, search)
- Order management (create, read, update status, cancel)
- Proper error handling and input sanitization
- Foreign key relationships between tables
- Stock management for products

## Usage

1. Set up your MySQL database using the queries in `database_setup.sql`
2. Update database credentials in `config.php`
3. Run the application using a web server with PHP support
4. Navigate to `index.php` to see the sample interface

## Security Features

- Password hashing using PHP's password_hash() function
- Prepared statements to prevent SQL injection
- Input sanitization functions
- Transaction handling for complex operations

## Sample Data

The database setup includes sample data for:
- 3 users
- 4 categories
- 8 products
- 3 orders
- 5 order items