<?php

// Include database connection file or set up your database connection
require_once("../config.php");

date_default_timezone_set("Asia/Kolkata");

try {
    // Check if the required tables exist
    $checkTablesQuery = "SHOW TABLES LIKE 'Posts'";
    $result = $pdo->query($checkTablesQuery);
    
    if ($result->rowCount() == 0) {
        // If 'Posts' table doesn't exist, create it
        $createPostsTableQuery = "
        CREATE TABLE Posts (
            post_id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255),
            description TEXT,
            status VARCHAR(20),
            categories TEXT,
            modified_at TEXT
            )
            ";
            $pdo->exec($createPostsTableQuery);
        }
        
        // Check if 'Sections' table exists
        $checkSectionsTableQuery = "SHOW TABLES LIKE 'Sections'";
            $result = $pdo->query($checkSectionsTableQuery);
            
            if ($result->rowCount() == 0) {
                // If 'Sections' table doesn't exist, create it
                $createSectionsTableQuery = "
                CREATE TABLE Sections (
                    section_id INT AUTO_INCREMENT PRIMARY KEY,
                    post_id INT,
                    section_type VARCHAR(20),
                    section_content TEXT,
                    FOREIGN KEY (post_id) REFERENCES Posts(post_id)
                    )
                    ";
                    $pdo->exec($createSectionsTableQuery);
                }
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    // Retrieve form data
    $postTitle = $_POST['postTitle'] ?? '';
    $metaDescription = $_POST['metaDescription'] ?? '';
    $categories = $_POST['categories'] ?? []; // Array of categories
    $postStatus = $_POST['postStatus'] ?? '';
    $sectionsType = $_POST['sectionType'] ?? []; // Array of section types
    $sectionsContent = $_POST['sectionContent'] ?? []; // Array of section content
     
    // Validate and process the received data
    // Insert into database and handle section types and content here
    // Store post details in the Posts table
    $insertPostQuery = "INSERT INTO Posts (title, description, status, categories, modified_at) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($insertPostQuery);
    $stmt->execute([$postTitle, $metaDescription, $postStatus, implode(", ", $categories), date("Y-m-d h:i:sa")]);
    
    // Get the last inserted post_id
    $postId = $pdo->lastInsertId();
    
    $section_counter_1 = -1;
    $section_counter_2 = -1;

    for ($i = 0; $i < count($sectionsType); $i++) {
        $sectionType = $sectionsType[$i];
          
        if ($sectionType === 'image' && isset($_FILES['sectionImage']['name'])) {
            $section_counter_1 += 1;
            $file = $_FILES['sectionImage'];
            $fileName = $file['name'][$section_counter_1];
            $fileTmpName = $file['tmp_name'][$section_counter_1];
            $fileError = $file['error'][$section_counter_1];
            
            if ($fileError === UPLOAD_ERR_OK) {
                // Define your upload directory
                $uploadDir = '../images/';
                $destination = $uploadDir .uniqid(). $fileName;
                
                // Move the uploaded file to the designated directory
                move_uploaded_file($fileTmpName, $destination);
                
                // Insert section details into Sections table
                $insertSectionQuery = "INSERT INTO Sections (post_id, section_type, section_content) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($insertSectionQuery);
                $stmt->execute([$postId, $sectionType, $destination]);
                
            }
        }
        else{

            $section_counter_2 += 1;
            $sectionContent = $sectionsContent[$section_counter_2];
            
            // Insert section details into Sections table
            $insertSectionQuery = "INSERT INTO Sections (post_id, section_type, section_content) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($insertSectionQuery);
            $stmt->execute([$postId, $sectionType, $sectionContent]);
            
        }
       
    }

    // Redirect to make_posts_page.php
    header("Location: make_posts_page.php");
    exit();
}
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>