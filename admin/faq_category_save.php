<?php
include('includes/db_connection.php');

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
    $name = sanitize($_POST['name']);
    $icon = sanitize($_POST['icon']);
    $display_order = intval($_POST['display_order']);
    
    if ($category_id > 0) {
        // Update existing category
        $sql = "UPDATE faq_categories SET 
                name = '$name', 
                icon = '$icon', 
                display_order = $display_order
                WHERE id = $category_id";
        
        if (executeQuery($sql)) {
            header('Location: faq.php?success=category_updated');
            exit;
        } else {
            header('Location: faq.php?error=update_failed');
            exit;
        }
    } else {
        // Insert new category
        $sql = "INSERT INTO faq_categories (name, icon, display_order) 
                VALUES ('$name', '$icon', $display_order)";
        
        if (executeQuery($sql)) {
            header('Location: faq.php?success=category_added');
            exit;
        } else {
            header('Location: faq.php?error=create_failed');
            exit;
        }
    }
} else {
    // If not POST request, redirect to FAQ page
    header('Location: faq.php');
    exit;
}
?> 