<?php
// mail_config.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// Create a new PHPMailer instance
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP(); // Use SMTP
    $mail->Host       = 'smtp.gmail.com'; // SMTP server (e.g., Gmail, Outlook)
    $mail->SMTPAuth   = true; // Enable SMTP authentication
    $mail->Username   = 'username81221@gmail.com'; // SMTP username (your email address)
    $mail->Password   = 'iorg vrmo nocx awjq'; // SMTP password (your email password or app password)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
    $mail->Port       = 587; // TCP port to connect to (587 for TLS)

    // Recipients
    $mail->setFrom('your_email@gmail.com', 'Secure Image Login'); // Sender email and name
    $mail->addReplyTo('your_email@gmail.com', 'Secure Image Login'); // Reply-to email and name

    // Content
    $mail->isHTML(true); // Set email format to HTML
} catch (Exception $e) {
    echo "PHPMailer configuration error: {$mail->ErrorInfo}";
}
?>