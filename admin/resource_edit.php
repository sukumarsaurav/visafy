<?php
ob_start(); // Start output buffering to prevent "headers already sent" errors
$page_title = "Edit Downloadable Resource";
include('includes/header.php');
include('includes/db_connection.php');

// Initialize variables
$id = '';
$title = '';
$description = '';
$file_path = '';
$file_type = 'pdf';
$file_size = '';
$icon = 'fas fa-file-pdf';
$category_id = '';
$display_order = 0;
$status = 'draft';

// Check if we're editing an existing resource
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql = "SELECT * FROM downloadable_resources WHERE id = $id";
    $result = executeQuery($sql);
    
    if ($result && $result->num_rows > 0) {
        $resource = $result->fetch_assoc();
        $title = $resource['title'];
        $description = $resource['description'];
        $file_path = $resource['file_path'];
        $file_type = $resource['file_type'];
        $file_size = $resource['file_size'];
        $icon = $resource['icon'];
        $category_id = $resource['category_id'];
        $display_order = $resource['display_order'];
        $status = $resource['status'];
    } else {
        // Resource not found
        header('Location: downloadable_resources.php');
        exit;
    }
}

// Get all guide categories
$sql = "SELECT id, name FROM guide_categories ORDER BY display_order, name";
$categories = executeQuery($sql);

// Define file icons
$file_icons = [
    'pdf' => 'fas fa-file-pdf',
    'doc' => 'fas fa-file-word',
    'docx' => 'fas fa-file-word',
    'xls' => 'fas fa-file-excel',
    'xlsx' => 'fas fa-file-excel',
    'ppt' => 'fas fa-file-powerpoint',
    'pptx' => 'fas fa-file-powerpoint',
    'zip' => 'fas fa-file-archive',
    'other' => 'fas fa-file-alt'
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : 'NULL';
    $icon = sanitize($_POST['icon']);
    $display_order = (int)$_POST['display_order'];
    $status = sanitize($_POST['status']);
    
    // Validate inputs
    $errors = [];
    if (empty($title)) {
        $errors[] = "Title is required.";
    }
    
    // Handle file upload
    $new_file_path = '';
    $new_file_type = '';
    $new_file_size = 0;
    
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $filename = $_FILES['file']['name'];
        $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $new_file_size = $_FILES['file']['size'];
        
        // Determine file type
        if (in_array($file_ext, ['pdf'])) {
            $new_file_type = 'pdf';
        } elseif (in_array($file_ext, ['doc', 'docx'])) {
            $new_file_type = $file_ext;
        } elseif (in_array($file_ext, ['xls', 'xlsx'])) {
            $new_file_type = $file_ext;
        } elseif (in_array($file_ext, ['ppt', 'pptx'])) {
            $new_file_type = $file_ext;
        } elseif (in_array($file_ext, ['zip', 'rar'])) {
            $new_file_type = 'zip';
        } else {
            $new_file_type = 'other';
        }
        
        // Create upload directory if it doesn't exist
        $upload_dir = '../files/resources/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $new_filename = 'resource_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
        $destination = $upload_dir . $new_filename;
        
        if (move_uploaded_file($_FILES['file']['tmp_name'], $destination)) {
            $new_file_path = 'files/resources/' . $new_filename;
            
            // Delete old file if exists
            if (!empty($file_path) && file_exists('../' . $file_path) && $file_path != $new_file_path) {
                unlink('../' . $file_path);
            }
        } else {
            $errors[] = "Failed to upload file.";
        }
    } elseif (empty($file_path) && empty($id)) {
        $errors[] = "Please upload a file.";
    }
    
    if (empty($errors)) {
        // Set new values or keep old ones
        $file_path_sql = !empty($new_file_path) ? "'$new_file_path'" : "'$file_path'";
        $file_type_sql = !empty($new_file_type) ? "'$new_file_type'" : "'$file_type'";
        $file_size_sql = !empty($new_file_size) ? $new_file_size : ($file_size ? $file_size : 'NULL');
        
        if (!empty($id)) {
            // Update existing resource
            $sql = "UPDATE downloadable_resources SET 
                    title = '$title', 
                    description = " . (!empty($description) ? "'$description'" : "NULL") . ", 
                    file_path = $file_path_sql, 
                    file_type = $file_type_sql, 
                    file_size = $file_size_sql, 
                    icon = '$icon', 
                    category_id = $category_id, 
                    display_order = $display_order, 
                    status = '$status',
                    updated_at = NOW()
                    WHERE id = $id";
            
            if (executeQuery($sql)) {
                header('Location: downloadable_resources.php?success=updated');
                exit;
            } else {
                $errors[] = "Error updating resource. Please try again.";
            }
        } else {
            // Insert new resource
            $sql = "INSERT INTO downloadable_resources 
                    (title, description, file_path, file_type, file_size, icon, category_id, display_order, status, created_at, updated_at) 
                    VALUES 
                    ('$title', " . (!empty($description) ? "'$description'" : "NULL") . ", $file_path_sql, $file_type_sql, $file_size_sql, '$icon', $category_id, $display_order, '$status', NOW(), NOW())";
            
            if (executeQuery($sql)) {
                header('Location: downloadable_resources.php?success=added');
                exit;
            } else {
                $errors[] = "Error adding resource. Please try again.";
            }
        }
    }
}

