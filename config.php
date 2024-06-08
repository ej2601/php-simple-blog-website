
<?php
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "ej_simpleblog";

// Create a connection to the MySQL server
$conn = new mysqli($servername, $dbUsername, $dbPassword);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the necessary database exists
$sql = "SHOW DATABASES LIKE '$dbName'";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    // Redirect to the first admin registration page if the database doesn't exist
    header("Location: admin_first_time_registration.php");
    exit();
}


// Create a connection to the MySQL server
$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbName);

// Check if the necessary tables exist in the database
$tableCheckQuery = "SHOW TABLES LIKE 'admin_users'";
$tableCheckResult = $conn->query($tableCheckQuery);

if ($tableCheckResult->num_rows === 0) {
    // Redirect to the first admin registration page
    header("Location: admin_first_time_registration.php");
    exit();
}

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create a PDO connection
try {
    $pdo = new PDO("mysql:host=$servername; dbname=$dbName", $dbUsername, $dbPassword);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit(); // Terminate the script if connection fails
}

?>