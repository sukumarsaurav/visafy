<?php
$page_title = "Edit Guide Category";
include('includes/header.php');
include('includes/db_connection.php');

// Initialize variables
$id = '';
$name = '';
$slug = '';
$icon = 'fas fa-book';
$display_order = 0;

// Check if we're editing an existing category
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql = "SELECT * FROM guide_categories WHERE id = $id";
    $result = executeQuery($sql);
    
    if ($result && $result->num_rows > 0) {
        $category = $result->fetch_assoc();
        $name = $category['name'];
        $slug = $category['slug'];
        $icon = $category['icon'];
        $display_order = $category['display_order'];
    } else {
        // Category not found
        header('Location: guides.php');
        exit;
    }
}

// Define common icons for categories
$common_icons = [
    'fas fa-book' => 'Book',
    'fas fa-passport' => 'Passport',
    'fas fa-graduation-cap' => 'Graduation Cap',
    'fas fa-plane' => 'Plane',
    'fas fa-users' => 'Users/Family',
    'fas fa-briefcase' => 'Briefcase/Work',
    'fas fa-university' => 'University',
    'fas fa-landmark' => 'Landmark/Government',
    'fas fa-file-alt' => 'Document',
    'fas fa-globe' => 'Globe',
    'fas fa-flag' => 'Flag',
    'fas fa-home' => 'Home',
    'fas fa-map-marker-alt' => 'Location'
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $name = sanitize($_POST['name']);
    $slug = sanitize($_POST['slug']);
    $icon = sanitize($_POST['icon']);
    $display_order = (int)$_POST['display_order'];
    
    // Generate slug if not provided
    if (empty($slug)) {
        $slug = strtolower(str_replace(' ', '-', $name));
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
    }
    
    // Validate inputs
    $errors = [];
    if (empty($name)) {
        $errors[] = "Category name is required.";
    }
    
    if (empty($errors)) {
        if (!empty($id)) {
            // Update existing category
            $sql = "UPDATE guide_categories SET name = '$name', slug = '$slug', icon = '$icon', display_order = $display_order, updated_at = NOW() WHERE id = $id";
            if (executeQuery($sql)) {
                $success_message = "Category updated successfully.";
            } else {
                $error_message = "Error updating category. Please try again.";
            }
        } else {
            // Check if slug already exists
            $check_sql = "SELECT id FROM guide_categories WHERE slug = '$slug'";
            $check_result = executeQuery($check_sql);
            
            if ($check_result && $check_result->num_rows > 0) {
                $errors[] = "A category with this slug already exists. Please choose a different name or provide a unique slug.";
            } else {
                // Insert new category
                $sql = "INSERT INTO guide_categories (name, slug, icon, display_order) VALUES ('$name', '$slug', '$icon', $display_order)";
                if (executeQuery($sql)) {
                    $success_message = "Category added successfully.";
                    // Clear form fields for a new entry
                    $id = '';
                    $name = '';
                    $slug = '';
                    $icon = 'fas fa-book';
                    $display_order = 0;
                } else {
                    $error_message = "Error adding category. Please try again.";
                }
            }
        }
    }
}
?>

<!-- Page Title -->
<div class="admin-page-title">
    <h1><i class="fas fa-folder-plus"></i> <?php echo empty($id) ? 'Add New' : 'Edit'; ?> Guide Category</h1>
    <div class="admin-page-actions">
        <a href="guides.php" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Guides
        </a>
    </div>
</div>

<!-- Success/Error Messages -->
<?php if (isset($success_message)): ?>
<div class="alert alert-success">
    <?php echo $success_message; ?>
</div>
<?php endif; ?>

<?php if (isset($error_message)): ?>
<div class="alert alert-danger">
    <?php echo $error_message; ?>
</div>
<?php endif; ?>

<?php if (isset($errors) && !empty($errors)): ?>
<div class="alert alert-danger">
    <ul class="mb-0">
        <?php foreach ($errors as $error): ?>
        <li><?php echo $error; ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<!-- Category Form -->
<div class="admin-card">
    <div class="admin-card-header">
        <h2><?php echo empty($id) ? 'Add New' : 'Edit'; ?> Category</h2>
    </div>
    <div class="admin-card-body">
        <form method="post" action="">
            <div class="form-group">
                <label for="name">Category Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="slug">Slug</label>
                <input type="text" class="form-control" id="slug" name="slug" value="<?php echo $slug; ?>" placeholder="Leave blank to auto-generate">
                <small class="form-text text-muted">The "slug" is the URL-friendly version of the name. It is usually lowercase and contains only letters, numbers, and hyphens.</small>
            </div>
            
            <div class="form-group">
                <label for="icon">Icon</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="<?php echo $icon; ?>"></i></span>
                    </div>
                    <select class="form-control" id="icon" name="icon">
                        <?php foreach ($common_icons as $icon_class => $icon_name): ?>
                        <option value="<?php echo $icon_class; ?>" <?php echo $icon === $icon_class ? 'selected' : ''; ?>>
                            <?php echo $icon_name; ?> (<?php echo $icon_class; ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <small class="form-text text-muted">Select an icon to represent this category.</small>
            </div>
            
            <div class="form-group">
                <label for="display_order">Display Order</label>
                <input type="number" class="form-control" id="display_order" name="display_order" value="<?php echo $display_order; ?>" min="0">
                <small class="form-text text-muted">Categories with lower numbers will be displayed first.</small>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo empty($id) ? 'Add Category' : 'Update Category'; ?>
                </button>
                <a href="guides.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript to update icon preview -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const iconSelect = document.getElementById('icon');
    const iconPreview = iconSelect.previousElementSibling.querySelector('i');
    
    iconSelect.addEventListener('change', function() {
        iconPreview.className = this.value;
    });
    
    // Auto-generate slug from name
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    
    nameInput.addEventListener('blur', function() {
        if (slugInput.value === '') {
            const slug = this.value.toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-');
            slugInput.value = slug;
        }
    });
});
</script>

<?php include('includes/footer.php'); ?> 