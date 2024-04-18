<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Compressor</title>
    <link rel="stylesheet" href="./style.css">
</head>

<body>
    <nav>
        <div class="containar">
            <h1>Compress</h1>
        </div>
    </nav>
    <div class="f-div">
        <form action="" method="post" enctype="multipart/form-data">
            <input type="file" name="image" accept="image/*" class="inputfile" id="fileInput" />
            <input type="submit" name="submit" value="Upload" class="uploadbtm" />
        </form>
    </div>

</body>

</html>
<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_image";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['submit'])) {
    // Compress and upload image
    if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['image']['tmp_name'];
        $name = basename($_FILES['image']['name']);

        // Load the original image
        $original_image = imagecreatefromjpeg($tmp_name);
        $width = imagesx($original_image);
        $height = imagesy($original_image);

        // Create a new image with the same dimensions
        $compressed_image = imagecreatetruecolor($width, $height);

        // Compress the image
        imagecopyresampled($compressed_image, $original_image, 0, 0, 0, 0, $width, $height, $width, $height);

        // Save the compressed image to uploads folder
        $upload_path = "uploads/";
        $compressed_image_name = $upload_path . "compressed_" . $name;
        imagejpeg($compressed_image, $compressed_image_name, 50); // Change the quality as needed

        // Save image info to database
        $sql = "INSERT INTO images (name, path, width, height) VALUES ('$name', '$compressed_image_name', $width, $height)";
        if ($conn->query($sql) === TRUE) {
            echo "<div class='massege'>Image Compress successfully.</div>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        // Clean up resources
        imagedestroy($original_image);
        imagedestroy($compressed_image);
    } else {
        echo "Error uploading file.";
    }
}

// Close database connection
$conn->close();
?>
<?php
// Display download link for the compressed image
if (isset($compressed_image_name)) {
    echo "<div class='sdiv'><a href='$compressed_image_name' class='downloadBtn' download >Download Compressed Image</a></div>";
}
?>