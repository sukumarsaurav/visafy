<?php
include('includes/db_connection.php');

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
    $category_id = intval($_POST['category_id']);
    $question = sanitize($_POST['question']);
    $answer = $_POST['answer']; // Don't sanitize to allow HTML formatting
    $display_order = intval($_POST['display_order']);
    
    if ($item_id > 0) {
        // Update existing question
        $sql = "UPDATE faq_items SET 
                category_id = $category_id, 
                question = '$question', 
                answer = '" . mysqli_real_escape_string($conn, $answer) . "', 
                display_order = $display_order
                WHERE id = $item_id";
        
        if (executeQuery($sql)) {
            header('Location: faq.php?success=question_updated');
            exit;
        } else {
            header('Location: faq.php?error=update_failed');
            exit;
        }
    } else {
        // Insert new question
        $sql = "INSERT INTO faq_items (category_id, question, answer, display_order) 
                VALUES ($category_id, '$question', '" . mysqli_real_escape_string($conn, $answer) . "', $display_order)";
        
        if (executeQuery($sql)) {
            header('Location: faq.php?success=question_added');
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