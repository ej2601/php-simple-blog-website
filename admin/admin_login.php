<?php

// Check if the config.php file is present and there is a valid database connection
if (!file_exists("../config.php")) {
    header("Location: admin_first_time_registration.php");
    exit();
}

require_once("../config.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $adminUsername = $_POST["admin_username"];
    $adminPassword = $_POST["admin_password"];

    // Check if the admin username and password match the database
    $sql = "SELECT * FROM admin_users WHERE username = '$adminUsername'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $storedPassword = $row["password"];

        // Verify the password
        if (password_verify($adminPassword, $storedPassword)) {
            $_SESSION["admin_username"] = $adminUsername;

            // Check if "Remember Me" is checked
            if (isset($_POST["remember_me"])) {
                $cookie_name = "admin_username";
                $cookie_value = $adminUsername;
                $expire = time() + (30 * 24 * 3600); // 30 days

                setcookie($cookie_name, $cookie_value, $expire, "/");
            }

            header("Location: index.php");
        } else {
            $login_error = "Incorrect username or password";
        }
    } else {
        $login_error = "Incorrect username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <!-- Include Bootstrap CSS -->
    <link href="../css_and_javascript/bootstrap-5.3.2-dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h2>Admin Login</h2>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <?php if (isset($login_error)) : ?>
                                <div class="alert alert-danger">
                                    <?php echo $login_error; ?>
                                </div>
                            <?php endif; ?>
                            <div class="form-group">
                                <label for="admin_username">Admin Username:</label>
                                <input type="text" class="form-control" name="admin_username" required>
                            </div>
                            <div class="form-group">
                                <label for="admin_password">Admin Password:</label>
                                <input type="password" class="form-control" name="admin_password" required>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="remember_me" id="remember_me">
                                <label class="form-check-label" for="remember_me">Remember Me</label>
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="../css_and_javascript/bootstrap-5.3.2-dist/js/bootstrap.min.js"></script>
</body>
</html>
