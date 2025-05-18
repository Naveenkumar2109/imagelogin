 <?php
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $image = $_FILES['image'];

    // Validate the image
    if ($image['error'] === UPLOAD_ERR_OK) {
        $imagePath = $image['tmp_name'];
        $imageType = exif_imagetype($imagePath);

        // Check if the image is a BMP or PNG
        if ($imageType === IMAGETYPE_BMP || $imageType === IMAGETYPE_PNG) {
            // Read the image file into a binary string
            $imageData = file_get_contents($imagePath);

            // Decode the message from the image
            $message = decodeMessageFromImage($imageData);

            // Display the decoded message
            echo "<script>alert('Decoded Message: $message'); window.history.back();</script>";
        } else {
            echo "<script>alert('Only BMP and PNG images are supported!'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Error uploading image!'); window.history.back();</script>";
    }
}

/**
 * Decodes a message from an image using LSB steganography.
 *
 * @param string $imageData The binary image data.
 * @return string The decoded message.
 */
function decodeMessageFromImage($imageData) {
    $binaryMessage = '';
    $message = '';

    // Extract the LSB of each byte
    for ($i = 0; $i < strlen($imageData); $i++) {
        $binaryMessage .= (ord($imageData[$i]) & 1);
        if (strlen($binaryMessage) % 8 === 0) {
            // Convert 8 bits to a character
            $char = chr(bindec(substr($binaryMessage, -8)));
            if (substr($message, -3) === '%%%') {
                break; // Stop decoding if the delimiter is found
            }
            $message .= $char;
        }
    }

    // Remove the delimiter
    return rtrim($message, '%%%');
}
?>