<?php
include('includes/db_connection.php');

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
    $name = sanitize($_POST['name']);
    $slug = sanitize($_POST['slug']);
    $icon = sanitize($_POST['icon']);
    $display_order = intval($_POST['display_order']);
    
    // Check if slug exists (except for the current category when updating)
    $slug_check_sql = "SELECT id FROM blog_categories WHERE slug = '$slug'" . 
                     ($category_id > 0 ? " AND id != $category_id" : "");
    $slug_check = executeQuery($slug_check_sql);
    
    if ($slug_check && $slug_check->num_rows > 0) {
        // Slug already exists
        header('Location: blog_category_edit.php' . ($category_id > 0 ? "?id=$category_id" : "") . '&error=slug_exists');
        exit;
    }
    
    if ($category_id > 0) {
        // Update existing category
        $sql = "UPDATE blog_categories SET 
                name = '$name', 
                slug = '$slug',
                icon = '$icon', 
                display_order = $display_order
                WHERE id = $category_id";
        
        if (executeQuery($sql)) {
            header('Location: blog.php?success=category_updated');
            exit;
        } else {
            header('Location: blog.php?error=update_failed');
            exit;
        }
    } else {
        // Insert new category
        $sql = "INSERT INTO blog_categories (name, slug, icon, display_order) 
                VALUES ('$name', '$slug', '$icon', $display_order)";
        
        if (executeQuery($sql)) {
            header('Location: blog.php?success=category_added');
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