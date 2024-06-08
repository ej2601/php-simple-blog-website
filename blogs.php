<?php
require_once ("header.php");
require_once ("config.php");

// Fetch all categories from the database for published posts
try {
    $getCategoriesQuery = "SELECT DISTINCT TRIM(categories) AS category FROM Posts WHERE status = 'publish'";
    $stmt = $pdo->query($getCategoriesQuery);
    $categories = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Split categories by comma and add them to the categories array
        $categories = array_merge($categories, explode(',', $row['category']));
    }
    // Remove duplicates and trim whitespace
    $categories = array_map('trim', array_unique($categories));
    $categories = array_unique($categories);

} catch (PDOException $e) {
    // Handle database error
    echo "Error: " . $e->getMessage();
    exit();
}

// Determine current page for pagination
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = 9; // Number of posts per page

// Calculate offset for pagination
$offset = ($page - 1) * $limit;

// Fetch total number of posts from the database
try {
    $getTotalPostsQuery = "SELECT COUNT(*) AS totalPosts FROM Posts WHERE status = 'publish'";
    $stmt = $pdo->prepare($getTotalPostsQuery);
    $stmt->execute();
    $totalPosts = $stmt->fetchColumn();
} catch (PDOException $e) {
    // Handle database error
    echo "Error: " . $e->getMessage();
    exit();
}

// Calculate total number of pages
$totalPages = ceil($totalPosts / $limit);

// Determine current page for pagination
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$page = max(1, min($totalPages, intval($page))); // Ensure page number is within valid range

// Calculate offset for pagination
$offset = ($page - 1) * $limit;

// Fetch blog posts from the database
try {
    $getPostsQuery = "SELECT * FROM Posts WHERE status = 'publish' LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($getPostsQuery);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $posts = $stmt->fetchAll();
} catch (PDOException $e) {
    // Handle database error
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!-- Display posts -->
<?php if (empty($posts)): ?>
    <p>No posts available.</p>
<?php else: ?>
<div class="container mt-4">
    <!-- Tabs for filtering blog posts by categories -->
    <ul class="nav nav-tabs" id="categoriesTabs">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#all">All</a>
        </li>
        <?php foreach ($categories as $category): ?>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab"
                    href="#<?php echo strtolower(str_replace(' ', '-', $category)); ?>">
                    <?php echo $category; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <!-- Tab content -->
    <div class="tab-content py-4" id="categoriesTabContent">
        <!-- Tab pane for all posts -->
        <div class="tab-pane fade show active" id="all">
            <?php foreach ($posts as $post): ?>
                <!-- Display blog post card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title"><a href="post.php?id=<?php echo $post['post_id']; ?>">
                                <?php echo $post['title']; ?>
                            </a></h5>
                        <p class="card-text">
                            <?php echo $post['description']; ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Tab panes for individual categories -->
        <?php foreach ($categories as $category): ?>
            <div class="tab-pane fade" id="<?php echo strtolower(str_replace(' ', '-', $category)); ?>">
                <?php
                // Fetch blog posts by category from the database
                try {
                    $getPostsByCategoryQuery = "SELECT * FROM Posts WHERE status = 'publish' AND categories LIKE ?";
                    $stmt = $pdo->prepare($getPostsByCategoryQuery);
                    $stmt->execute(["%$category%"]);
                    $categoryPosts = $stmt->fetchAll();
                } catch (PDOException $e) {
                    // Handle database error
                    echo "Error: " . $e->getMessage();
                    exit();
                }

                // Display blog posts in the category
                foreach ($categoryPosts as $post):
                    ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title"><a href="post.php?post_id=<?php echo $post['post_id']; ?>">
                                    <?php echo $post['title']; ?>
                                </a></h5>
                            <p class="card-text">
                                <?php echo $post['description']; ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link"
                    href="<?php echo ($page <= 1) ? '#' : 'blogs.php?page=' . ($page - 1); ?>">Previous</a>
            </li>
            <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                <a class="page-link"
                    href="<?php echo ($page >= $totalPages) ? '#' : 'blogs.php?page=' . ($page + 1); ?>">Next</a>
            </li>
        </ul>
    </nav>

</div>
<?php endif; ?>
<?php
require_once ("footer.php");
?>