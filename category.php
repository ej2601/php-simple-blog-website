<?php
require_once("header.php");
require_once("config.php");

// Retrieve the category from the URL parameter
if(isset($_GET['category'])) {
    $category = $_GET['category'];
} else {
    // Redirect to error page or homepage if no category is provided
    header("Location: index.php");
    exit();
}

try {
    // Fetch posts by category from the database
    $getPostsByCategoryQuery = "SELECT * FROM Posts WHERE status = 'publish' AND categories LIKE ?";
    $stmt = $pdo->prepare($getPostsByCategoryQuery);
    $stmt->execute(["%$category%"]);
    $posts = $stmt->fetchAll();
} catch (PDOException $e) {
    // Handle database error
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!-- Post List by Category -->
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h2 class="mb-4">Posts in <?php echo $category; ?> Category</h2>
            <?php foreach ($posts as $post) : ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <!-- Post Title with link to post.php -->
                        <h3 class="card-title"><a href="post.php?id=<?php echo $post['post_id']; ?>" style="text-decoration:none"><?php echo $post['title']; ?></a></h3>
                        <!-- Post Categories -->
                        <p class="card-text"><strong>Categories:</strong> <?php echo $post['categories']; ?></p>
                        <!-- Post Description -->
                        <p class="card-text"><?php echo $post['description']; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($posts)) : ?>
                <div class="alert alert-info" role="alert">
                    No posts found in this category.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
require_once("footer.php");
?>
