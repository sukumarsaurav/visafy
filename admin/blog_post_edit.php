<?php
include('includes/header.php');
include('includes/db_connection.php');

// Initialize variables
$post = [
    'id' => 0,
    'category_id' => 0,
    'title' => '',
    'slug' => '',
    'excerpt' => '',
    'content' => '',
    'featured_image' => '',
    'author' => 'CANEXT Team',
    'status' => 'draft',
    'publish_date' => date('Y-m-d H:i:s')
];

// Check if editing existing post
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $post_id = intval($_GET['id']);
    $sql = "SELECT * FROM blog_posts WHERE id = $post_id";
    $result = executeQuery($sql);
    
    if ($result && $result->num_rows > 0) {
        $post = $result->fetch_assoc();
    } else {
        header('Location: blog.php?error=post_not_found');
        exit;
    }
}

// Get all categories for dropdown
$sql = "SELECT * FROM blog_categories ORDER BY display_order, name";
$categories = executeQuery($sql);

if (!$categories || $categories->num_rows === 0) {
    // No categories, redirect to add category page
    header('Location: blog_category_edit.php?error=no_categories');
    exit;
}
?>

<div class="admin-content-header">
    <h1><?php echo $post['id'] ? 'Edit' : 'Add'; ?> Blog Post</h1>
    <p><a href="blog.php">← Back to Blog Management</a></p>
</div>

<?php if (isset($_GET['error'])): ?>
<div class="alert alert-danger">
    <?php 
    switch ($_GET['error']) {
        case 'slug_exists':
            echo "A post with this slug already exists. Please choose a different slug.";
            break;
        default:
            echo "An error occurred. Please try again.";
    }
    ?>
</div>
<?php endif; ?>

