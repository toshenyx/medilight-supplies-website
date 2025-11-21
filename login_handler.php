<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "my_app"; // CHANGE THIS

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare SQL statement
    $stmt = $conn->prepare("SELECT id, username, email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    // Get result
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if ($password === $user['password']) {

            // Store session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            echo "Login successful!";

            // Redirect to dashboard
            header("Location: brands.html");
            exit();

        } else {
            echo "Invalid password!";
        }

    } else {
        echo "No account found with that email!";
    }

    $stmt->close();
}

$conn->close();
?>
