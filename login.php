<?php
session_start();
require 'db_connect.php';
require 'mail_config.php'; // Include PHPMailer configuration

// Track failed login attempts
if (!isset($_SESSION['failed_attempts'])) {
    $_SESSION['failed_attempts'] = 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $image = $_FILES['image'];

    // Fetch user data from the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    if (!$stmt) {
        die("Error in SQL query: " . $conn->error);
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // Validate the uploaded image
        $uploadedImagePath = $image['tmp_name'];
        $registeredImagePath = $user['image_path'];

        if (compareImages($uploadedImagePath, $registeredImagePath)) {
            // Login successful
            $_SESSION['username'] = $user['username'];
            $_SESSION['secret_message'] = $user['secret_message'];
            $_SESSION['failed_attempts'] = 0; // Reset failed attempts

            header("Location: dashboard.php");
            exit;
        } else {
            $_SESSION['failed_attempts']++;
            echo "<script>alert('Invalid image!'); window.history.back();</script>";
        }
    } else {
        $_SESSION['failed_attempts']++;

        // Check if failed attempts exceed 2
        if ($_SESSION['failed_attempts'] >= 2) {
            // Send alert email using PHPMailer
            try {
                $mail->addAddress($user['email'], $username); // Add recipient
                $mail->Subject = 'Failed Login Attempts Alert';
                $mail->Body    = "Dear $username,<br><br>There have been 2 failed login attempts on your account. If this was not you, please secure your account immediately.<br><br>Best regards,<br>Naveen's Secure Image Login Team😎";

                $mail->send(); // Send the email
                echo "<script>alert('Invalid username or password! An alert email has been sent to your registered email address.'); window.history.back();</script>";
            } catch (Exception $e) {
                echo "<script>alert('Invalid username or password! Email notification failed. Error: {$mail->ErrorInfo}'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Invalid username or password!'); window.history.back();</script>";
        }
    }
}

/**
 * Compares two images to check if they are the same.
 *
 * @param string $uploadedImagePath Path to the uploaded image.
 * @param string $registeredImagePath Path to the registered image.
 * @return bool True if the images are the same, false otherwise.
 */
function compareImages($uploadedImagePath, $registeredImagePath) {
    // Get the MD5 hash of both images
    $uploadedImageHash = md5_file($uploadedImagePath);
    $registeredImageHash = md5_file($registeredImagePath);

    // Compare the hashes
    return $uploadedImageHash === $registeredImageHash;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="login-bg">
    <div class="form-container">
        <h2>Login</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="file" name="image" required>
            <button type="submit" class="btn">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
    </div>
</body>
</html>