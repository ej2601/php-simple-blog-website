<?php
// Start session if not already started
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to the login page or any other desired page
header("Location: admin_login.php");
exit();
