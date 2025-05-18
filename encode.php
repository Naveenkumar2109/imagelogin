<?php
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $image = $_FILES['image'];
    $message = $_POST['message'];

    // Validate the image
    if ($image['error'] === UPLOAD_ERR_OK) {
        $imagePath = $image['tmp_name'];
        $imageType = exif_imagetype($imagePath);

        // Check if the image is a BMP or PNG
        if ($imageType === IMAGETYPE_BMP || $imageType === IMAGETYPE_PNG) {
            // Read the image file into a binary string
            $imageData = file_get_contents($imagePath);

            // Encode the message into the image
            $encodedImageData = encodeMessageIntoImage($imageData, $message);

            // Save the encoded image
            $encodedImagePath = 'uploads/encoded_' . basename($image['name']);
            file_put_contents($encodedImagePath, $encodedImageData);

            // Offer the encoded image for download
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($encodedImagePath) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($encodedImagePath));
            readfile($encodedImagePath);
            exit;
        } else {
            echo "<script>alert('Only BMP and PNG images are supported!'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Error uploading image!'); window.history.back();</script>";
    }
}

/**
 * Encodes a message into an image using LSB steganography.
 *
 * @param string $imageData The binary image data.
 * @param string $message The secret message.
 * @return string The encoded binary image data.
 */
function encodeMessageIntoImage($imageData, $message) {
    // Add a delimiter to the message to mark its end
    $message .= '%%%';

    // Convert the message to binary
    $binaryMessage = '';
    for ($i = 0; $i < strlen($message); $i++) {
        $binaryMessage .= sprintf('%08b', ord($message[$i]));
    }

    // Encode the message into the LSB of the image data
    $messageIndex = 0;
    for ($i = 0; $i < strlen($imageData); $i++) {
        if ($messageIndex < strlen($binaryMessage)) {
            // Modify the LSB of the byte
            $imageData[$i] = chr(ord($imageData[$i]) & ~1 | intval($binaryMessage[$messageIndex]));
            $messageIndex++;
        } else {
            break; // Stop encoding if the message is fully encoded
        }
    }

    return $imageData;
}
?>