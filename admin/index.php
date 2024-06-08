<?php
require_once("admin_navbar.php");
require_once("../config.php");

// Check if the "posts" table exists in the database
$sql = "SHOW TABLES LIKE 'posts'";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    // Redirect to the first admin registration page if the "posts" table doesn't exist
    header("Location: make_posts_page.php");
    exit();
}

// Fetch data for the admin dashboard overview
$adminUsername = $_SESSION["admin_username"];

// Number of posts created by the admin
$sqlPosts = "SELECT COUNT(*) AS postCount FROM posts";
$resultPosts = $conn->query($sqlPosts);
$rowPosts = $resultPosts->fetch_assoc();
$postCount = $rowPosts["postCount"];


// Count number of image files in the image folder
$imageFolder = '../images/'; // Change this path to your image folder
$imageFileCount = count(glob($imageFolder . '*.{jpg,jpeg,png,gif}', GLOB_BRACE));

// Fetch latest 4 images from the image folder
$latestImages = glob($imageFolder . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
rsort($latestImages);
$latestImages = array_slice($latestImages, 0, 4);

// Close the MySQL connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Include Bootstrap CSS -->
    <link href="../css_and_javascript/bootstrap-5.3.2-dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Add your custom styles here */
        .overview-card {
            margin-top: 20px;
        }
        .image-preview {
            width: 48.5%; /* Adjust image preview size as needed */
            height: 50%; /* Adjust image preview size as needed */
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <!-- Content -->
    <div class="container mt-4">
        <h2>Welcome, <?php echo $adminUsername; ?>!</h2>

        <!-- Overview Cards -->
        <div class="row">
            <div class="col-md-4">
                <div class="card overview-card">
                    <div class="card-body">
                        <h5 class="card-title">Total Posts</h5>
                        <p class="card-text"><?php echo $postCount; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card overview-card">
                    <div class="card-body">
                        <h5 class="card-title">Image Files</h5>
                        <p class="card-text"><?php echo $imageFileCount; ?></p>
                        <h5 class="card-title">Latest Images</h5>
                        <?php foreach ($latestImages as $image): ?>
                            <img src="<?php echo $image; ?>" alt="Image" class="image-preview">
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="../css_and_javascript/bootstrap-5.3.2-dist/js/bootstrap.min.js"></script>
</body>
</html>
