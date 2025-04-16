<?php
$page_title = "Downloadable Resources Manager";
include('includes/header.php');
include('includes/db_connection.php');

// Handle resource deletion
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Get resource details to delete file if exists
    $sql = "SELECT file_path FROM downloadable_resources WHERE id = $id";
    $result = executeQuery($sql);
    if ($result && $result->num_rows > 0) {
        $resource = $result->fetch_assoc();
        if (!empty($resource['file_path']) && file_exists('../' . $resource['file_path'])) {
            unlink('../' . $resource['file_path']);
        }
    }
    
    // Delete the resource
    $sql = "DELETE FROM downloadable_resources WHERE id = $id";
    if (executeQuery($sql)) {
        header('Location: downloadable_resources.php?success=deleted');
        exit;
    } else {
        $error = "Failed to delete resource.";
    }
}

// Get all downloadable resources
$sql = "SELECT dr.*, gc.name as category_name 
        FROM downloadable_resources dr 
        LEFT JOIN guide_categories gc ON dr.category_id = gc.id 
        ORDER BY dr.status DESC, dr.display_order, dr.created_at DESC";
$resources = executeQuery($sql);

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

// Format date function
function formatDate($date) {
    return date('M j, Y', strtotime($date));
}
?>

<div class="admin-content-header">
    <h1>Downloadable Resources Manager</h1>
    <p>Manage downloadable resources for immigration assistance</p>
</div>

<?php if (isset($_GET['success'])): ?>
<div class="alert alert-success">
    <?php 
    switch ($_GET['success']) {
        case 'added':
            echo "Resource added successfully.";
            break;
        case 'updated':
            echo "Resource updated successfully.";
            break;
        case 'deleted':
            echo "Resource deleted successfully.";
            break;
        default:
            echo "Operation completed successfully.";
    }
    ?>
</div>
<?php endif; ?>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="admin-controls">
    <a href="resource_edit.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Resource
    </a>
</div>

<div class="admin-card">
    <div class="admin-card-header">
        <h2>All Downloadable Resources</h2>
    </div>
    <div class="admin-card-body">
        <?php if ($resources && $resources->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>File Type</th>
                        <th>Size</th>
                        <th>Category</th>
                        <th>Downloads</th>
                        <th>Status</th>
                        <th>Order</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($resource = $resources->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $resource['id']; ?></td>
                        <td><?php echo $resource['title']; ?></td>
                        <td>
                            <span style="display: inline-flex; align-items: center; gap: 5px;">
                                <i class="<?php echo $resource['icon']; ?>"></i>
                                <?php echo strtoupper($resource['file_type']); ?>
                            </span>
                        </td>
                        <td><?php echo !empty($resource['file_size']) ? formatFileSize($resource['file_size']) : '-'; ?></td>
                        <td><?php echo !empty($resource['category_name']) ? $resource['category_name'] : 'Uncategorized'; ?></td>
                        <td><?php echo $resource['download_count']; ?></td>
                        <td>
                            <span class="status-badge <?php echo $resource['status'] === 'published' ? 'status-published' : 'status-draft'; ?>">
                                <?php echo ucfirst($resource['status']); ?>
                            </span>
                        </td>
                        <td><?php echo $resource['display_order']; ?></td>
                        <td class="actions-cell">
                            <div class="action-buttons">
                                <a href="../<?php echo $resource['file_path']; ?>" class="action-btn view-btn" target="_blank" title="Download"><i class="fas fa-download"></i></a>
                                <a href="resource_edit.php?id=<?php echo $resource['id']; ?>" class="action-btn edit-btn" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="downloadable_resources.php?delete=<?php echo $resource['id']; ?>" class="action-btn delete-btn" title="Delete" onclick="return confirm('Are you sure you want to delete this resource?');"><i class="fas fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <p>No downloadable resources found. <a href="resource_edit.php">Add your first resource</a>.</p>
        </div>
        <?php endif; ?>
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

<?php include('includes/footer.php'); ?> 