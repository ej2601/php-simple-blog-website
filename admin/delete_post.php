<?php
require_once("../config.php");

echo var_dump($_GET);
// Check if post ID is provided
if (isset($_GET['id'])) {
    $postId = $_GET['id'];
    
    // Delete associated sections from Sections table
    $deleteSectionsQuery = "DELETE FROM Sections WHERE post_id = ?";
    $deleteSectionsStmt = $pdo->prepare($deleteSectionsQuery);
    $deleteSectionsStmt->execute([$postId]);

    // Delete post from Posts table
    $deletePostQuery = "DELETE FROM Posts WHERE post_id = ?";
    $deletePostStmt = $pdo->prepare($deletePostQuery);
    $deletePostStmt->execute([$postId]);


    // Redirect back to all_posts.php
    header("Location: all_posts.php");
    exit();
} else {
    // If postId is not provided, redirect back to the all_posts.php page
    header("Location: all_posts.php");
    exit();
}
?>
