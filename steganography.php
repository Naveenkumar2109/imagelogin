<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Steganography | Secure Image Login</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="steganography-bg">
    <!-- Back Button (Top-Left Corner) -->
    <a href="index.php" class="back-btn">
        <i class="fas fa-arrow-left"></i> Back
    </a>

    <div class="form-container">
        <h2>Image Steganography</h2>
        
        <!-- Encode Section -->
        <div class="section">
            <h3>Encode Message</h3>
            <input type="file" id="imageInput" accept="image/*" class="file-input">
            <textarea id="messageInput" placeholder="Enter secret message" class="textarea"></textarea>
            <button onclick="encodeMessage()" class="btn">
                <i class="fas fa-lock"></i> Encode & Download
            </button>
        </div>

        <!-- Decode Section -->
        <div class="section">
            <h3>Decode Message</h3>
            <input type="file" id="decodeImageInput" accept="image/*" class="file-input">
            <button onclick="decodeMessage()" class="btn">
                <i class="fas fa-unlock"></i> Decode Message
            </button>
            <p id="decodedMessage" class="decoded-message"></p>
        </div>

        <!-- Canvas (hidden) -->
        <canvas id="canvas" style="display: none;"></canvas>
    </div>

    <script>
        // JavaScript functions for encoding and decoding
        function encodeMessage() {
            const fileInput = document.getElementById('imageInput');
            const message = document.getElementById('messageInput').value;
            
            if (!fileInput.files.length || !message) {
                alert("Please select an image and enter a message.");
                return;
            }

            const reader = new FileReader();
            reader.onload = function(event) {
                const img = new Image();
                img.onload = function() {
                    const canvas = document.getElementById('canvas');
                    const ctx = canvas.getContext('2d');

                    canvas.width = img.width;
                    canvas.height = img.height;
                    ctx.drawImage(img, 0, 0);

                    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    const data = imageData.data;

                    let binaryMessage = message.split('').map(char => char.charCodeAt(0).toString(2).padStart(8, '0')).join('') + '1111111111111110'; // End marker

                    let bitIndex = 0;
                    for (let i = 0; i < data.length; i += 4) { // Modify only RGB values
                        if (bitIndex < binaryMessage.length) {
                            data[i] = (data[i] & ~1) | parseInt(binaryMessage[bitIndex], 2);
                            bitIndex++;
                        }
                    }

                    ctx.putImageData(imageData, 0, 0);
                    const encodedImage = canvas.toDataURL("image/png");
                    
                    const link = document.createElement('a');
                    link.href = encodedImage;
                    link.download = 'stego_image.png';
                    link.click();
                };
                img.src = event.target.result;
            };
            reader.readAsDataURL(fileInput.files[0]);
        }

        function decodeMessage() {
            const fileInput = document.getElementById('decodeImageInput');
            
            if (!fileInput.files.length) {
                alert("Please select an image to decode.");
                return;
            }

            const reader = new FileReader();
            reader.onload = function(event) {
                const img = new Image();
                img.onload = function() {
                    const canvas = document.getElementById('canvas');
                    const ctx = canvas.getContext('2d');

                    canvas.width = img.width;
                    canvas.height = img.height;
                    ctx.drawImage(img, 0, 0);

                    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    const data = imageData.data;

                    let binaryMessage = "";
                    for (let i = 0; i < data.length; i += 4) {
                        binaryMessage += (data[i] & 1).toString();
                    }

                    let bytes = binaryMessage.match(/.{1,8}/g);
                    let message = bytes.map(byte => String.fromCharCode(parseInt(byte, 2))).join('');

                    message = message.split('þ')[0]; // Stop at delimiter
                    document.getElementById('decodedMessage').innerText = "Decoded Message: " + message;
                };
                img.src = event.target.result;
            };
            reader.readAsDataURL(fileInput.files[0]);
        }
    </script>
</body>
</html>