<?php
$page_title = "Edit Guide";
include('includes/header.php');
include('includes/db_connection.php');

// Initialize variables
$id = '';
$title = '';
$slug = '';
$excerpt = '';
$content = '';
$category_id = '';
$featured_image = '';
$author = 'CANEXT Team';
$status = 'draft';
$publish_date = '';
$meta_title = '';
$meta_description = '';
$downloads = [];

// Check if we're editing an existing guide
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql = "SELECT * FROM guides WHERE id = $id";
    $result = executeQuery($sql);
    
    if ($result && $result->num_rows > 0) {
        $guide = $result->fetch_assoc();
        $title = $guide['title'];
        $slug = $guide['slug'];
        $excerpt = $guide['excerpt'];
        $content = $guide['content'];
        $category_id = $guide['category_id'];
        $featured_image = $guide['featured_image'];
        $author = $guide['author'];
        $status = $guide['status'];
        $publish_date = !empty($guide['publish_date']) ? date('Y-m-d\TH:i', strtotime($guide['publish_date'])) : '';
        $meta_title = $guide['meta_title'];
        $meta_description = $guide['meta_description'];
        
        // Get guide downloads
        $downloads_sql = "SELECT * FROM guide_downloads WHERE guide_id = $id";
        $downloads_result = executeQuery($downloads_sql);
        if ($downloads_result && $downloads_result->num_rows > 0) {
            while ($row = $downloads_result->fetch_assoc()) {
                $downloads[] = $row;
            }
        }
    } else {
        // Guide not found
        header('Location: guides.php');
        exit;
    }
}

// Get all categories
$categories_sql = "SELECT id, name FROM guide_categories ORDER BY display_order, name";
$categories = executeQuery($categories_sql);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $title = sanitize($_POST['title']);
    $slug = sanitize($_POST['slug']);
    $excerpt = sanitize($_POST['excerpt']);
    $content = $_POST['content']; // Don't sanitize content to allow HTML
    $category_id = (int)$_POST['category_id'];
    $featured_image = sanitize($_POST['featured_image']);
    $author = sanitize($_POST['author']);
    $status = sanitize($_POST['status']);
    $publish_date = sanitize($_POST['publish_date']);
    $meta_title = sanitize($_POST['meta_title']);
    $meta_description = sanitize($_POST['meta_description']);
    
    // Handle image upload
    if (isset($_FILES['featured_image_upload']) && $_FILES['featured_image_upload']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../images/resources/';
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Generate a unique filename
        $file_name = strtolower(str_replace(' ', '-', basename($_FILES['featured_image_upload']['name'])));
        $file_path = $upload_dir . $file_name;
        
        // Move the uploaded file
        if (move_uploaded_file($_FILES['featured_image_upload']['tmp_name'], $file_path)) {
            // Update featured_image path
            $featured_image = 'images/resources/' . $file_name;
        }
    }
    
    // Check if remove image checkbox is checked
    if (isset($_POST['remove_image']) && $_POST['remove_image'] == '1') {
        $featured_image = '';
    }
    
    // Generate slug if not provided
    if (empty($slug)) {
        $slug = strtolower(str_replace(' ', '-', $title));
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
    }
    
    // Format publish date for MySQL
    $formatted_publish_date = !empty($publish_date) ? date('Y-m-d H:i:s', strtotime($publish_date)) : 'NULL';
    $date_clause = !empty($publish_date) ? "'$formatted_publish_date'" : "NULL";
    
    // Validate inputs
    $errors = [];
    if (empty($title)) {
        $errors[] = "Guide title is required.";
    }
    if (empty($content)) {
        $errors[] = "Guide content is required.";
    }
    if (empty($category_id)) {
        $errors[] = "Please select a category.";
    }
    
    if (empty($errors)) {
        if (!empty($id)) {
            // Update existing guide
            $sql = "UPDATE guides SET 
                    title = '$title', 
                    slug = '$slug', 
                    excerpt = '$excerpt', 
                    content = '$content', 
                    category_id = $category_id, 
                    featured_image = '$featured_image', 
                    author = '$author', 
                    status = '$status', 
                    publish_date = $date_clause, 
                    meta_title = '$meta_title', 
                    meta_description = '$meta_description', 
                    updated_at = NOW() 
                    WHERE id = $id";
            
            if (executeQuery($sql)) {
                // Update guide count in category
                $update_count_sql = "UPDATE guide_categories SET guide_count = (SELECT COUNT(*) FROM guides WHERE category_id = $category_id) WHERE id = $category_id";
                executeQuery($update_count_sql);
                
                $success_message = "Guide updated successfully.";
                
                // Handle downloads - we'll handle this in a separate feature
                
            } else {
                $error_message = "Error updating guide. Please try again.";
            }
        } else {
            // Check if slug already exists
            $check_sql = "SELECT id FROM guides WHERE slug = '$slug'";
            $check_result = executeQuery($check_sql);
            
            if ($check_result && $check_result->num_rows > 0) {
                $errors[] = "A guide with this slug already exists. Please choose a different title or provide a unique slug.";
            } else {
                // Insert new guide
                $sql = "INSERT INTO guides (
                        title, slug, excerpt, content, category_id, featured_image, 
                        author, status, publish_date, meta_title, meta_description
                    ) VALUES (
                        '$title', '$slug', '$excerpt', '$content', $category_id, '$featured_image', 
                        '$author', '$status', $date_clause, '$meta_title', '$meta_description'
                    )";
                
                if (executeQuery($sql)) {
                    $new_guide_id = $conn->insert_id;
                    
                    // Update guide count in category
                    $update_count_sql = "UPDATE guide_categories SET guide_count = (SELECT COUNT(*) FROM guides WHERE category_id = $category_id) WHERE id = $category_id";
                    executeQuery($update_count_sql);
                    
                    $success_message = "Guide added successfully.";
                    
                    // Redirect to edit the new guide (for adding downloads or continue editing)
                    header("Location: guide_edit.php?id=$new_guide_id&message=added");
                    exit;
                } else {
                    $error_message = "Error adding guide. Please try again.";
                }
            }
        }
    }
}

