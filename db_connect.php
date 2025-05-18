<?php
$servername = "localhost"; // Server name (usually 'localhost')
$username = "root";        // Default username for XAMPP
$password = "";            // Default password for XAMPP (empty)
$database = "my_app";      // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>