<?php
require_once("admin_navbar.php");
require_once("../config.php");

$sql = "SHOW TABLES LIKE 'posts'";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    // Redirect to the first admin registration page if the "posts" table doesn't exist
    header("Location: make_posts_page.php");
    exit();
}

// Fetch all published posts
$publishedPostsQuery = "SELECT * FROM Posts WHERE status = 'publish'";
$publishedPostsStmt = $pdo->query($publishedPostsQuery);
$publishedPosts = $publishedPostsStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all draft posts
$draftPostsQuery = "SELECT * FROM Posts WHERE status = 'draft'";
$draftPostsStmt = $pdo->query($draftPostsQuery);
$draftPosts = $draftPostsStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle search functionality
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$searchCondition = $searchQuery ? "AND title LIKE '%$searchQuery%'" : '';

$publishedPostsQuery .= " $searchCondition";
$draftPostsQuery .= " $searchCondition";
$publishedPostsStmt = $pdo->query($publishedPostsQuery);
$publishedPosts = $publishedPostsStmt->fetchAll(PDO::FETCH_ASSOC);
$draftPostsStmt = $pdo->query($draftPostsQuery);
$draftPosts = $draftPostsStmt->fetchAll(PDO::FETCH_ASSOC);

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Posts</title>
    <!-- Include Bootstrap CSS -->
    <link href="../css_and_javascript/bootstrap-5.3.2-dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include any additional CSS or JS files if needed -->
</head>
<body>
    <div class="container mt-4">
        <h2>All Posts</h2>

        <!-- Search bar -->
        <form class="mb-3" action="" method="GET">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Search posts..." name="search" value="<?php echo htmlspecialchars($searchQuery); ?>">
                <button class="btn btn-outline-primary" type="submit">Search</button>
            </div>
        </form>

        <!-- Tabbed navigation for published and draft posts -->
        <ul class="nav nav-tabs" id="postTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="published-tab" data-bs-toggle="tab" data-bs-target="#published" type="button" role="tab" aria-controls="published" aria-selected="true">Published</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="draft-tab" data-bs-toggle="tab" data-bs-target="#draft" type="button" role="tab" aria-controls="draft" aria-selected="false">Draft</button>
            </li>
        </ul>

        <!-- Tab panes for published and draft posts -->
        <div class="tab-content" id="postTabsContent">
            <!-- Published Posts -->
            <div class="tab-pane fade show active" id="published" role="tabpanel" aria-labelledby="published-tab">
                <?php if (count($publishedPosts) > 0): ?>
                    <div class="row mt-3">
                        <?php foreach ($publishedPosts as $post): ?>
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo $post['title']; ?></h5>
                                        <p class="card-text">Status: <?php echo $post['status']; ?></p>
                                        <p class="card-text">Categories: <?php echo $post['categories']; ?></p>
                                        <a href="edit_post.php?id=<?php echo $post['post_id']; ?>" class="btn btn-primary">Edit</a>
                                        <a href="delete_post.php?id=<?php echo $post['post_id']; ?>" class="btn btn-danger">Delete</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>No published posts found.</p>
                <?php endif; ?>
            </div>

            <!-- Draft Posts -->
            <div class="tab-pane fade" id="draft" role="tabpanel" aria-labelledby="draft-tab">
                <?php if (count($draftPosts) > 0): ?>
                    <div class="row mt-3">
                        <?php foreach ($draftPosts as $post): ?>
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo $post['title']; ?></h5>
                                        <p class="card-text">Status: <?php echo $post['status']; ?></p>
                                        <p class="card-text">Categories: <?php echo $post['categories']; ?></p>
                                        <a href="edit_post.php?id=<?php echo $post['post_id']; ?>" class="btn btn-primary">Edit</a>
                                        <a href="delete_post.php?id=<?php echo $post['post_id']; ?>" class="btn btn-danger">Delete</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>No draft posts found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS and any additional JS files if needed -->
    <script src="../css_and_javascript/bootstrap-5.3.2-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
