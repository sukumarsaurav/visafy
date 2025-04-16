<?php
include('includes/header.php');
include('includes/db_connection.php');

// Check if ID is provided
if (!isset($_GET['id'])) {
    header('Location: news.php');
    exit;
}

$article_id = intval($_GET['id']);

// Get article details
$sql = "SELECT * FROM news_articles WHERE id = $article_id";
$result = executeQuery($sql);

if (!$result || $result->num_rows === 0) {
    header('Location: news.php?error=not_found');
    exit;
}

$article = $result->fetch_assoc();
?>

<div class="admin-content-header">
    <h1>Edit News Article</h1>
    <p><a href="news.php">← Back to News List</a></p>
</div>

<div class="admin-form-container">
    <form id="news-form" action="news_save.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="article_id" value="<?php echo $article_id; ?>">
        
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" value="<?php echo $article['title']; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="slug">Slug</label>
            <input type="text" id="slug" name="slug" value="<?php echo $article['slug']; ?>" required>
            <small>This will be used in the URL: immigration-news/your-slug</small>
        </div>
        
        <div class="form-group">
            <label for="image">Featured Image</label>
            <input type="file" id="image" name="image" accept="image/*">
            <?php if ($article['image']): ?>
                <div id="current-image" class="image-preview">
                    <img src="../images/news/<?php echo $article['image']; ?>" alt="Current Image">
                    <small>Current image: <?php echo $article['image']; ?></small>
                </div>
            <?php endif; ?>
            <div id="image-preview" class="image-preview" style="display: none;">
                <img src="" alt="Preview" id="preview-img">
                <button type="button" id="remove-image">Remove</button>
            </div>
        </div>
        
        <div class="form-group">
            <label for="excerpt">Excerpt</label>
            <textarea id="excerpt" name="excerpt" rows="3"><?php echo $article['excerpt']; ?></textarea>
            <small>A short description for the news listing</small>
        </div>
        
        <div class="form-group">
            <label for="content">Content</label>
            <textarea id="content" name="content" rows="15" required><?php echo $article['content']; ?></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="publish_date">Publish Date</label>
                <input type="date" id="publish_date" name="publish_date" value="<?php echo date('Y-m-d', strtotime($article['publish_date'])); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="draft" <?php echo $article['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                    <option value="published" <?php echo $article['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
                </select>
            </div>
        </div>
        
        <div class="form-actions">
            <a href="news.php" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Update Article</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-generate slug from title
        const titleInput = document.getElementById('title');
        const slugInput = document.getElementById('slug');
        let slugModified = false;
        
        slugInput.addEventListener('input', function() {
            slugModified = true;
        });
        
        titleInput.addEventListener('input', function() {
            if (!slugModified) {
                // Create slug from title
                slugInput.value = this.value
                    .toLowerCase()
                    .replace(/[^\w\s-]/g, '') // Remove special chars
                    .replace(/\s+/g, '-')     // Replace spaces with -
                    .replace(/-+/g, '-')      // Replace multiple - with single -
                    .trim();                  // Trim whitespace
            }
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
                    
                    // Hide current image preview if showing a new one
                    const currentImage = document.getElementById('current-image');
                    if (currentImage) {
                        currentImage.style.display = 'none';
                    }
                }
                reader.readAsDataURL(file);
            }
        });
        
        removeImageBtn.addEventListener('click', function() {
            imageInput.value = '';
            imagePreview.style.display = 'none';
            
            // Show current image again if exists
            const currentImage = document.getElementById('current-image');
            if (currentImage) {
                currentImage.style.display = 'block';
            }
        });
    });
</script>

<?php include('includes/footer.php'); ?> 