// Check for message parameter
if (isset($_GET['message']) && $_GET['message'] === 'added') {
    $success_message = "Guide added successfully. You can now continue editing or add downloads.";
}
?>

<!-- Page Title -->
<div class="admin-page-title">
    <h1><i class="fas fa-book"></i> <?php echo empty($id) ? 'Add New' : 'Edit'; ?> Guide</h1>
    <div class="admin-page-actions">
        <a href="guides.php" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Guides
        </a>
        <?php if (!empty($id) && $status === 'published'): ?>
        <a href="../resources/guide-details.php?slug=<?php echo $slug; ?>" target="_blank" class="btn btn-info btn-sm">
            <i class="fas fa-eye"></i> View Guide
        </a>
        <?php endif; ?>
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

<!-- Guide Form -->
<div class="admin-card">
    <div class="admin-card-header">
        <h2><?php echo empty($id) ? 'Add New' : 'Edit'; ?> Guide</h2>
    </div>
    <div class="admin-card-body">
        <form method="post" action="" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <!-- Main Content -->
                    <div class="form-group">
                        <label for="title">Guide Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo $title; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="slug">Slug</label>
                        <input type="text" class="form-control" id="slug" name="slug" value="<?php echo $slug; ?>" placeholder="Leave blank to auto-generate">
                        <small class="form-text text-muted">The "slug" is the URL-friendly version of the title. It is usually lowercase and contains only letters, numbers, and hyphens.</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="excerpt">Excerpt</label>
                        <textarea class="form-control" id="excerpt" name="excerpt" rows="3"><?php echo $excerpt; ?></textarea>
                        <small class="form-text text-muted">A short summary of the guide content (displayed in listings).</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="content">Content <span class="text-danger">*</span></label>
                        <div id="simple-editor-container">
                            <ul class="editor-tabs">
                                <li class="active" data-tab="edit">Edit</li>
                                <li data-tab="preview">Preview</li>
                            </ul>
                            <div class="editor-toolbar">
                                <button type="button" data-action="bold" title="Bold"><i class="fas fa-bold"></i></button>
                                <button type="button" data-action="italic" title="Italic"><i class="fas fa-italic"></i></button>
                                <button type="button" data-action="heading" data-level="2" title="Heading 2"><i class="fas fa-heading"></i>2</button>
                                <button type="button" data-action="heading" data-level="3" title="Heading 3"><i class="fas fa-heading"></i>3</button>
                                <button type="button" data-action="heading" data-level="4" title="Heading 4"><i class="fas fa-heading"></i>4</button>
                                <button type="button" data-action="link" title="Insert Link"><i class="fas fa-link"></i></button>
                                <button type="button" data-action="unordered-list" title="Bulleted List"><i class="fas fa-list-ul"></i></button>
                                <button type="button" data-action="ordered-list" title="Numbered List"><i class="fas fa-list-ol"></i></button>
                                <button type="button" data-action="image" title="Insert Image"><i class="fas fa-image"></i></button>
                                <button type="button" data-action="paragraph" title="Paragraph"><i class="fas fa-paragraph"></i></button>
                            </div>
                            <div id="editor-edit-panel" class="editor-panel active">
                                <textarea class="form-control" id="content" name="content" rows="15" required><?php echo $content; ?></textarea>
                            </div>
                            <div id="editor-preview-panel" class="editor-panel">
                                <div id="content-preview"></div>
                            </div>
                        </div>
                        <small class="form-text text-muted">The main content of the guide. HTML is allowed.</small>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <!-- Sidebar Options -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0">Publishing Options</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="draft" <?php echo $status === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                    <option value="published" <?php echo $status === 'published' ? 'selected' : ''; ?>>Published</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="publish_date">Publish Date/Time</label>
                                <input type="datetime-local" class="form-control" id="publish_date" name="publish_date" value="<?php echo $publish_date; ?>">
                                <small class="form-text text-muted">Leave blank for drafts.</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="author">Author</label>
                                <input type="text" class="form-control" id="author" name="author" value="<?php echo $author; ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0">Category</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="category_id">Category <span class="text-danger">*</span></label>
                                <select class="form-control" id="category_id" name="category_id" required>
                                    <option value="">Select a category</option>
                                    <?php if ($categories && $categories->num_rows > 0): ?>
                                        <?php while ($category = $categories->fetch_assoc()): ?>
                                        <option value="<?php echo $category['id']; ?>" <?php echo $category_id == $category['id'] ? 'selected' : ''; ?>>
                                            <?php echo $category['name']; ?>
                                        </option>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0">Featured Image</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="featured_image_upload">Upload Image</label>
                                <input type="file" class="form-control-file" id="featured_image_upload" name="featured_image_upload" accept="image/*">
                                <small class="form-text text-muted">Recommended size: 1200x800 pixels or similar ratio</small>
                            </div>
                            
                            <div class="form-group mt-3">
                                <label for="featured_image">Image Path</label>
                                <input type="text" class="form-control" id="featured_image" name="featured_image" value="<?php echo $featured_image; ?>" placeholder="e.g., images/resources/guide1.jpg">
                                <small class="form-text text-muted">Path will be automatically updated when you upload an image.</small>
                            </div>
                            
                            <?php if (!empty($featured_image)): ?>
                            <div class="featured-image-preview mt-3">
                                <p>Current image:</p>
                                <img src="../<?php echo $featured_image; ?>" alt="Featured Image" class="img-thumbnail" style="max-width: 100%;">
                                <div class="mt-2">
                                    <label>
                                        <input type="checkbox" name="remove_image" value="1"> Remove image
                                    </label>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0">SEO Settings</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="meta_title">Meta Title</label>
                                <input type="text" class="form-control" id="meta_title" name="meta_title" value="<?php echo $meta_title; ?>">
                                <small class="form-text text-muted">Leave blank to use the guide title.</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="meta_description">Meta Description</label>
                                <textarea class="form-control" id="meta_description" name="meta_description" rows="3"><?php echo $meta_description; ?></textarea>
                                <small class="form-text text-muted">A brief description for search engines. Leave blank to use the excerpt.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo empty($id) ? 'Add Guide' : 'Update Guide'; ?>
                </button>
                <a href="guides.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php if (!empty($id)): ?>
