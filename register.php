<?php
// Include database connection and PHPMailer configuration
require 'db_connect.php';
require 'mail_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password
    $secret_message = $_POST['secret_message'];
    $image = $_FILES['image'];
    $email = $_POST['email'];

    // Validate the uploaded image
    if ($image['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true); // Create the uploads directory if it doesn't exist
        }
        $imagePath = $uploadDir . basename($image['name']);

        // Move the uploaded file to the uploads directory
        if (move_uploaded_file($image['tmp_name'], $imagePath)) {
            // Prepare the SQL query
            $stmt = $conn->prepare("INSERT INTO users (username, password, image_path, secret_message, email) VALUES (?, ?, ?, ?, ?)");
            if (!$stmt) {
                die("Error in SQL query: " . $conn->error);
            }

            // Bind parameters and execute the query
            $stmt->bind_param("sssss", $username, $password, $imagePath, $secret_message, $email);
            if ($stmt->execute()) {
                // Send email alert for successful registration using PHPMailer
                try {
                    $mail->addAddress($email, $username); // Add recipient
                    $mail->Subject = 'Registration Successful';
                    $mail->Body    = "Dear $username,<br><br>Thank you for registering with Secure Image Login. Your account has been successfully created🚀.<br><br>Best regards,<br>Naveen's Secure Image Login Team😎";

                    if ($mail->send()) {
                        echo "<script>alert('Registration Successful. Check your email for confirmation.'); window.location.href='login.php';</script>";
                    } else {
                        echo "<script>alert('Registration Successful, but email notification failed.'); window.location.href='login.php';</script>";
                    }
                } catch (Exception $e) {
                    echo "<script>alert('Error sending email: {$mail->ErrorInfo}'); window.location.href='login.php';</script>";
                }
            } else {
                echo "<script>alert('Error registering user: " . $stmt->error . "');</script>";
            }
        } else {
            echo "<script>alert('Error uploading image!');</script>";
        }
    } else {
        echo "<script>alert('Error uploading image!');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="register-bg">
    <div class="form-container">
        <h2>Register</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="email" name="email" placeholder="Email" required>
            <textarea name="secret_message" placeholder="Enter your secret message" required></textarea>
            <input type="file" name="image" required>
            <button type="submit" class="btn">
                <i class="fas fa-user-plus"></i> Register
            </button>
        </form>
    </div>
</body>
</html>