<div class="admin-form-container">
    <form method="post" action="blog_post_save.php" class="admin-form" enctype="multipart/form-data">
        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
        
        <div class="form-row">
            <div class="form-group col-md-8">
                <label for="title">Post Title</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required onkeyup="generateSlug(this.value)">
            </div>
            
            <div class="form-group col-md-4">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select a category</option>
                    <?php while ($category = $categories->fetch_assoc()): ?>
                        <option value="<?php echo $category['id']; ?>" <?php if ($category['id'] == $post['category_id']) echo 'selected'; ?>>
                            <?php echo $category['name']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label for="slug">Slug</label>
            <input type="text" id="slug" name="slug" value="<?php echo htmlspecialchars($post['slug']); ?>" required>
            <small>URL-friendly version of the title. Used in post links.</small>
        </div>
        
        <div class="form-group">
            <label for="excerpt">Excerpt</label>
            <textarea id="excerpt" name="excerpt" rows="3"><?php echo htmlspecialchars($post['excerpt']); ?></textarea>
            <small>A short summary of the post. If left empty, an excerpt will be generated from the content.</small>
        </div>
        
        <div class="form-group">
            <label for="content">Content</label>
            <div id="simple-editor-container">
                <!-- Editor Tabs -->
                <ul class="editor-tabs">
                    <li class="active" data-tab="edit">Edit</li>
                    <li data-tab="preview">Preview</li>
                </ul>
                
                <!-- Editor Toolbar -->
                <div class="editor-toolbar">
                    <button type="button" data-action="bold" title="Bold"><strong>B</strong></button>
                    <button type="button" data-action="italic" title="Italic"><em>I</em></button>
                    <button type="button" data-action="h2" title="Heading 2">H2</button>
                    <button type="button" data-action="h3" title="Heading 3">H3</button>
                    <button type="button" data-action="h4" title="Heading 4">H4</button>
                    <button type="button" data-action="link" title="Insert Link"><span>🔗</span></button>
                    <button type="button" data-action="ul" title="Bullet List">• List</button>
                    <button type="button" data-action="ol" title="Numbered List">1. List</button>
                    <button type="button" data-action="p" title="Paragraph">¶</button>
                </div>
                
                <!-- Editor Panels -->
                <div id="editor-edit-panel" class="editor-panel active">
                    <textarea id="content" name="content" rows="15" required><?php echo htmlspecialchars($post['content']); ?></textarea>
                </div>
                <div id="editor-preview-panel" class="editor-panel">
                    <div id="preview-content"></div>
                </div>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="featured_image">Featured Image</label>
                <input type="file" id="featured_image" name="featured_image" accept="image/*">
                
                <?php if (!empty($post['featured_image'])): ?>
                <div class="current-image">
                    <p>Current image:</p>
                    <img src="../<?php echo $post['featured_image']; ?>" alt="Current featured image" style="max-width: 200px; max-height: 150px; margin-top: 10px;">
                    <div>
                        <label>
                            <input type="checkbox" name="remove_image" value="1"> Remove image
                        </label>
                    </div>
                </div>
                <?php endif; ?>
                
                <small>Recommended size: 1200x800 pixels or similar ratio</small>
            </div>
            
            <div class="form-group col-md-6">
                <label for="author">Author</label>
                <input type="text" id="author" name="author" value="<?php echo htmlspecialchars($post['author']); ?>">
                
                <div class="form-row" style="margin-top: 15px;">
                    <div class="form-group col-md-6">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="draft" <?php if ($post['status'] === 'draft') echo 'selected'; ?>>Draft</option>
                            <option value="published" <?php if ($post['status'] === 'published') echo 'selected'; ?>>Published</option>
                        </select>
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="publish_date">Publish Date</label>
                        <input type="datetime-local" id="publish_date" name="publish_date" value="<?php echo date('Y-m-d\TH:i', strtotime($post['publish_date'])); ?>">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?php echo $post['id'] ? 'Update' : 'Add'; ?> Post</button>
            <a href="blog.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
    function generateSlug(text) {
        // If it's a new post or the slug field hasn't been manually edited
        if (document.getElementById('post_id').value == "0" || document.getElementById('slug').dataset.edited !== 'true') {
            const slug = text.toLowerCase()
                .replace(/[^\w\s-]/g, '') // Remove special characters
                .replace(/\s+/g, '-')     // Replace spaces with hyphens
                .replace(/-+/g, '-');     // Remove consecutive hyphens
            
            document.getElementById('slug').value = slug;
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        // Mark slug as edited if user changes it manually
        document.getElementById('slug').addEventListener('input', function() {
            this.dataset.edited = 'true';
        });
        
        // Simple HTML Editor
        const textarea = document.getElementById('content');
        const previewDiv = document.getElementById('preview-content');
        
        // Update preview when content changes
        function updatePreview() {
            previewDiv.innerHTML = textarea.value;
        }
        
        // Handle formatting actions
        function handleFormatting(action) {
            const selStart = textarea.selectionStart;
            const selEnd = textarea.selectionEnd;
            const selectedText = textarea.value.substring(selStart, selEnd);
            let replacement = '';
            
            switch(action) {
                case 'bold':
                    replacement = `<strong>${selectedText}</strong>`;
                    break;
                case 'italic':
                    replacement = `<em>${selectedText}</em>`;
                    break;
                case 'h2':
                    replacement = `<h2>${selectedText}</h2>`;
                    break;
                case 'h3':
                    replacement = `<h3>${selectedText}</h3>`;
                    break;
                case 'h4':
                    replacement = `<h4>${selectedText}</h4>`;
                    break;
                case 'link':
                    const url = prompt('Enter the URL:', 'https://');
                    if (url) {
                        replacement = `<a href="${url}">${selectedText || url}</a>`;
                    } else {
                        return;
                    }
                    break;
                case 'ul':
                    if (selectedText.includes('\n')) {
                        const lines = selectedText.split('\n');
                        replacement = '<ul>\n' + lines.map(line => `  <li>${line}</li>`).join('\n') + '\n</ul>';
                    } else {
                        replacement = `<ul>\n  <li>${selectedText}</li>\n</ul>`;
                    }
                    break;
                case 'ol':
                    if (selectedText.includes('\n')) {
                        const lines = selectedText.split('\n');
                        replacement = '<ol>\n' + lines.map(line => `  <li>${line}</li>`).join('\n') + '\n</ol>';
                    } else {
                        replacement = `<ol>\n  <li>${selectedText}</li>\n</ol>`;
                    }
                    break;
                case 'p':
                    replacement = `<p>${selectedText}</p>`;
                    break;
            }
            
            textarea.value = textarea.value.substring(0, selStart) + replacement + textarea.value.substring(selEnd);
            updatePreview();
            
            // Reset selection to after inserted text
            textarea.focus();
            const newCursorPos = selStart + replacement.length;
            textarea.setSelectionRange(newCursorPos, newCursorPos);
        }
        
        // Add event listeners for toolbar buttons
        document.querySelectorAll('.editor-toolbar button').forEach(button => {
            button.addEventListener('click', function() {
                handleFormatting(this.getAttribute('data-action'));
            });
        });
        
        // Tab switching
        document.querySelectorAll('.editor-tabs li').forEach(tab => {
            tab.addEventListener('click', function() {
                const tabName = this.getAttribute('data-tab');
                
                // Update active tab
                document.querySelectorAll('.editor-tabs li').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                // Show corresponding panel
                document.querySelectorAll('.editor-panel').forEach(panel => panel.classList.remove('active'));
                document.getElementById(`editor-${tabName}-panel`).classList.add('active');
                
                // Update preview when switching to preview tab
                if (tabName === 'preview') {
                    updatePreview();
                }
            });
        });
        
        // Initial preview update
        updatePreview();
        
        // Update preview as user types
        textarea.addEventListener('input', updatePreview);
    });
</script>

<style>
    #simple-editor-container {
        border: 1px solid #ddd;
        border-radius: 4px;
        overflow: hidden;
    }
    
    .editor-tabs {
        display: flex;
        list-style: none;
        margin: 0;
        padding: 0;
        background-color: #f5f5f5;
        border-bottom: 1px solid #ddd;
    }
    
    .editor-tabs li {
        padding: 8px 15px;
        cursor: pointer;
    }
    
    .editor-tabs li.active {
        background-color: #fff;
        border-bottom: 2px solid #007bff;
    }
    
    .editor-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        padding: 8px;
        background-color: #f8f9fa;
        border-bottom: 1px solid #ddd;
    }
    
    .editor-toolbar button {
        padding: 5px 10px;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 3px;
        cursor: pointer;
        font-size: 14px;
    }
    
    .editor-toolbar button:hover {
        background-color: #e9ecef;
    }
    
    .editor-panel {
        display: none;
    }
    
    .editor-panel.active {
        display: block;
    }
    
    #content {
        width: 100%;
        min-height: 400px;
        padding: 10px;
        border: none;
        resize: vertical;
        font-family: monospace;
        line-height: 1.5;
        box-sizing: border-box;
    }
    
    #preview-content {
        min-height: 400px;
        padding: 10px;
        border: none;
        overflow-y: auto;
        background-color: #fff;
    }
</style>

<?php include('includes/footer.php'); ?> 