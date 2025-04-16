<?php
include('includes/db_connection.php');

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $category_id = intval($_POST['category_id']);
    $title = sanitize($_POST['title']);
    $slug = sanitize($_POST['slug']);
    $excerpt = isset($_POST['excerpt']) ? sanitize($_POST['excerpt']) : '';
    $content = $_POST['content']; // Don't sanitize to allow HTML
    $author = sanitize($_POST['author']);
    $status = sanitize($_POST['status']);
    $publish_date = sanitize($_POST['publish_date']);
    
    // Format publish date for MySQL
    $publish_date = date('Y-m-d H:i:s', strtotime($publish_date));
    
    // Check if slug exists (except for the current post when updating)
    $slug_check_sql = "SELECT id FROM blog_posts WHERE slug = '$slug'" . 
                     ($post_id > 0 ? " AND id != $post_id" : "");
    $slug_check = executeQuery($slug_check_sql);
    
    if ($slug_check && $slug_check->num_rows > 0) {
        // Slug already exists
        header('Location: blog_post_edit.php' . ($post_id > 0 ? "?id=$post_id" : "") . '&error=slug_exists');
        exit;
    }
    
    // If excerpt is empty, generate from content
    if (empty($excerpt)) {
        $excerpt = substr(strip_tags($content), 0, 150) . '...';
    }
    
    // Handle image upload
    $featured_image = '';
    $remove_image = isset($_POST['remove_image']) && $_POST['remove_image'] == '1';
    
    // If editing an existing post, get the current image
    if ($post_id > 0) {
        $sql = "SELECT featured_image FROM blog_posts WHERE id = $post_id";
        $result = executeQuery($sql);
        if ($result && $result->num_rows > 0) {
            $post = $result->fetch_assoc();
            $featured_image = $post['featured_image'];
        }
    }
    
    // Handle image removal
    if ($remove_image && !empty($featured_image)) {
        // Delete the file if it exists
        $file_path = "../$featured_image";
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        $featured_image = ''; // Clear the image path
    }
    
    // Handle new image upload
    if (!empty($_FILES['featured_image']['name'])) {
        // Delete the old image if it exists
        if (!empty($featured_image) && file_exists("../$featured_image")) {
            unlink("../$featured_image");
        }
        
        // Process the new image
        $upload_dir = 'images/blog/';
        $timestamp = time();
        $file_name = $timestamp . '_' . sanitize($_FILES['featured_image']['name']);
        $upload_path = "../$upload_dir" . $file_name;
        
        // Create directory if it doesn't exist
        if (!file_exists("../$upload_dir")) {
            mkdir("../$upload_dir", 0777, true);
        }
        
        if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $upload_path)) {
            $featured_image = $upload_dir . $file_name;
        } else {
            // If upload fails, continue without image
            // Could add error handling here
        }
    }
    
    // Update database
    if ($post_id > 0) {
        // Update existing post
        $sql = "UPDATE blog_posts SET 
                category_id = $category_id, 
                title = '$title', 
                slug = '$slug',
                excerpt = '$excerpt', 
                content = '" . mysqli_real_escape_string($conn, $content) . "', 
                featured_image = '$featured_image',
                author = '$author',
                status = '$status',
                publish_date = '$publish_date'
                WHERE id = $post_id";
        
        if (executeQuery($sql)) {
            // Update category post count
            $sql = "UPDATE blog_categories SET post_count = (
                    SELECT COUNT(*) FROM blog_posts WHERE category_id = blog_categories.id
                    )";
            executeQuery($sql);
            
            header('Location: blog.php?success=post_updated');
            exit;
        } else {
            header('Location: blog.php?error=update_failed');
            exit;
        }
    } else {
        // Insert new post
        $sql = "INSERT INTO blog_posts (category_id, title, slug, excerpt, content, featured_image, author, status, publish_date) 
                VALUES ($category_id, '$title', '$slug', '$excerpt', '" . mysqli_real_escape_string($conn, $content) . "', '$featured_image', '$author', '$status', '$publish_date')";
        
        if (executeQuery($sql)) {
            // Update category post count
            $sql = "UPDATE blog_categories SET post_count = (
                    SELECT COUNT(*) FROM blog_posts WHERE category_id = blog_categories.id
                    )";
            executeQuery($sql);
            
            header('Location: blog.php?success=post_added');
            exit;
        } else {
            header('Location: blog.php?error=create_failed');
            exit;
        }
    }
} else {
    // If not POST request, redirect to blog page
    header('Location: blog.php');
    exit;
}
?> 