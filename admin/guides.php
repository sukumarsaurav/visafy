<?php
$page_title = "Guides Manager";
include('includes/header.php');
include('includes/db_connection.php');

// Check if guide deletion was requested
if (isset($_GET['delete_guide']) && !empty($_GET['delete_guide'])) {
    $guide_id = (int)$_GET['delete_guide'];
    
    // First delete any associated downloads
    $delete_downloads_sql = "DELETE FROM guide_downloads WHERE guide_id = $guide_id";
    executeQuery($delete_downloads_sql);
    
    // Now delete the guide
    $delete_sql = "DELETE FROM guides WHERE id = $guide_id";
    if (executeQuery($delete_sql)) {
        // Update guide count in category
        $category_id = (int)$_GET['category_id'];
        $update_count_sql = "UPDATE guide_categories SET guide_count = (SELECT COUNT(*) FROM guides WHERE category_id = $category_id) WHERE id = $category_id";
        executeQuery($update_count_sql);
        
        $success_message = "Guide deleted successfully.";
    } else {
        $error_message = "Error deleting guide. Please try again.";
    }
}

// Check if category deletion was requested
if (isset($_GET['delete_category']) && !empty($_GET['delete_category'])) {
    $category_id = (int)$_GET['delete_category'];
    
    // Check if there are guides in this category
    $check_sql = "SELECT COUNT(*) as guide_count FROM guides WHERE category_id = $category_id";
    $check_result = executeQuery($check_sql);
    $check_data = $check_result->fetch_assoc();
    
    if ($check_data['guide_count'] > 0) {
        $error_message = "Cannot delete category. There are guides associated with this category.";
    } else {
        $delete_sql = "DELETE FROM guide_categories WHERE id = $category_id";
        if (executeQuery($delete_sql)) {
            $success_message = "Category deleted successfully.";
        } else {
            $error_message = "Error deleting category. Please try again.";
        }
    }
}

// Get all guide categories
$categories_sql = "SELECT * FROM guide_categories ORDER BY display_order, name";
$categories = executeQuery($categories_sql);

// Get all guides with their categories
$guides_sql = "SELECT g.*, c.name as category_name 
              FROM guides g
              JOIN guide_categories c ON g.category_id = c.id
              ORDER BY g.created_at DESC";
$guides = executeQuery($guides_sql);

// Function to format date
function formatDate($date) {
    return date('M j, Y', strtotime($date));
}
?>

<div class="admin-content-header">
    <h1>Immigration Guides</h1>
    <p>Manage guides and categories displayed on the website</p>
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

<!-- Admin Controls -->
<div class="admin-controls">
    <div class="admin-search">
        <input type="text" placeholder="Search guides..." id="guides-search">
        <button><i class="fas fa-search"></i></button>
    </div>
    
    <div class="admin-filters">
        <select id="category-filter">
            <option value="">All Categories</option>
            <?php if ($categories && $categories->num_rows > 0): ?>
                <?php while ($category = $categories->fetch_assoc()): ?>
                <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                <?php endwhile; $categories->data_seek(0); ?>
            <?php endif; ?>
        </select>
        
        <select id="status-filter">
            <option value="">All Statuses</option>
            <option value="published">Published</option>
            <option value="draft">Draft</option>
        </select>
    </div>
    
    <div class="admin-actions">
        <button class="btn-primary" id="add-guide-btn" onclick="window.location.href='guide_edit.php';">
            <i class="fas fa-plus"></i> Add New Guide
        </button>
        <button class="btn-secondary" id="add-category-btn" onclick="window.location.href='guide_category_edit.php';">
            <i class="fas fa-folder-plus"></i> Add New Category
        </button>
    </div>
</div>

<!-- Categories Section -->
<div class="admin-table-container">
    <h2><i class="fas fa-folder"></i> Guide Categories</h2>
    <?php if ($categories && $categories->num_rows > 0): ?>
    <table class="admin-table categories-table">
        <thead>
            <tr>
                <th>
                    <input type="checkbox" id="select-all-categories">
                </th>
                <th>ID <i class="fas fa-sort"></i></th>
                <th>Name <i class="fas fa-sort"></i></th>
                <th>Slug</th>
                <th>Icon</th>
                <th>Display Order <i class="fas fa-sort"></i></th>
                <th>Guide Count <i class="fas fa-sort"></i></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($category = $categories->fetch_assoc()): ?>
            <tr>
                <td><input type="checkbox" class="category-select"></td>
                <td><?php echo $category['id']; ?></td>
                <td><?php echo $category['name']; ?></td>
                <td><?php echo $category['slug']; ?></td>
                <td><i class="<?php echo $category['icon']; ?>"></i> <?php echo $category['icon']; ?></td>
                <td><?php echo $category['display_order']; ?></td>
                <td><?php echo $category['guide_count']; ?></td>
                <td class="actions-cell">
                    <div class="action-buttons">
                        <a href="guide_category_edit.php?id=<?php echo $category['id']; ?>" class="action-btn edit-btn" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <?php if ($category['guide_count'] == 0): ?>
                        <button type="button" class="action-btn delete-btn" title="Delete" 
                               onclick="confirmDeleteCategory(<?php echo $category['id']; ?>, '<?php echo htmlspecialchars($category['name']); ?>')">
                            <i class="fas fa-trash"></i>
                        </button>
                        <?php else: ?>
                        <button type="button" class="action-btn delete-btn disabled" title="Cannot delete category with guides" disabled>
                            <i class="fas fa-trash"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p>No guide categories found. <a href="guide_category_edit.php">Create a category</a> to get started.</p>
    <?php endif; ?>