<!-- Downloads Section -->
<div class="admin-card mt-4">
    <div class="admin-card-header">
        <h2>Guide Downloads</h2>
        <button class="btn btn-sm btn-primary" id="add-download-btn">
            <i class="fas fa-plus"></i> Add Download
        </button>
    </div>
    <div class="admin-card-body">
        <?php if (!empty($downloads)): ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>File Path</th>
                        <th>Downloads</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($downloads as $download): ?>
                    <tr>
                        <td><?php echo $download['title']; ?></td>
                        <td><?php echo $download['file_path']; ?></td>
                        <td><?php echo $download['download_count']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary edit-download" data-id="<?php echo $download['id']; ?>">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <a href="guide_download_delete.php?id=<?php echo $download['id']; ?>&guide_id=<?php echo $id; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this download?');">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p>No downloads found for this guide. Add a download file using the button above.</p>
        <?php endif; ?>
        
        <div class="mt-3">
            <small class="text-muted">Note: Manage downloadable resources for this guide here. These will appear in the guide's sidebar.</small>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate slug from title
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');
    
    titleInput.addEventListener('blur', function() {
        if (slugInput.value === '') {
            const slug = this.value.toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-');
            slugInput.value = slug;
        }
    });
    
    // For downloads management
    const addDownloadBtn = document.getElementById('add-download-btn');
    if (addDownloadBtn) {
        addDownloadBtn.addEventListener('click', function() {
            window.location.href = 'guide_download_edit.php?guide_id=<?php echo $id; ?>';
        });
    }
    
    // Preview image when URL is entered
    const featuredImageInput = document.getElementById('featured_image');
    const featuredImageUpload = document.getElementById('featured_image_upload');
    
    if (featuredImageInput) {
        featuredImageInput.addEventListener('blur', function() {
            updateImagePreview(this.value);
        });
    }
    
    // Preview uploaded image
    if (featuredImageUpload) {
        featuredImageUpload.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Create/update preview
                    let previewContainer = document.querySelector('.featured-image-preview');
                    if (!previewContainer) {
                        previewContainer = document.createElement('div');
                        previewContainer.className = 'featured-image-preview mt-3';
                        document.querySelector('.card-body').appendChild(previewContainer);
                        
                        const title = document.createElement('p');
                        title.textContent = 'Image preview:';
                        previewContainer.appendChild(title);
                    }
                    
                    let img = previewContainer.querySelector('img');
                    if (!img) {
                        img = document.createElement('img');
                        img.className = 'img-thumbnail';
                        img.style.maxWidth = '100%';
                        previewContainer.appendChild(img);
                    }
                    
                    img.src = e.target.result;
                    img.alt = 'Featured Image Preview';
                    
                    // Auto-generate a tentative path (will be replaced by the actual server-side upload)
                    const fileName = file.name.toLowerCase().replace(/\s+/g, '-');
                    featuredImageInput.value = 'images/resources/' + fileName;
                }
                reader.readAsDataURL(file);
            }
        });
    }
    
    function updateImagePreview(imagePath) {
        if (!imagePath) return;
        
        const previewContainer = document.querySelector('.featured-image-preview');
        if (!previewContainer) return;
        
        const img = previewContainer.querySelector('img') || document.createElement('img');
        img.src = '../' + imagePath;
        img.alt = 'Featured Image';
        img.className = 'img-thumbnail';
        img.style.maxWidth = '100%';
        
        if (!previewContainer.contains(img)) {
            previewContainer.appendChild(img);
        }
        
        previewContainer.style.display = 'block';
    }
    
    // Simple HTML Editor
    const contentTextarea = document.getElementById('content');
    const contentPreview = document.getElementById('content-preview');
    const editorContainer = document.getElementById('simple-editor-container');
    
    // Update preview function
    function updatePreview() {
        contentPreview.innerHTML = contentTextarea.value;
    }
    
    // Initialize preview
    updatePreview();
    
    // Update preview as user types
    contentTextarea.addEventListener('input', updatePreview);
    
    // Tab switching
    const tabs = document.querySelectorAll('.editor-tabs li');
    const panels = document.querySelectorAll('.editor-panel');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            
            // Remove active class from all tabs and panels
            tabs.forEach(t => t.classList.remove('active'));
            panels.forEach(p => p.classList.remove('active'));
            
            // Add active class to current tab and panel
            this.classList.add('active');
            document.getElementById(`editor-${tabName}-panel`).classList.add('active');
            
            // Update preview when switching to preview tab
            if (tabName === 'preview') {
                updatePreview();
            }
        });
    });
    
    // Editor toolbar actions
    const toolbarButtons = document.querySelectorAll('.editor-toolbar button');
    
    toolbarButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const action = this.getAttribute('data-action');
            const textarea = document.getElementById('content');
            const selStart = textarea.selectionStart;
            const selEnd = textarea.selectionEnd;
            const selectedText = textarea.value.substring(selStart, selEnd);
            
            let result = '';
            
            switch(action) {
                case 'bold':
                    result = `<strong>${selectedText}</strong>`;
                    break;
                case 'italic':
                    result = `<em>${selectedText}</em>`;
                    break;
                case 'heading':
                    const level = this.getAttribute('data-level');
                    result = `<h${level}>${selectedText}</h${level}>`;
                    break;
                case 'link':
                    const url = prompt('Enter URL:', 'https://');
                    if (url) {
                        result = `<a href="${url}">${selectedText || url}</a>`;
                    }
                    break;
                case 'unordered-list':
                    if (selectedText) {
                        const items = selectedText.split('\n').map(item => `  <li>${item}</li>`).join('\n');
                        result = `<ul>\n${items}\n</ul>`;
                    } else {
                        result = `<ul>\n  <li></li>\n</ul>`;
                    }
                    break;
                case 'ordered-list':
                    if (selectedText) {
                        const items = selectedText.split('\n').map(item => `  <li>${item}</li>`).join('\n');
                        result = `<ol>\n${items}\n</ol>`;
                    } else {
                        result = `<ol>\n  <li></li>\n</ol>`;
                    }
                    break;
                case 'image':
                    const imgUrl = prompt('Enter image URL:', '');
                    if (imgUrl) {
                        const altText = prompt('Enter alt text:', '');
                        result = `<img src="${imgUrl}" alt="${altText}" />`;
                    }
                    break;
                case 'paragraph':
                    result = `<p>${selectedText}</p>`;
                    break;
            }
            
            if (result) {
                textarea.focus();
                document.execCommand('insertText', false, result);
                
                // For browsers that don't support execCommand('insertText')
                if (textarea.value.substring(selStart, selStart + result.length) !== result) {
                    const before = textarea.value.substring(0, selStart);
                    const after = textarea.value.substring(selEnd);
                    textarea.value = before + result + after;
                }
                
                updatePreview();
            }
        });
    });
});
</script>

