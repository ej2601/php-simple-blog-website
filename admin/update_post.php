<?php
// Include database connection file or set up your database connection
require_once ("../config.php");

date_default_timezone_set("Asia/Kolkata");

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve form data
        $postId = $_POST['post_id'] ?? ''; // Add this line to retrieve the post ID
        $postTitle = $_POST['postTitle'] ?? '';
        $metaDescription = $_POST['metaDescription'] ?? '';
        $categories = $_POST['categories'] ?? []; // Array of categories
        $postStatus = $_POST['postStatus'] ?? '';
        $sectionsType = $_POST['sectionType'] ?? []; // Array of section types
        $sectionsContent = $_POST['sectionContent'] ?? []; // Array of section content
        $currentImages = $_POST['currentImage'] ?? []; // Array of current image section content

        // echo var_dump($_POST);
        // echo var_dump($_FILES);
        // Update the post details in the Posts table
        $updatePostQuery = "UPDATE Posts SET title=?, description=?, status=?, categories=?, modified_at=? WHERE post_id=?";
        $stmt = $pdo->prepare($updatePostQuery);
        $stmt->execute([$postTitle, $metaDescription, $postStatus, implode(", ", $categories), date("Y-m-d h:i:sa"), $postId]);

        // // Delete existing sections of the edited post from the Sections table
        $deleteSectionsQuery = "DELETE FROM Sections WHERE post_id=?";
        $stmt = $pdo->prepare($deleteSectionsQuery);
        $stmt->execute([$postId]);

        $section_counter_1 = -1;
        $section_counter_2 = -1;
        $section_counter_3 = -1;
        // echo "hello";
        // echo var_dump($currentImages);
        // echo "second";

        // echo var_dump($_FILES['sectionImage']);
        // Insert updated sections into the Sections table
        for ($i = 0; $i < count($sectionsType); $i++) {
            $sectionType = $sectionsType[$i];

            if ($sectionType === 'image' && isset($_FILES['sectionImage']['name'])) {
                // echo "image hi";
                $section_counter_1 += 1;
                $file = $_FILES['sectionImage'];
                $fileName = $file['name'][$section_counter_1];
                $fileTmpName = $file['tmp_name'][$section_counter_1];
                $fileError = $file['error'][$section_counter_1];
                
                if ($fileError === UPLOAD_ERR_OK) {
                    // Define your upload directory
                    $uploadDir = '../images/';
                    $destination = $uploadDir . uniqid() . $fileName;

                    // Move the uploaded file to the designated directory
                    move_uploaded_file($fileTmpName, $destination);

                    // Insert section details into Sections table
                    $insertSectionQuery = "INSERT INTO Sections (post_id, section_type, section_content) VALUES (?, ?, ?)";
                    $stmt = $pdo->prepare($insertSectionQuery);
                    $stmt->execute([$postId, $sectionType, $destination]);
                }
                else{
                
                    $section_counter_3 += 1;
                    echo var_dump($currentImages);
                    if (empty($currentImages[$section_counter_1]) || $currentImages[$section_counter_1] === "no image"){
                        $sectionContent = "no image";
                    }

                    else{
                        // Insert current image section content into Sections table
                        $sectionContent = $currentImages[$section_counter_1];
                    }

                    $insertSectionQuery = "INSERT INTO Sections (post_id, section_type, section_content) VALUES (?, ?, ?)";
                    $stmt = $pdo->prepare($insertSectionQuery);
                    $stmt->execute([$postId, $sectionType, $sectionContent]);
                }
            }  else {
                $section_counter_2 += 1;
                $sectionContent = $sectionsContent[$section_counter_2];

                // Insert section details into Sections table
                $insertSectionQuery = "INSERT INTO Sections (post_id, section_type, section_content) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($insertSectionQuery);
                $stmt->execute([$postId, $sectionType, $sectionContent]);
            }
        }

        // Redirect to all_posts.php or any other page as needed
        header("Location: all_posts.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