// Function to format file size
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}
?>

<div class="admin-content-header">
    <h1><?php echo empty($id) ? 'Add New Downloadable Resource' : 'Edit Downloadable Resource'; ?></h1>
    <div class="header-actions">
        <a href="downloadable_resources.php" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Resources
        </a>
    </div>
</div>

<?php if (isset($errors) && !empty($errors)): ?>
<div class="alert alert-danger">
    <ul>
        <?php foreach ($errors as $error): ?>
        <li><?php echo $error; ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2><i class="fas fa-file-download"></i> Resource Information</h2>
    </div>
    <div class="admin-card-body">
        <form method="post" action="" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label for="title">Title <span class="required">*</span></label>
                    <input type="text" id="title" name="title" value="<?php echo $title; ?>" required>
                </div>
                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id">
                        <option value="">-- Select Category --</option>
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
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3"><?php echo $description; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="file">File <?php echo empty($id) ? '<span class="required">*</span>' : ''; ?></label>
                <input type="file" id="file" name="file" <?php echo empty($id) ? 'required' : ''; ?>>
                <?php if (!empty($file_path)): ?>
                <div class="current-file" style="margin-top: 10px; display: flex; align-items: center; gap: 10px;">
                    <i class="<?php echo $icon; ?>" style="font-size: 24px;"></i>
                    <div>
                        <p><strong>Current file:</strong> <?php echo basename($file_path); ?></p>
                        <p><small>Size: <?php echo formatFileSize($file_size); ?></small></p>
                        <p class="form-text text-muted">Upload a new file to replace it.</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="icon">Icon</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="<?php echo $icon; ?>"></i></span>
                        </div>
                        <select id="icon" name="icon">
                            <?php foreach ($file_icons as $type => $icon_class): ?>
                            <option value="<?php echo $icon_class; ?>" <?php echo $icon === $icon_class ? 'selected' : ''; ?>>
                                <?php echo ucfirst($type); ?> (<?php echo $icon_class; ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="display_order">Display Order</label>
                    <input type="number" id="display_order" name="display_order" value="<?php echo $display_order; ?>" min="0">
                    <small class="form-text text-muted">Resources with lower numbers will be displayed first.</small>
                </div>
            </div>
            
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="draft" <?php echo $status === 'draft' ? 'selected' : ''; ?>>Draft</option>
                    <option value="published" <?php echo $status === 'published' ? 'selected' : ''; ?>>Published</option>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> <?php echo empty($id) ? 'Add Resource' : 'Update Resource'; ?>
                </button>
                <a href="downloadable_resources.php" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<style>
.required {
    color: #dc3545;
}

.form-row {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    flex: 1;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-family: inherit;
    font-size: inherit;
}

.input-group {
    display: flex;
}

.input-group-prepend {
    display: flex;
    align-items: center;
    padding: 0 12px;
    background-color: #f8f9fa;
    border: 1px solid #ddd;
    border-right: none;
    border-radius: 4px 0 0 4px;
}

.input-group select {
    border-radius: 0 4px 4px 0;
}

.form-actions {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #eee;
    display: flex;
    gap: 10px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update icon preview when selection changes
    const iconSelect = document.getElementById('icon');
    const iconPreview = document.querySelector('.input-group-prepend i');
    
    iconSelect.addEventListener('change', function() {
        iconPreview.className = this.value;
    });
});
</script>

<?php include('includes/footer.php'); ?> 