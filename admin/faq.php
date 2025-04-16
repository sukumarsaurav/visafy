<?php 
include('includes/header.php');
include('includes/db_connection.php');

// Handle category deletion
if (isset($_GET['delete_category']) && is_numeric($_GET['delete_category'])) {
    $category_id = intval($_GET['delete_category']);
    $sql = "DELETE FROM faq_categories WHERE id = $category_id";
    
    if (executeQuery($sql)) {
        // Redirect to avoid resubmission
        header('Location: faq.php?success=category_deleted');
        exit;
    } else {
        // Error message will be shown on the page
        $error = "Failed to delete category.";
    }
}

// Handle question deletion
if (isset($_GET['delete_question']) && is_numeric($_GET['delete_question'])) {
    $question_id = intval($_GET['delete_question']);
    $sql = "DELETE FROM faq_items WHERE id = $question_id";
    
    if (executeQuery($sql)) {
        // Redirect to avoid resubmission
        header('Location: faq.php?success=question_deleted');
        exit;
    } else {
        // Error message will be shown on the page
        $error = "Failed to delete question.";
    }
}

// Get all FAQ categories
$sql = "SELECT * FROM faq_categories ORDER BY display_order, name";
$categories = executeQuery($sql);

// Initialize items array
$items = [];

// If categories exist, get all questions grouped by category
if ($categories && $categories->num_rows > 0) {
    $sql = "SELECT fi.*, fc.name as category_name 
            FROM faq_items fi 
            JOIN faq_categories fc ON fi.category_id = fc.id 
            ORDER BY fc.display_order, fi.display_order, fi.question";
    $result = executeQuery($sql);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $items[$row['category_id']][] = $row;
        }
    }
}
?>

<div class="admin-content-header">
    <h1>FAQ Management</h1>
    <p>Manage frequently asked questions and categories</p>
</div>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<?php if (isset($_GET['success'])): ?>
<div class="alert alert-success">
    <?php 
    switch ($_GET['success']) {
        case 'category_added':
            echo "Category added successfully.";
            break;
        case 'category_updated':
            echo "Category updated successfully.";
            break;
        case 'category_deleted':
            echo "Category deleted successfully.";
            break;
        case 'question_added':
            echo "Question added successfully.";
            break;
        case 'question_updated':
            echo "Question updated successfully.";
            break;
        case 'question_deleted':
            echo "Question deleted successfully.";
            break;
        default:
            echo "Operation completed successfully.";
    }
    ?>
</div>
<?php endif; ?>

<div class="admin-controls">
    <div>
        <a href="faq_category_edit.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Category</a>
        <a href="faq_item_edit.php" class="btn btn-secondary"><i class="fas fa-plus"></i> Add New Question</a>
    </div>
</div>

<div class="admin-tabs">
    <div class="tab-header">
        <button class="tab-btn active" data-tab="categories">Categories</button>
        <button class="tab-btn" data-tab="questions">All Questions</button>
    </div>
    
    <div class="tab-content">
        <!-- Categories Tab -->
        <div class="tab-pane active" id="categories-tab">
            <div class="admin-card">
                <h2>FAQ Categories</h2>
                
                <?php if ($categories && $categories->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Icon</th>
                                <th>Questions</th>
                                <th>Display Order</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($category = $categories->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $category['id']; ?></td>
                                <td><?php echo $category['name']; ?></td>
                                <td><i class="<?php echo $category['icon']; ?>"></i> <?php echo $category['icon']; ?></td>
                                <td><?php echo isset($items[$category['id']]) ? count($items[$category['id']]) : 0; ?></td>
                                <td><?php echo $category['display_order']; ?></td>
                                <td class="actions-cell">
                                <div class="action-buttons">
                                    <a href="faq_category_edit.php?id=<?php echo $category['id']; ?>" class="action-btn edit-btn"><i class="fas fa-edit"></i></a>
                                    <a href="faq.php?delete_category=<?php echo $category['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this category? All associated questions will also be deleted.');"><i class="fas fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p>No categories found. <a href="faq_category_edit.php">Add your first category</a>.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Questions Tab -->
        <div class="tab-pane" id="questions-tab">
            <div class="admin-card">
                <h2>All FAQ Questions</h2>
                
                <?php if ($categories && $categories->num_rows > 0): ?>
                    <?php 
                    $categories->data_seek(0); // Reset the category result pointer
                    while ($category = $categories->fetch_assoc()): 
                    ?>
                    <div class="category-questions">
                        <h3><?php echo $category['name']; ?> <a href="faq_item_edit.php?category_id=<?php echo $category['id']; ?>" class="btn btn-sm btn-secondary"><i class="fas fa-plus"></i> Add Question</a></h3>
                        
                        <?php if (isset($items[$category['id']]) && !empty($items[$category['id']])): ?>
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Question</th>
                                        <th>Display Order</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items[$category['id']] as $item): ?>
                                    <tr>
                                        <td><?php echo $item['id']; ?></td>
                                        <td><?php echo $item['question']; ?></td>
                                        <td><?php echo $item['display_order']; ?></td>
                                        <td class="actions-cell">
                                            <div class="action-buttons">
                                                <a href="view_faq.php?id=<?php echo $item['id']; ?>" class="action-btn view-btn" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="edit_faq.php?id=<?php echo $item['id']; ?>" class="action-btn edit-btn" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="action-btn delete-btn" title="Delete" 
                                                        onclick="confirmDelete(<?php echo $item['id']; ?>, '<?php echo htmlspecialchars($item['question']); ?>')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <p>No questions in this category. <a href="faq_item_edit.php?category_id=<?php echo $category['id']; ?>">Add your first question</a>.</p>
                        <?php endif; ?>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                <p>No categories found. Please <a href="faq_category_edit.php">add a category</a> first.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab functionality
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabPanes = document.querySelectorAll('.tab-pane');
        
        tabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons and panes
                tabBtns.forEach(b => b.classList.remove('active'));
                tabPanes.forEach(p => p.classList.remove('active'));
                
                // Add active class to clicked button and corresponding pane
                btn.classList.add('active');
                document.getElementById(btn.dataset.tab + '-tab').classList.add('active');
            });
        });
    });

    function confirmDelete(id, question) {
        if (confirm('Are you sure you want to delete FAQ "' + question + '"?')) {
            window.location.href = 'delete_faq.php?id=' + id;
        }
    }
</script>

<?php include('includes/footer.php'); ?> 