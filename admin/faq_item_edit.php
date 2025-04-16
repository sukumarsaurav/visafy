<?php
include('includes/header.php');
include('includes/db_connection.php');

// Initialize variables
$item = [
    'id' => 0,
    'category_id' => isset($_GET['category_id']) ? intval($_GET['category_id']) : 0,
    'question' => '',
    'answer' => '',
    'display_order' => 0
];

// Check if editing existing question
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $item_id = intval($_GET['id']);
    $sql = "SELECT * FROM faq_items WHERE id = $item_id";
    $result = executeQuery($sql);
    
    if ($result && $result->num_rows > 0) {
        $item = $result->fetch_assoc();
    } else {
        // Question not found, redirect to FAQ page
        header('Location: faq.php?error=question_not_found');
        exit;
    }
}

// Get all categories for dropdown
$sql = "SELECT * FROM faq_categories ORDER BY display_order, name";
$categories = executeQuery($sql);

if (!$categories || $categories->num_rows === 0) {
    // No categories, redirect to add category page
    header('Location: faq_category_edit.php?error=no_categories');
    exit;
}
?>

<div class="admin-content-header">
    <h1><?php echo $item['id'] ? 'Edit' : 'Add'; ?> FAQ Question</h1>
    <p><a href="faq.php">← Back to FAQ Management</a></p>
</div>

<div class="admin-form-container">
    <form method="post" action="faq_item_save.php" class="admin-form">
        <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
        
        <div class="form-group">
            <label for="category_id">Category</label>
            <select id="category_id" name="category_id" required>
                <option value="">Select a category</option>
                <?php while ($category = $categories->fetch_assoc()): ?>
                    <option value="<?php echo $category['id']; ?>" <?php if ($category['id'] == $item['category_id']) echo 'selected'; ?>>
                        <?php echo $category['name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="question">Question</label>
            <input type="text" id="question" name="question" value="<?php echo htmlspecialchars($item['question']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="answer">Answer</label>
            <textarea id="answer" name="answer" rows="6" required><?php echo htmlspecialchars($item['answer']); ?></textarea>
            <small>You can use HTML formatting in the answer.</small>
        </div>
        
        <div class="form-group">
            <label for="display_order">Display Order</label>
            <input type="number" id="display_order" name="display_order" value="<?php echo $item['display_order']; ?>" min="0">
            <small>Questions with lower order values will be displayed first within their category.</small>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?php echo $item['id'] ? 'Update' : 'Add'; ?> Question</button>
            <a href="faq.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include('includes/footer.php'); ?> 