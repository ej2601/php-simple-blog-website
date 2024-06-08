<?php
require_once("admin_navbar.php");
require_once("../config.php");


// Check if user is logged in
// Include authentication code or check session/login status

// Define media directory
$mediaDirectory = "../images/";

// Function to get list of images in the media directory
function getImagesList($directory) {
    $images = [];
    $files = scandir($directory);
    foreach ($files as $file) {
        if (is_file($directory . $file) && pathinfo($file, PATHINFO_EXTENSION) === "jpg" || pathinfo($file, PATHINFO_EXTENSION) === "jpeg" || pathinfo($file, PATHINFO_EXTENSION) === "png" || pathinfo($file, PATHINFO_EXTENSION) === "gif") {
            $images[] = $file;
        }
    }
    return $images;
}

// Delete image function
function deleteImage($directory, $filename) {
    $filepath = $directory . $filename;
    if (file_exists($filepath)) {
        unlink($filepath);
        return true;
    } else {
        return false;
    }
}

// Handle image deletion if requested
if (isset($_POST['delete_image'])) {
    $imageToDelete = $_POST['delete_image'];
    if (deleteImage($mediaDirectory, $imageToDelete)) {
        // Image deleted successfully
        // Redirect or show success message
    } else {
        // Error deleting image
        // Redirect or show error message
    }
}

// Get list of images in the media directory
$imagesList = getImagesList($mediaDirectory);
?>

<html lang="en">
<head>
    <title>Media Gallery</title>
    <!-- Include any additional CSS -->
    <style>
        .image-container {
            position: relative;
            display: inline-block;
        }
        .delete-button {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: rgba(255, 0, 0, 0.7);
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            line-height: 30px;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .delete-button:hover {
            background-color: rgba(255, 0, 0, 0.9);
        }
    </style>
</head>
<body>
    <div class="container p-2">
        <h1>Media Gallery</h1>
        <div class="row">
            <?php foreach ($imagesList as $image): ?>
                <div class="col-md-3 mb-3">
                    <div class="image-container">
                        <img src="<?php echo $mediaDirectory . $image; ?>" class="img-fluid" alt="<?php echo $image; ?>">
                        <form action="" method="post">
                            <input type="hidden" name="delete_image" value="<?php echo $image; ?>">
                            <button type="submit" class="delete-button">X</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

   
</body>
</html>

