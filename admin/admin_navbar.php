<?php
// Ensure that the user is logged in before displaying the navbar
session_start();
if (!isset($_SESSION["admin_username"])) {
    header("Location: admin_login.php");
    exit();
}
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
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <!-- Navbar brand/logo (customize as needed) -->
            <a class="navbar-brand" href="#">Admin Dashboard</a>

            <!-- Navbar toggle button for mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="fas fa-home"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="make_posts_page.php"><i class="fas fa-pencil-alt"></i> Make Post</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="all_posts.php"><i class="fas fa-list"></i> All Posts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="media.php"><i class="fas fa-images"></i> Media</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_personal_info.php"><i class="fas fa-user"></i> Admin Personal Info</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Include Bootstrap JS -->
    <script src="../css_and_javascript/bootstrap-5.3.2-dist/js/bootstrap.min.js"></script>
</body>
</html>
