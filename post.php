<?php
require_once ("header.php");
require_once ("config.php");

// Retrieve the post ID from the URL parameter
if (isset($_GET['id'])) {
    $postId = $_GET['id'];
} else {
    // Redirect to error page or homepage if no post ID is provided
    header("Location: index.php");
    exit();
}

try {
    // Fetch post details from the database
    $getPostQuery = "SELECT * FROM Posts WHERE post_id = ?";
    $stmt = $pdo->prepare($getPostQuery);
    $stmt->execute([$postId]);
    $post = $stmt->fetch();

    // Fetch sections of the post from the database
    $getSectionsQuery = "SELECT * FROM Sections WHERE post_id = ?";
    $stmt = $pdo->prepare($getSectionsQuery);
    $stmt->execute([$postId]);
    $sections = $stmt->fetchAll();
} catch (PDOException $e) {
    // Handle database error
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!-- Post Content -->
<div class="container mt-4">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <!-- Post Title -->
            <h2 class="mb-4 display-1">
                <?php echo $post['title']; ?>
            </h2>
            <!-- Post Categories -->
            <?php
            $categories = explode(", ", $post['categories']);
            foreach ($categories as $category): ?>
                <a href="category.php?category=<?php echo urlencode($category); ?>" class="btn btn-warning btn-sm">
                    <?php echo $category; ?>
                </a>
            <?php endforeach; ?>

            <!-- Post Sections -->
            <?php foreach ($sections as $section): ?>
                <?php if ($section['section_type'] === 'image'): ?>
                    <!-- Image Section -->
                    <img src="<?php echo str_replace('../', '', $section['section_content']); ?>" class="img-fluid mb-3"
                        alt="Post Image">
                <?php elseif ($section['section_type'] === 'video'): ?>
                    <!-- Video Section -->
                    <div class="embed-responsive embed-responsive-16by9 mb-3">
                        <iframe class="embed-responsive-item" src="<?php echo $section['section_content']; ?>"
                            allowfullscreen></iframe>
                    </div>
                <?php elseif ($section['section_type'] === 'heading'): ?>
                    <!-- Video Section -->
                    <div class="my-4">
                        <h2>
                            <?php echo $section['section_content']; ?>
                        </h2>
                    </div>
                <?php else: ?>
                    <!-- Text Section -->
                    <div class="mb-4">
                        <p>
                            <?php echo $section['section_content']; ?>
                        </p>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php
require_once ("footer.php");
?>