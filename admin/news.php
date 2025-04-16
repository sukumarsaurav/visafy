<?php 
include('includes/header.php');
include('includes/db_connection.php');

// Get news articles from database
$sql = "SELECT * FROM news_articles ORDER BY publish_date DESC";
$result = executeQuery($sql);
?>

<div class="admin-content-header">
    <h1>Immigration News</h1>
    <p>Manage news articles displayed on the website</p>
</div>

<div class="admin-controls">
    <div class="admin-search">
        <input type="text" placeholder="Search articles..." id="news-search">
        <button><i class="fas fa-search"></i></button>
    </div>
    
    <div class="admin-filters">
        <select id="status-filter">
            <option value="">All Statuses</option>
            <option value="published">Published</option>
            <option value="draft">Draft</option>
        </select>
        
        <select id="date-filter">
            <option value="">All Dates</option>
            <option value="this-week">This Week</option>
            <option value="this-month">This Month</option>
            <option value="this-year">This Year</option>
        </select>
    </div>
    
    <div class="admin-actions">
        <button class="btn-primary" id="add-news-btn">
            <i class="fas fa-plus"></i> Add New Article
        </button>
    </div>
</div>

<div class="admin-table-container">
    <table class="admin-table news-table">
        <thead>
            <tr>
                <th>
                    <input type="checkbox" id="select-all-news">
                </th>
                <th>ID <i class="fas fa-sort"></i></th>
                <th>Title <i class="fas fa-sort"></i></th>
                <th>Image</th>
                <th>Publish Date <i class="fas fa-sort"></i></th>
                <th>Status <i class="fas fa-sort"></i></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($article = $result->fetch_assoc()): ?>
                    <tr>
                        <td><input type="checkbox" class="news-select"></td>
                        <td>#<?php echo $article['id']; ?></td>
                        <td>
                            <div class="news-info">
                                <span class="news-title"><?php echo $article['title']; ?></span>
                                <span class="news-slug">/immigration-news/<?php echo $article['slug']; ?></span>
                            </div>
                        </td>
                        <td>
                            <?php if ($article['image']): ?>
                                <img src="../images/news/<?php echo $article['image']; ?>" alt="<?php echo $article['title']; ?>" class="thumbnail" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                            <?php else: ?>
                                <span class="no-image">No Image</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php echo date('M j, Y', strtotime($article['publish_date'])); ?>
                        </td>
                        <td>
                            <span class="status-badge status-<?php echo strtolower($article['status']); ?>">
                                <?php echo ucfirst($article['status']); ?>
                            </span>
                        </td>
                        <td class="actions-cell">
                            <div class="action-buttons">
                                <a href="../immigration-news/<?php echo $article['slug']; ?>" class="action-btn view-btn" title="View" target="_blank">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="news_edit.php?id=<?php echo $article['id']; ?>" class="action-btn edit-btn" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="action-btn delete-btn" title="Delete" 
                                        onclick="confirmDelete(<?php echo $article['id']; ?>, '<?php echo htmlspecialchars($article['title']); ?>')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center;">No news articles found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Add/Edit News Modal -->
<div id="edit-news-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add New Article</h3>
            <span class="modal-close">&times;</span>
        </div>
        <div class="modal-body">
            <form id="news-form" action="news_save.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="article_id" id="article_id">
                
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="slug">Slug</label>
                    <input type="text" id="slug" name="slug" required>
                    <small>This will be used in the URL: immigration-news/your-slug</small>
                </div>
                
                <div class="form-group">
                    <label for="image">Featured Image</label>
                    <input type="file" id="image" name="image" accept="image/*">
                    <div id="image-preview" class="image-preview" style="display: none;">
                        <img src="" alt="Preview" id="preview-img">
                        <button type="button" id="remove-image">Remove</button>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="excerpt">Excerpt</label>
                    <textarea id="excerpt" name="excerpt" rows="3"></textarea>
                    <small>A short description for the news listing</small>
                </div>
                
                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea id="content" name="content" rows="15" required></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="publish_date">Publish Date</label>
                        <input type="date" id="publish_date" name="publish_date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-secondary modal-close">Cancel</button>
                    <button type="submit" class="btn-primary">Save Article</button>
                </div>
            </form>
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
        // Add News Button
        const addNewsBtn = document.getElementById('add-news-btn');
        const editNewsModal = document.getElementById('edit-news-modal');
        const modalClose = document.querySelectorAll('.modal-close');
        const newsForm = document.getElementById('news-form');
        
        // Auto-generate slug from title
        const titleInput = document.getElementById('title');
        const slugInput = document.getElementById('slug');
        
        titleInput.addEventListener('input', function() {
            // Create slug from title
            slugInput.value = this.value
                .toLowerCase()
                .replace(/[^\w\s-]/g, '') // Remove special chars
                .replace(/\s+/g, '-')     // Replace spaces with -
                .replace(/-+/g, '-')      // Replace multiple - with single -
                .trim();                  // Trim whitespace
        });
        
        // Image preview
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('image-preview');
        const previewImg = document.getElementById('preview-img');
        const removeImageBtn = document.getElementById('remove-image');
        
        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    imagePreview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
        
        removeImageBtn.addEventListener('click', function() {
            imageInput.value = '';
            imagePreview.style.display = 'none';
        });
        
        addNewsBtn.addEventListener('click', function() {
            // Clear form
            newsForm.reset();
            document.getElementById('article_id').value = '';
            imagePreview.style.display = 'none';
            
            // Change modal title
            document.querySelector('#edit-news-modal .modal-header h3').textContent = 'Add New Article';
            
            // Show modal
            editNewsModal.style.display = 'flex';
        });
        
        // Edit News Buttons
        const editNewsBtns = document.querySelectorAll('.edit-news');
        editNewsBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const articleId = this.getAttribute('href').split('?id=')[1];
                
                // In a real app, you would fetch article details via AJAX
                // For demo, we'll just show the modal
                
                // Change modal title
                document.querySelector('#edit-news-modal .modal-header h3').textContent = 'Edit Article';
                
                // Show modal
                editNewsModal.style.display = 'flex';
            });
        });
        
        // Delete News Buttons
        const deleteNewsBtns = document.querySelectorAll('.delete-news');
        deleteNewsBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const articleId = this.getAttribute('data-id');
                if (confirm('Are you sure you want to delete this article?')) {
                    // Send AJAX request to delete
                    window.location.href = 'news_delete.php?id=' + articleId;
                }
            });
        });
        
        // Close Modal
        modalClose.forEach(btn => {
            btn.addEventListener('click', function() {
                editNewsModal.style.display = 'none';
            });
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === editNewsModal) {
                editNewsModal.style.display = 'none';
            }
        });
        
        // Select All Checkbox
        const selectAllCheckbox = document.getElementById('select-all-news');
        const newsCheckboxes = document.querySelectorAll('.news-select');
        
        selectAllCheckbox.addEventListener('change', function() {
            newsCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    });
    
    // Add the missing confirmDelete function
    function confirmDelete(id, title) {
        if (confirm('Are you sure you want to delete the article "' + title + '"?')) {
            window.location.href = 'news_delete.php?id=' + id;
        }
    }
</script>

<?php include('includes/footer.php'); ?> 