<style>
/* Simple Editor Styles */
#simple-editor-container {
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-bottom: 15px;
}

.editor-tabs {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
    background-color: #f5f5f5;
    border-bottom: 1px solid #ddd;
}

.editor-tabs li {
    padding: 10px 15px;
    cursor: pointer;
}

.editor-tabs li.active {
    background-color: #fff;
    border-bottom: 2px solid #007bff;
}

.editor-toolbar {
    padding: 10px;
    background-color: #f9f9f9;
    border-bottom: 1px solid #ddd;
    display: flex;
    flex-wrap: wrap;
}

.editor-toolbar button {
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 3px;
    margin-right: 5px;
    margin-bottom: 5px;
    padding: 5px 10px;
    cursor: pointer;
    font-size: 0.9rem;
}

.editor-toolbar button:hover {
    background-color: #f0f0f0;
}

.editor-panel {
    display: none;
    padding: 15px;
}

.editor-panel.active {
    display: block;
}

#content {
    min-height: 300px;
    width: 100%;
    border: none;
    padding: 0;
    resize: vertical;
}

#content-preview {
    min-height: 300px;
    overflow-y: auto;
    word-break: break-word;
    border: 1px dashed #ddd;
    padding: 10px;
    background-color: #fff;
}
</style>

<?php include('includes/footer.php'); ?> 