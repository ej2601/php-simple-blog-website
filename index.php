<?php
// Include header and footer
require_once("header.php");
require_once("config.php");

// Fetch latest published posts from the database
$sql = "SELECT * FROM Posts WHERE status='publish' ORDER BY modified_at DESC LIMIT 10"; // Change limit as needed
$stmt = $pdo->query($sql);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Content -->
<div class="container mt-4">
    <h2>Latest Posts</h2>
    <div class="row">
        <?php foreach ($posts as $post) : ?>
            <div class="col-md-4">
                <div class="card mb-4">
                    <?php 
                        // Fetch the first image of the post if it exists
                        $image = ''; // Set a default image path
                        $sectionQuery = "SELECT section_content FROM Sections WHERE post_id=? AND section_type='image' LIMIT 1";
                        $sectionStmt = $pdo->prepare($sectionQuery);
                        $sectionStmt->execute([$post['post_id']]);
                        $section = $sectionStmt->fetch(PDO::FETCH_ASSOC);
                        if ($section) {
                            // Remove dots from the image path
                            $image = str_replace('../', '', $section['section_content']);
                        }
                    ?>
                    <img src="<?php echo $image; ?>" class="card-img-top" alt="Post Image">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $post['title']; ?></h5>
                        <p class="card-text"><?php echo $post['description']; ?></p>
                        <p class="card-text">Category: <?php echo $post['categories']; ?></p>
                        <p class="card-text">Date: <?php echo $post['modified_at']; ?></p>
                        <!-- Read More button -->
                        <a href="post.php?id=<?php echo $post['post_id']; ?>" class="btn btn-primary">Read More</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php
// Include footer
require_once("footer.php");
?>
