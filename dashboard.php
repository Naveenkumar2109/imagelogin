<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="dashboard-bg">
    <div class="dashboard-container">
        <h2>Welcome, <?php echo $_SESSION['username']; ?></h2>
        <p>Your Secret Message: <?php echo $_SESSION['secret_message']; ?></p>
        <a href="logout.php" class="btn logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</body>
</html>