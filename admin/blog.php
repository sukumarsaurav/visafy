<?php 
include('includes/header.php');
include('includes/db_connection.php');

// Handle category deletion
if (isset($_GET['delete_category']) && is_numeric($_GET['delete_category'])) {
    $category_id = intval($_GET['delete_category']);
    $sql = "DELETE FROM blog_categories WHERE id = $category_id";
    
    if (executeQuery($sql)) {
        header('Location: blog.php?success=category_deleted');
        exit;
    } else {
        $error = "Failed to delete category.";
    }
}

// Handle post deletion
if (isset($_GET['delete_post']) && is_numeric($_GET['delete_post'])) {
    $post_id = intval($_GET['delete_post']);
    
    // Get image path before deleting post
    $sql = "SELECT featured_image FROM blog_posts WHERE id = $post_id";
    $result = executeQuery($sql);
    if ($result && $result->num_rows > 0) {
        $post = $result->fetch_assoc();
        $image_path = $post['featured_image'];
    }
    
    $sql = "DELETE FROM blog_posts WHERE id = $post_id";
    if (executeQuery($sql)) {
        // Delete image file if it exists
        if (!empty($image_path) && file_exists("../$image_path")) {
            unlink("../$image_path");
        }
        
        // Update category post count
        $sql = "UPDATE blog_categories SET post_count = (
                SELECT COUNT(*) FROM blog_posts WHERE category_id = blog_categories.id
                )";
        executeQuery($sql);
        
        header('Location: blog.php?success=post_deleted');
        exit;
    } else {
        $error = "Failed to delete post.";
    }
}

// Get all blog categories
$sql = "SELECT * FROM blog_categories ORDER BY display_order, name";
$categories = executeQuery($sql);

// Get all blog posts with their categories
$sql = "SELECT p.*, c.name as category_name 
        FROM blog_posts p 
        JOIN blog_categories c ON p.category_id = c.id 
        ORDER BY p.status DESC, p.publish_date DESC";
$posts = executeQuery($sql);
?>

<div class="admin-content-header">
    <h1>Blog Management</h1>
    <p>Manage blog categories and posts</p>
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
        case 'post_added':
            echo "Blog post added successfully.";
            break;
        case 'post_updated':
            echo "Blog post updated successfully.";
            break;
        case 'post_deleted':
            echo "Blog post deleted successfully.";
            break;
        default:
            echo "Operation completed successfully.";
    }
    ?>
</div>
<?php endif; ?>

<div class="admin-controls">
    <div>
        <a href="blog_post_edit.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Post</a>
        <a href="blog_category_edit.php" class="btn btn-secondary"><i class="fas fa-plus"></i> Add New Category</a>
    </div>
</div>

<div class="admin-tabs">
    <div class="tab-header">
        <button class="tab-btn active" data-tab="posts">Posts</button>
        <button class="tab-btn" data-tab="categories">Categories</button>
    </div>
    
    <div class="tab-content">
        <!-- Posts Tab -->
        <div class="tab-pane active" id="posts-tab">
            <div class="admin-card">
                <h2>Blog Posts</h2>
                
                <?php if ($posts && $posts->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($post = $posts->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $post['id']; ?></td>
                                <td>
                                    <?php if (!empty($post['featured_image'])): ?>
                                    <img src="../<?php echo $post['featured_image']; ?>" alt="<?php echo $post['title']; ?>" style="width: 50px; height: 40px; object-fit: cover; border-radius: 4px;">
                                    <?php else: ?>
                                    <div style="width: 50px; height: 40px; background: #eee; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-image" style="color: #aaa;"></i>
                                    </div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $post['title']; ?></td>
                                <td><?php echo $post['category_name']; ?></td>
                                <td>
                                    <span class="status-badge <?php echo $post['status'] === 'published' ? 'status-published' : 'status-draft'; ?>">
                                        <?php echo ucfirst($post['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($post['publish_date'])); ?></td>
                                <td class="actions-cell">
                                    <div class="action-buttons">
                                        <a href="../blog/<?php echo $post['slug']; ?>" class="action-btn view-btn" target="_blank" title="View"><i class="fas fa-eye"></i></a>
                                        <a href="blog_post_edit.php?id=<?php echo $post['id']; ?>" class="action-btn edit-btn" title="Edit"><i class="fas fa-edit"></i></a>
                                        <a href="blog.php?delete_post=<?php echo $post['id']; ?>" class="action-btn delete-btn" title="Delete" onclick="return confirm('Are you sure you want to delete this post?');"><i class="fas fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p>No blog posts found. <a href="blog_post_edit.php">Add your first post</a>.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Categories Tab -->
        <div class="tab-pane" id="categories-tab">
            <div class="admin-card">
                <h2>Blog Categories</h2>
                
                <?php if ($categories && $categories->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Icon</th>
                                <th>Posts</th>
                                <th>Display Order</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($category = $categories->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $category['id']; ?></td>
                                <td><?php echo $category['name']; ?></td>
                                <td><?php echo $category['slug']; ?></td>
                                <td><i class="<?php echo $category['icon']; ?>"></i></td>
                                <td><?php echo $category['post_count']; ?></td>
                                <td><?php echo $category['display_order']; ?></td>
                                <td class="actions-cell">
                                    <div class="action-buttons">
                                        <a href="blog_category_edit.php?id=<?php echo $category['id']; ?>" class="action-btn edit-btn" title="Edit"><i class="fas fa-edit"></i></a>
                                        <a href="blog.php?delete_category=<?php echo $category['id']; ?>" class="action-btn delete-btn" title="Delete" onclick="return confirm('Are you sure you want to delete this category? All associated posts will also be deleted.');"><i class="fas fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p>No categories found. <a href="blog_category_edit.php">Add your first category</a>.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.status-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}
.status-published {
    background-color: #e1f3e8;
    color: #1e7e34;
}
.status-draft {
    background-color: #f8f9fa;
    color: #6c757d;
}
</style>

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
</script>

<?php include('includes/footer.php'); ?> 