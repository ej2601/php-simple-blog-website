<?php
require_once("admin_navbar.php");
require_once("../config.php");


// Fetch admin details
$adminId = $_SESSION["admin_username"];

// Fetch admin data from database
$stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
$stmt->execute([$adminId]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// Update username and password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Handle password change
    if (isset($_POST['newPassword']) && !empty(trim($_POST['newPassword']))) {
        $newPassword = password_hash(trim($_POST['newPassword']), PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE admin_users SET password = ? WHERE username = ?");
        $stmt->execute([$newPassword, $adminId]);
    }

    // Handle username change
    if (isset($_POST['newUsername']) && !empty(trim($_POST['newUsername']))) {
        $newUsername = trim($_POST['newUsername']);
        $stmt = $pdo->prepare("UPDATE admin_users SET username = ? WHERE username = ?");
        $stmt->execute([$newUsername, $adminId]);
        // Update session with new username
        $_SESSION["admin_username"] = $newUsername;
    }


    // Redirect to the same page to prevent form resubmission
    header("Location: admin_personal_info.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Personal Info</title>
    <!-- Include Bootstrap CSS -->
    <link href="../css_and_javascript/bootstrap-5.3.2-dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Admin Personal Info</h2>
        <form action="admin_personal_info.php" method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="newUsername" value="<?php echo htmlspecialchars($admin['username']); ?>">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">New Password</label>
                <input type="password" class="form-control" id="password" name="newPassword">
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="../css_and_javascript/bootstrap-5.3.2-dist/js/bootstrap.min.js"></script>
</body>
</html>
