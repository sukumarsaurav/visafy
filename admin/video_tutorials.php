<?php
$page_title = "Video Tutorials Manager";
include('includes/header.php');
include('includes/db_connection.php');

// Handle video tutorial deletion
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Get tutorial details to delete thumbnail if exists
    $sql = "SELECT thumbnail FROM video_tutorials WHERE id = $id";
    $result = executeQuery($sql);
    if ($result && $result->num_rows > 0) {
        $video = $result->fetch_assoc();
        if (!empty($video['thumbnail']) && file_exists('../' . $video['thumbnail'])) {
            unlink('../' . $video['thumbnail']);
        }
    }
    
    // Delete the tutorial
    $sql = "DELETE FROM video_tutorials WHERE id = $id";
    if (executeQuery($sql)) {
        header('Location: video_tutorials.php?success=deleted');
        exit;
    } else {
        $error = "Failed to delete tutorial.";
    }
}

// Get all video tutorials
$sql = "SELECT vt.*, gc.name as category_name 
        FROM video_tutorials vt 
        LEFT JOIN guide_categories gc ON vt.category_id = gc.id 
        ORDER BY vt.status DESC, vt.display_order, vt.created_at DESC";
$tutorials = executeQuery($sql);

// Format date function
function formatDate($date) {
    return date('M j, Y', strtotime($date));
}
?>

<div class="admin-content-header">
    <h1>Video Tutorials Manager</h1>
    <p>Manage video tutorials for immigration guidance</p>
</div>

<?php if (isset($_GET['success'])): ?>
<div class="alert alert-success">
    <?php 
    switch ($_GET['success']) {
        case 'added':
            echo "Video tutorial added successfully.";
            break;
        case 'updated':
            echo "Video tutorial updated successfully.";
            break;
        case 'deleted':
            echo "Video tutorial deleted successfully.";
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
    <a href="video_tutorial_edit.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Tutorial
    </a>
</div>

<div class="admin-card">
    <div class="admin-card-header">
        <h2>All Video Tutorials</h2>
    </div>
    <div class="admin-card-body">
        <?php if ($tutorials && $tutorials->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Thumbnail</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Order</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($tutorial = $tutorials->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $tutorial['id']; ?></td>
                        <td>
                            <?php if (!empty($tutorial['thumbnail'])): ?>
                            <img src="../<?php echo $tutorial['thumbnail']; ?>" alt="<?php echo $tutorial['title']; ?>" style="width: 80px; height: 45px; object-fit: cover; border-radius: 4px;">
                            <?php else: ?>
                            <div style="width: 80px; height: 45px; background: #eee; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-video" style="color: #aaa;"></i>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $tutorial['title']; ?></td>
                        <td><?php echo !empty($tutorial['category_name']) ? $tutorial['category_name'] : 'Uncategorized'; ?></td>
                        <td><?php echo !empty($tutorial['duration']) ? $tutorial['duration'] : '-'; ?></td>
                        <td>
                            <span class="status-badge <?php echo $tutorial['status'] === 'published' ? 'status-published' : 'status-draft'; ?>">
                                <?php echo ucfirst($tutorial['status']); ?>
                            </span>
                        </td>
                        <td><?php echo $tutorial['display_order']; ?></td>
                        <td class="actions-cell">
                            <div class="action-buttons">
                                <a href="<?php echo $tutorial['video_url']; ?>" class="action-btn view-btn" target="_blank" title="View"><i class="fas fa-eye"></i></a>
                                <a href="video_tutorial_edit.php?id=<?php echo $tutorial['id']; ?>" class="action-btn edit-btn" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="video_tutorials.php?delete=<?php echo $tutorial['id']; ?>" class="action-btn delete-btn" title="Delete" onclick="return confirm('Are you sure you want to delete this video tutorial?');"><i class="fas fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <p>No video tutorials found. <a href="video_tutorial_edit.php">Add your first tutorial</a>.</p>
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