</div>

<!-- Guides Section -->
<div class="admin-table-container">
    <h2><i class="fas fa-book"></i> Guides</h2>
    <?php if ($guides && $guides->num_rows > 0): ?>
    <table class="admin-table guides-table">
        <thead>
            <tr>
                <th>
                    <input type="checkbox" id="select-all-guides">
                </th>
                <th>ID <i class="fas fa-sort"></i></th>
                <th>Title <i class="fas fa-sort"></i></th>
                <th>Category <i class="fas fa-sort"></i></th>
                <th>Featured Image</th>
                <th>Publish Date <i class="fas fa-sort"></i></th>
                <th>Status <i class="fas fa-sort"></i></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($guide = $guides->fetch_assoc()): ?>
            <tr>
                <td><input type="checkbox" class="guide-select"></td>
                <td>#<?php echo $guide['id']; ?></td>
                <td>
                    <div class="guide-info">
                        <span class="guide-title"><?php echo $guide['title']; ?></span>
                        <span class="guide-slug">/resources/guide-details.php?slug=<?php echo $guide['slug']; ?></span>
                    </div>
                </td>
                <td><?php echo $guide['category_name']; ?></td>
                <td>
                    <?php if ($guide['featured_image']): ?>
                        <img src="../<?php echo $guide['featured_image']; ?>" alt="<?php echo $guide['title']; ?>" class="thumbnail" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                    <?php else: ?>
                        <span class="no-image">No Image</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($guide['publish_date']): ?>
                        <?php echo formatDate($guide['publish_date']); ?>
                    <?php else: ?>
                        <em>Not published</em>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="status-badge status-<?php echo strtolower($guide['status']); ?>">
                        <?php echo ucfirst($guide['status']); ?>
                    </span>
                </td>
                <td class="actions-cell">
                    <div class="action-buttons">
                        <a href="../resources/guide-details.php?slug=<?php echo $guide['slug']; ?>" target="_blank" class="action-btn view-btn" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="guide_edit.php?id=<?php echo $guide['id']; ?>" class="action-btn edit-btn" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" class="action-btn delete-btn" title="Delete" 
                               onclick="confirmDeleteGuide(<?php echo $guide['id']; ?>, <?php echo $guide['category_id']; ?>, '<?php echo htmlspecialchars($guide['title']); ?>')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p>No guides found. <a href="guide_edit.php">Create a guide</a> to get started.</p>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle search functionality
        const searchInput = document.getElementById('guides-search');
        searchInput.addEventListener('input', function() {
            filterGuides();
        });
        
        // Handle category filter
        const categoryFilter = document.getElementById('category-filter');
        categoryFilter.addEventListener('change', function() {
            filterGuides();
        });
        
        // Handle status filter
        const statusFilter = document.getElementById('status-filter');
        statusFilter.addEventListener('change', function() {
            filterGuides();
        });
        
        // Select All Checkboxes for Categories
        const selectAllCategories = document.getElementById('select-all-categories');
        const categoryCheckboxes = document.querySelectorAll('.category-select');
        
        if (selectAllCategories) {
            selectAllCategories.addEventListener('change', function() {
                categoryCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
        }
        
        // Select All Checkboxes for Guides
        const selectAllGuides = document.getElementById('select-all-guides');
        const guideCheckboxes = document.querySelectorAll('.guide-select');
        
        if (selectAllGuides) {
            selectAllGuides.addEventListener('change', function() {
                guideCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
        }
        
        // Filter guides function
        function filterGuides() {
            const searchTerm = searchInput.value.toLowerCase();
            const categoryId = categoryFilter.value;
            const status = statusFilter.value;
            
            const guideRows = document.querySelectorAll('.guides-table tbody tr');
            
            guideRows.forEach(row => {
                const title = row.querySelector('.guide-title').textContent.toLowerCase();
                const rowCategoryId = row.cells[3].getAttribute('data-category-id') || '';
                const rowStatus = row.querySelector('.status-badge').textContent.toLowerCase();
                
                const matchesSearch = title.includes(searchTerm);
                const matchesCategory = categoryId === '' || rowCategoryId === categoryId;
                const matchesStatus = status === '' || rowStatus === status.toLowerCase();
                
                row.style.display = (matchesSearch && matchesCategory && matchesStatus) ? '' : 'none';
            });
        }
    });
    
    // Confirm delete guide
    function confirmDeleteGuide(id, categoryId, title) {
        if (confirm('Are you sure you want to delete the guide "' + title + '"?')) {
            window.location.href = 'guides.php?delete_guide=' + id + '&category_id=' + categoryId;
        }
    }
    
    // Confirm delete category
    function confirmDeleteCategory(id, name) {
        if (confirm('Are you sure you want to delete the category "' + name + '"?')) {
            window.location.href = 'guides.php?delete_category=' + id;
        }
    }
</script>

<?php include('includes/footer.php'); ?> 