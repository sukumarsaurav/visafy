<?php
include('includes/db_connection.php');

// Check if ID is provided
if (isset($_GET['id'])) {
    $article_id = intval($_GET['id']);
    
    // Get image filename before deleting
    $sql = "SELECT image FROM news_articles WHERE id = $article_id";
    $result = executeQuery($sql);
    
    if ($result && $result->num_rows > 0) {
        $article = $result->fetch_assoc();
        
        // Delete image file if exists
        if ($article['image']) {
            $image_path = "../images/news/" . $article['image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        // Delete article from database
        $sql = "DELETE FROM news_articles WHERE id = $article_id";
        
        if (executeQuery($sql)) {
            header('Location: news.php?success=deleted');
            exit;
        } else {
            header('Location: news.php?error=delete_failed');
            exit;
        }
    } else {
        header('Location: news.php?error=not_found');
        exit;
    }
} else {
    header('Location: news.php');
    exit;
}
?> 