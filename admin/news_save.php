<?php
include('includes/db_connection.php');

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $article_id = isset($_POST['article_id']) ? intval($_POST['article_id']) : 0;
    $title = sanitize($_POST['title']);
    $slug = sanitize($_POST['slug']);
    $excerpt = sanitize($_POST['excerpt']);
    $content = $_POST['content']; // Don't sanitize to allow HTML
    $publish_date = sanitize($_POST['publish_date']);
    $status = sanitize($_POST['status']);
    
    // Handle image upload
    $image_name = '';
    if (isset($_FILES['image']) && $_FILES['image']['name']) {
        $target_dir = "../images/news/";
        
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        // Generate unique filename
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image_name = $slug . '-' . time() . '.' . $file_extension;
        $target_file = $target_dir . $image_name;
        
        // Move uploaded file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            // File uploaded successfully
        } else {
            // Error uploading file
            $image_name = '';
        }
    }
    
    if ($article_id > 0) {
        // Update existing article
        $sql = "UPDATE news_articles SET 
                title = '$title', 
                slug = '$slug', 
                excerpt = '$excerpt', 
                content = '" . mysqli_real_escape_string($conn, $content) . "', 
                publish_date = '$publish_date', 
                status = '$status'";
        
        // Add image to update only if a new one was uploaded
        if ($image_name) {
            $sql .= ", image = '$image_name'";
        }
        
        $sql .= " WHERE id = $article_id";
        
        if (executeQuery($sql)) {
            header('Location: news.php?success=updated');
            exit;
        } else {
            header('Location: news.php?error=update_failed');
            exit;
        }
    } else {
        // Insert new article
        $sql = "INSERT INTO news_articles (title, slug, excerpt, content, image, publish_date, status) 
                VALUES ('$title', '$slug', '$excerpt', '" . mysqli_real_escape_string($conn, $content) . "', '$image_name', '$publish_date', '$status')";
        
        if (executeQuery($sql)) {
            header('Location: news.php?success=created');
            exit;
        } else {
            header('Location: news.php?error=create_failed');
            exit;
        }
    }
} else {
    // If not POST request, redirect to news page
    header('Location: news.php');
    exit;
}
?> 