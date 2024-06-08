<?php
// Include database connection
require_once("../config.php");

// Fetch post details from database based on post ID
if (isset($_GET['id'])) {
    $postId = $_GET['id'];

    // Fetch post details from Posts table
    $getPostQuery = "SELECT * FROM Posts WHERE post_id = ?";
    $getPostStmt = $pdo->prepare($getPostQuery);
    $getPostStmt->execute([$postId]);
    $post = $getPostStmt->fetch(PDO::FETCH_ASSOC);

    // Extract categories from the post data
    $categories = explode(',', $post['categories']);

    // Fetch sections associated with the post
    $getSectionsQuery = "SELECT * FROM Sections WHERE post_id = ?";
    $getSectionsStmt = $pdo->prepare($getSectionsQuery);
    $getSectionsStmt->execute([$postId]);
    $sections = $getSectionsStmt->fetchAll(PDO::FETCH_ASSOC);

} else {
    // If post ID is not provided, redirect to make_post_page.php
    header("Location: make_post_page.php");
    exit();
}
?>

<?php
// Include admin navbar
require_once("admin_navbar.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
    <!-- Include Bootstrap CSS -->
    <link href="../css_and_javascript/bootstrap-5.3.2-dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include any additional CSS or JS files if needed -->
</head>

<body>
    <div class="container mt-4">
        <h2>Edit Post</h2>

        <!-- Form for editing post -->
        <form id="postForm" action="update_post.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
            <div class="mb-3">
                <label for="postTitle" class="form-label">Post Title</label>
                <input type="text" class="form-control" id="postTitle" name="postTitle" placeholder="Enter post title" value="<?php echo $post['title']; ?>">
            </div>
            <div class="mb-3">
                <label for="metaDescription" class="form-label">Meta Description</label>
                <textarea class="form-control" id="metaDescription" name="metaDescription" rows="3" placeholder="Enter meta description"><?php echo $post['description']; ?></textarea>
            </div>

       <!-- Display existing sections -->
            <div id="existingSections">
                <?php foreach ($sections as $section) : ?>
                    <div class="mb-3">
                        <!-- Create a disabled dropdown with selected section type -->
                        <select class="form-select" name = "sectionType[]">
                            <option value="<?php echo $section['section_type']; ?>" selected><?php echo ucfirst($section['section_type']); ?></option>
                        </select>
                        
                        <!-- Create a content box for the section -->
                        <div class="mb-3">
                            <?php if ($section['section_type'] === 'image') : ?>

                                <input type="hidden" name="currentImage[]" value="<?php echo $section['section_content']; ?>">
                                <img src="../images/<?php echo $section['section_content']; ?>" class="img-fluid p-2" alt="Current Image" style="width : 50%;">
                                <br>
                                <label class="form-label mt-3">New Image Upload</label>
                                <input type="file" class="form-control" name="sectionImage[]" accept="image/*">
                            <?php elseif ($section['section_type'] === 'video') : ?>
                                <label class="form-label">Video URL</label>
                                <input type="text" class="form-control" name="sectionVideo" placeholder="Enter YouTube video URL">
                            <?php else : ?>
                                <label class="form-label">Content for <?php echo ucfirst($section['section_type']); ?></label>
                                <textarea class="form-control" rows="3" placeholder="Enter section content" name="sectionContent[]"><?php echo $section['section_content']; ?></textarea>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Create a remove section button -->
                        <button type="button" class="btn btn-danger" onclick="removeSection(this)">Remove Section</button>
                    </div>
                <?php endforeach; ?>
            </div>

        <!-- Dynamic Sections -->
        <div id="sections">
                <!-- Sections will be dynamically added here -->
            </div>
            <hr>
            
            <!-- Dropdown to select section type -->
            <div class="mb-3">
                <label for="sectionType" class="form-label">Select Section Type</label>
                <select class="form-select" id="sectionType">
                    <option value="heading">Heading</option>
                    <option value="paragraph">Paragraph</option>
                    <option value="image">Image</option>
                    <option value="video">Video</option>
                </select>
            </div>
            
            <!-- Button to add selected section -->
            <button type="button" class="btn btn-primary mt-3" onclick="addSection()">Add Section</button>
            
            <!-- Categories Section -->
            <hr>
            <div class="mb-3">
                <label for="categories" class="form-label">Categories</label>
                <div id="categoriesList">
                    <!-- Display existing categories -->
                    <div id="existingCategories">
                        <?php foreach ($categories as $category) : ?>
                            <div class="badge bg-warning me-2 mb-2">
                                <?php echo $category; ?>
                                <button type="button" class="btn-close" onclick="removeCategory(this)"></button>
                                <input type="hidden" name="categories[]" value="<?php echo $category; ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Categories will be dynamically added here -->
                </div>
                <div class="input-group mt-2">
                    <input type="text" class="form-control" id="categoryInput" placeholder="Enter category">
                    <button class="btn btn-primary" type="button" onclick="addCategory()">Add Category</button>
                </div>
            </div>
            
            <!-- Post Status -->
            <hr>
            <div class="mb-3">
                <label for="postStatus" class="form-label">Post Status</label>
                <select class="form-select" name="postStatus">
                    <option value="draft" <?php if ($post['status'] === 'draft') echo 'selected'; ?>>Save as Draft</option>
                    <option value="publish" <?php if ($post['status'] === 'publish') echo 'selected'; ?>>Publish</option>
                </select>
                <div id="scheduleDateTime" style="display: none;">
                    <input type="datetime-local" class="form-control mt-2" name="scheduleDateTime">
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Save Post</button>
        </form>
    </div>

    <!-- Include Bootstrap JS and any additional JS files if needed -->
    <script src="../css_and_javascript/bootstrap-5.3.2-dist/js/bootstrap.min.js"></script>
    <script>
        // JavaScript code for adding sections and categories (same as make_post.php)
        // Add any additional scripts if needed
        function addSection() {
    // Get the selected section type from the dropdown menu
    const sectionType = document.getElementById('sectionType').value;

    // Get the container where new sections will be added
    const sectionsContainer = document.getElementById('sections');

    // Create a new section container
    const newSection = document.createElement('div');
    newSection.className = 'mb-3';

    // Create a disabled dropdown with selected section type
    const disabledDropdown = document.createElement('select');
    disabledDropdown.className = 'form-select';
    // disabledDropdown.setAttribute('disabled', true);
    disabledDropdown.setAttribute('name', "sectionType[]");

    // Create an option element for the selected section type
    const selectedOption = document.createElement('option');
    selectedOption.value = sectionType;
    selectedOption.text = sectionType.charAt(0).toUpperCase() + sectionType.slice(1);
    selectedOption.selected = true;

    // Append the selected option to the dropdown menu
    disabledDropdown.appendChild(selectedOption);

    // Create a content box for the section
    const contentBox = document.createElement('div');
    contentBox.className = 'mb-3';

    // Determine the type of content box based on the selected section type
    if (sectionType === 'image') {
        // Create a file input field for images
        contentBox.innerHTML = `<label class="form-label">Image Upload</label>
                                <input type="file" class="form-control" name="sectionImage[]" accept="image/*">`;
    } else if (sectionType === 'video') {
        // Create an input field for video URL
        contentBox.innerHTML = `<label class="form-label">Video URL</label>
                                <input type="text" class="form-control" name="sectionVideo" placeholder="Enter YouTube video URL">`;
    } else {
        // Create a textarea for other content types (e.g., text)
        contentBox.innerHTML = `<label class="form-label">Content for ${sectionType}</label>
                                <textarea class="form-control" rows="3" placeholder="Enter section content" name="sectionContent[]"></textarea>`;
    }

    // Create a remove section button
    const removeButton = document.createElement('button');
    removeButton.type = 'button';
    removeButton.className = 'btn btn-danger';
    removeButton.innerHTML = 'Remove Section';

    // Define an onclick event handler to remove the section when the button is clicked
    removeButton.onclick = function() {
        sectionsContainer.removeChild(newSection);
    };

    // Append the disabled dropdown, content box, and remove button to the new section container
    newSection.appendChild(disabledDropdown);
    newSection.appendChild(contentBox);
    newSection.appendChild(removeButton);

    // Append the new section container to the sections container
    sectionsContainer.appendChild(newSection);
}

function removeSection(buttonElement) {
        const sectionContainer = buttonElement.parentElement;
        sectionContainer.remove();
    }

// Function to add a category
function addCategory() {
            const categoryInput = document.getElementById('categoryInput');
            const category = categoryInput.value.trim();
            if (category !== '') {
                const categoriesList = document.getElementById('categoriesList');
                const categoryItem = document.createElement('div');
                categoryItem.className = 'badge bg-warning me-2 mb-2';
                categoryItem.innerHTML = `
                    ${category}
                    <button type="button" class="btn-close" onclick="removeCategory(this)"></button>
                    <input type="hidden" name="categories[]" value="${category}">
                `;
                categoriesList.appendChild(categoryItem);
                categoryInput.value = '';
            }
        }

        // Function to remove a category
        function removeCategory(buttonElement) {
            const categoryItem = buttonElement.parentElement;
            categoryItem.remove();
        }

    </script>
</body>

</html>
