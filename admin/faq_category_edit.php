<?php
include('includes/header.php');
include('includes/db_connection.php');

// Initialize variables
$category = [
    'id' => 0,
    'name' => '',
    'icon' => 'fas fa-question-circle',
    'display_order' => 0
];

// Check if editing existing category
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $category_id = intval($_GET['id']);
    $sql = "SELECT * FROM faq_categories WHERE id = $category_id";
    $result = executeQuery($sql);
    
    if ($result && $result->num_rows > 0) {
        $category = $result->fetch_assoc();
    } else {
        // Category not found, redirect to categories page
        header('Location: faq.php?error=category_not_found');
        exit;
    }
}

// Available Font Awesome icons for selection
$available_icons = [
    'fas fa-question-circle', 
    'fas fa-info-circle', 
    'fas fa-book', 
    'fas fa-graduation-cap', 
    'fas fa-users', 
    'fas fa-paper-plane', 
    'fas fa-passport', 
    'fas fa-file-alt', 
    'fas fa-dollar-sign', 
    'fas fa-plane', 
    'fas fa-building', 
    'fas fa-home', 
    'fas fa-map-marker-alt', 
    'fas fa-briefcase'
];
?>

<div class="admin-content-header">
    <h1><?php echo $category['id'] ? 'Edit' : 'Add'; ?> FAQ Category</h1>
    <p><a href="faq.php">← Back to FAQ Management</a></p>
</div>

<div class="admin-form-container">
    <form method="post" action="faq_category_save.php" class="admin-form">
        <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
        
        <div class="form-group">
            <label for="name">Category Name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($category['name']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="display_order">Display Order</label>
            <input type="number" id="display_order" name="display_order" value="<?php echo $category['display_order']; ?>" min="0">
            <small>Categories with lower order values will be displayed first.</small>
        </div>
        
        <div class="form-group">
            <label>Select Icon</label>
            <div class="icon-selection" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 10px; margin-top: 10px;">
                <?php foreach ($available_icons as $icon): ?>
                <label class="icon-option" style="display: flex; align-items: center; padding: 10px; border: 1px solid #ddd; border-radius: 5px; cursor: pointer; <?php if ($icon === $category['icon']) echo 'background-color: #f0f0f0;'; ?>">
                    <input type="radio" name="icon" value="<?php echo $icon; ?>" <?php if ($icon === $category['icon']) echo 'checked'; ?> style="margin-right: 10px;">
                    <i class="<?php echo $icon; ?>"></i>
                    <span style="margin-left: 5px; font-size: 0.8rem;"><?php echo str_replace(['fas ', 'fa-'], ['', ''], $icon); ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?php echo $category['id'] ? 'Update' : 'Add'; ?> Category</button>
            <a href="faq.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Highlight selected icon
        const iconOptions = document.querySelectorAll('.icon-option');
        
        iconOptions.forEach(option => {
            const radio = option.querySelector('input[type="radio"]');
            
            radio.addEventListener('change', function() {
                // Reset all options
                iconOptions.forEach(opt => {
                    opt.style.backgroundColor = '';
                });
                
                // Highlight the selected option
                if (this.checked) {
                    option.style.backgroundColor = '#f0f0f0';
                }
            });
        });
    });
</script>

<?php include('includes/footer.php'); ?> 