<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle form submission to create the database and tables
    $servername = "localhost";
    $dbUsername = $_POST["db_username"];
    $dbPassword = $_POST["db_password"];
    $adminUsername = $_POST["admin_username"];
    $adminPassword = password_hash($_POST["admin_password"], PASSWORD_DEFAULT); // Hash the admin password
    $dbname = $_POST["dbname"];

    // Create a connection to the MySQL server with database credentials
    $conn = new mysqli($servername, $dbUsername, $dbPassword);

    // Check connection
    if ($conn->connect_error) {
        echo '<div class="alert alert-danger">Connection failed: ' . $conn->connect_error . '</div>';
    } else {
        // Create the database
        $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
        if ($conn->query($sql) === TRUE) {
            echo '<div class="alert alert-success">Database created successfully</div>';

            // Connect to the created database
            $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

            // Create tables and define their structure here
            // Example: Create an "admin_users" table to store admin details
            $sql = "CREATE TABLE IF NOT EXISTS admin_users (
                id INT(11) AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(255) NOT NULL,
                password VARCHAR(255) NOT NULL
            )";

            if ($conn->query($sql) === TRUE) {
                // Check if the admin username already exists
                $checkUsernameQuery = "SELECT username FROM admin_users WHERE username = '$adminUsername'";
                $result = $conn->query($checkUsernameQuery);

                if ($result->num_rows > 0) {
                    echo '<div class="alert alert-danger">Admin username already exists. Please choose a different username.</div>';
                } else {
                    // Insert the admin user into the admin_users table
                    $sql = "INSERT INTO admin_users (username, password) VALUES ('$adminUsername', '$adminPassword')";

                    if ($conn->query($sql) === TRUE) {
                        echo '<div class="alert alert-success">Admin user created successfully</div>';
                        echo '<div class="alert alert-success">Tables created successfully</div>';
                         // Update config.php with the actual database configuration
            $config = <<<EOL
            
            <?php
            \$servername = "$servername";
            \$dbUsername = "$dbUsername";
            \$dbPassword = "$dbPassword";
            \$dbName = "$dbname";

            // Create a connection to the MySQL server
            \$conn = new mysqli(\$servername, \$dbUsername, \$dbPassword);

            // Check connection
            if (\$conn->connect_error) {
                die("Connection failed: " . \$conn->connect_error);
            }

            // Check if the necessary database exists
            \$sql = "SHOW DATABASES LIKE '\$dbName'";
            \$result = \$conn->query(\$sql);

            if (\$result->num_rows === 0) {
                // Redirect to the first admin registration page if the database doesn't exist
                header("Location: admin_first_time_registration.php");
                exit();
            }


            // Create a connection to the MySQL server
            \$conn = new mysqli(\$servername, \$dbUsername, \$dbPassword, \$dbName);

            // Check if the necessary tables exist in the database
            \$tableCheckQuery = "SHOW TABLES LIKE 'admin_users'";
            \$tableCheckResult = \$conn->query(\$tableCheckQuery);

            if (\$tableCheckResult->num_rows === 0) {
                // Redirect to the first admin registration page
                header("Location: admin_first_time_registration.php");
                exit();
            }

            // Check connection
            if (\$conn->connect_error) {
                die("Connection failed: " . \$conn->connect_error);
            }
            
            // Create a PDO connection
            try {
                \$pdo = new PDO("mysql:host=\$servername; dbname=\$dbName", \$dbUsername, \$dbPassword);
                // Set the PDO error mode to exception
                \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException \$e) {
                echo "Connection failed: " . \$e->getMessage();
                exit(); // Terminate the script if connection fails
            }

            ?>
            EOL;
                        file_put_contents("../config.php", $config);

            
                        echo '<div class="alert alert-success">Database configuration updated</div>';
                    } else {
                        echo '<div class="alert alert-danger">Error creating admin user: ' . $conn->error . '</div>';
                    }
                }
            } else {
                echo '<div class="alert alert-danger">Error creating tables: ' . $conn->error . '</div>';
            }
        } else {
            echo '<div class="alert alert-danger">Error creating database: ' . $conn->error . '</div>';
        }

        // Close the MySQL connection
        $conn->close();
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
   <!-- Include Bootstrap CSS -->
<link href="../css_and_javascript/bootstrap-5.3.2-dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
<div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h2>Admin First Time Registration</h2>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="form-group">
                                <label for="db_username">Database Username:</label>
                                <input type="text" class="form-control" name="db_username" required>
                            </div>
                            <div class="form-group">
                                <label for="db_password">Database Password:</label>
                                <input type="password" class="form-control" name="db_password">
                            </div>
                            <div class="form-group">
                                <label for="admin_username">Admin Username:</label>
                                <input type="text" class="form-control" name="admin_username" required>
                            </div>
                            <div class="form-group">
                                <label for="admin_password">Admin Password:</label>
                                <input type="password" class="form-control" name="admin_password" required>
                            </div>
                            <div class="form-group">
                                <label for="dbname">Database Name:</label>
                                <input type="text" class="form-control" name="dbname" required>
                            </div>
                            <button type="submit" class="btn btn-primary my-2">Register and Create Database</button>
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
