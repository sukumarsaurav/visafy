<?php
ob_start(); // Start output buffering to prevent "headers already sent" errors
$page_title = "Edit Video Tutorial";
include('includes/header.php');
include('includes/db_connection.php');

// Initialize variables
$id = '';
$title = '';
$description = '';
$video_url = '';
$thumbnail = '';
$category_id = '';
$duration = '';
$display_order = 0;
$status = 'draft';

// Check if we're editing an existing tutorial
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql = "SELECT * FROM video_tutorials WHERE id = $id";
    $result = executeQuery($sql);
    
    if ($result && $result->num_rows > 0) {
        $tutorial = $result->fetch_assoc();
        $title = $tutorial['title'];
        $description = $tutorial['description'];
        $video_url = $tutorial['video_url'];
        $thumbnail = $tutorial['thumbnail'];
        $category_id = $tutorial['category_id'];
        $duration = $tutorial['duration'];
        $display_order = $tutorial['display_order'];
        $status = $tutorial['status'];
    } else {
        // Tutorial not found
        header('Location: video_tutorials.php');
        exit;
    }
}

// Get all guide categories
$sql = "SELECT id, name FROM guide_categories ORDER BY display_order, name";
$categories = executeQuery($sql);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $video_url = sanitize($_POST['video_url']);
    $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : 'NULL';
    $duration = sanitize($_POST['duration']);
    $display_order = (int)$_POST['display_order'];
    $status = sanitize($_POST['status']);
    
    // Validate inputs
    $errors = [];
    if (empty($title)) {
        $errors[] = "Title is required.";
    }
    if (empty($video_url)) {
        $errors[] = "Video URL is required.";
    }
    
    // Handle thumbnail upload
    $new_thumbnail = '';
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['thumbnail']['name'];
        $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (!in_array($file_ext, $allowed)) {
            $errors[] = "Thumbnail must be a JPG, PNG or GIF file.";
        } else {
            $upload_dir = '../images/resources/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $new_filename = 'video_thumbnail_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
            $destination = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $destination)) {
                $new_thumbnail = 'images/resources/' . $new_filename;
                
                // Delete old thumbnail if exists
                if (!empty($thumbnail) && file_exists('../' . $thumbnail) && $thumbnail != $new_thumbnail) {
                    unlink('../' . $thumbnail);
                }
            } else {
                $errors[] = "Failed to upload thumbnail.";
            }
        }
    }
    
    if (empty($errors)) {
        // If thumbnail wasn't updated, keep the old one
        if (empty($new_thumbnail)) {
            $thumbnail_sql = !empty($thumbnail) ? "'$thumbnail'" : "NULL";
        } else {
            $thumbnail_sql = "'$new_thumbnail'";
        }
        
        if (!empty($id)) {
            // Update existing tutorial
            $sql = "UPDATE video_tutorials SET 
                    title = '$title', 
                    description = " . (!empty($description) ? "'$description'" : "NULL") . ", 
                    video_url = '$video_url', 
                    thumbnail = $thumbnail_sql, 
                    category_id = $category_id, 
                    duration = " . (!empty($duration) ? "'$duration'" : "NULL") . ", 
                    display_order = $display_order, 
                    status = '$status',
                    updated_at = NOW()
                    WHERE id = $id";
            
            if (executeQuery($sql)) {
                header('Location: video_tutorials.php?success=updated');
                exit;
            } else {
                $errors[] = "Error updating tutorial. Please try again.";
            }
        } else {
            // Insert new tutorial
            $sql = "INSERT INTO video_tutorials 
                    (title, description, video_url, thumbnail, category_id, duration, display_order, status, created_at, updated_at) 
                    VALUES 
                    ('$title', " . (!empty($description) ? "'$description'" : "NULL") . ", '$video_url', $thumbnail_sql, $category_id, " . (!empty($duration) ? "'$duration'" : "NULL") . ", $display_order, '$status', NOW(), NOW())";
            
            if (executeQuery($sql)) {
                header('Location: video_tutorials.php?success=added');
                exit;
            } else {
                $errors[] = "Error adding tutorial. Please try again.";
            }
        }
    }
}
?>

<div class="admin-content-header">
    <h1><?php echo empty($id) ? 'Add New Video Tutorial' : 'Edit Video Tutorial'; ?></h1>
    <div class="header-actions">
        <a href="video_tutorials.php" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Tutorials
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
        <h2><i class="fas fa-video"></i> Tutorial Information</h2>
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
            
            <div class="form-row">
                <div class="form-group">
                    <label for="video_url">Video URL <span class="required">*</span></label>
                    <input type="text" id="video_url" name="video_url" value="<?php echo $video_url; ?>" required>
                    <small class="form-text text-muted">YouTube or Vimeo embed URL (e.g., https://www.youtube.com/embed/VIDEO_ID)</small>
                </div>
                <div class="form-group">
                    <label for="duration">Duration</label>
                    <input type="text" id="duration" name="duration" value="<?php echo $duration; ?>" placeholder="e.g., 12:45">
                </div>
            </div>
            
            <div class="form-group">
                <label for="thumbnail">Thumbnail</label>
                <input type="file" id="thumbnail" name="thumbnail" accept="image/*">
                <?php if (!empty($thumbnail)): ?>
                <div class="current-thumbnail" style="margin-top: 10px;">
                    <img src="../<?php echo $thumbnail; ?>" alt="Current thumbnail" style="max-width: 200px; max-height: 120px; border-radius: 4px;">
                    <p class="form-text text-muted">Current thumbnail. Upload a new one to replace it.</p>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="display_order">Display Order</label>
                    <input type="number" id="display_order" name="display_order" value="<?php echo $display_order; ?>" min="0">
                    <small class="form-text text-muted">Tutorials with lower numbers will be displayed first.</small>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="draft" <?php echo $status === 'draft' ? 'selected' : ''; ?>>Draft</option>
                        <option value="published" <?php echo $status === 'published' ? 'selected' : ''; ?>>Published</option>
                    </select>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> <?php echo empty($id) ? 'Add Tutorial' : 'Update Tutorial'; ?>
                </button>
                <a href="video_tutorials.php" class="btn-secondary">Cancel</a>
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

.form-actions {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #eee;
    display: flex;
    gap: 10px;
}
</style>

<?php include('includes/footer.php'); ?> 