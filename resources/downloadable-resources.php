<?php
$page_title = "Downloadable Resources";
include('../includes/header.php');
include('../admin/includes/db_connection.php');

// Get categories for filter
$categories_sql = "SELECT * FROM guide_categories ORDER BY name";
$categories = executeQuery($categories_sql);

// Process filter
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$search_term = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Build query with filters
$sql = "SELECT dr.*, gc.name as category_name 
        FROM downloadable_resources dr 
        LEFT JOIN guide_categories gc ON dr.category_id = gc.id 
        WHERE dr.status = 'published'";

if ($category_filter > 0) {
    $sql .= " AND dr.category_id = $category_filter";
}

if (!empty($search_term)) {
    $sql .= " AND (dr.title LIKE '%$search_term%' OR dr.description LIKE '%$search_term%')";
}

$sql .= " ORDER BY dr.display_order, dr.title";
$resources = executeQuery($sql);

// Format file size function
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

// Record download (this would be called via AJAX when a user clicks download)
if (isset($_GET['download']) && !empty($_GET['download'])) {
    $resource_id = (int)$_GET['download'];
    
    // Update download count
    $update_sql = "UPDATE downloadable_resources SET download_count = download_count + 1 WHERE id = $resource_id";
    executeQuery($update_sql);
    
    // Get file info
    $file_sql = "SELECT file_path, title FROM downloadable_resources WHERE id = $resource_id";
    $file_result = executeQuery($file_sql);
    
    if ($file_result && $file_result->num_rows > 0) {
        $file = $file_result->fetch_assoc();
        $file_path = $file['file_path'];
        
        // Redirect to file for download
        header("Location: $file_path");
        exit;
    }
}
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1>Downloadable Resources</h1>
        <p>Access helpful documents, templates, and guides for your immigration journey</p>
    </div>
</div>

<!-- Resources Section -->
<section class="resources-section section-padding">
    <div class="container">
        <!-- Filter and Search -->
        <div class="resource-filters">
            <form action="" method="GET" class="filter-form">
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="category">Category</label>
                        <select name="category" id="category" class="form-control">
                            <option value="0">All Categories</option>
                            <?php if ($categories && $categories->num_rows > 0): 
                                while ($category = $categories->fetch_assoc()): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo ($category_filter == $category['id']) ? 'selected' : ''; ?>>
                                        <?php echo $category['name']; ?>
                                    </option>
                            <?php endwhile; endif; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group search-group">
                        <label for="search">Search</label>
                        <div class="search-input-wrapper">
                            <input type="text" name="search" id="search" class="form-control" placeholder="Search resources..." value="<?php echo htmlspecialchars($search_term); ?>">
                            <button type="submit" class="search-btn">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="filter-group filter-buttons">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="downloadable-resources.php" class="btn btn-outline">Reset</a>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Resources Grid -->
        <div class="resources-grid">
            <?php if ($resources && $resources->num_rows > 0): 
                while ($resource = $resources->fetch_assoc()): ?>
                    <div class="resource-card" data-aos="fade-up">
                        <div class="resource-icon">
                            <i class="<?php echo $resource['icon']; ?>"></i>
                        </div>
                        <div class="resource-content">
                            <h3><?php echo $resource['title']; ?></h3>
                            <p class="resource-description"><?php echo $resource['description']; ?></p>
                            <div class="resource-meta">
                                <span class="resource-category">
                                    <i class="fas fa-folder"></i>
                                    <?php echo !empty($resource['category_name']) ? $resource['category_name'] : 'General'; ?>
                                </span>
                                <span class="resource-file-type">
                                    <i class="fas fa-file"></i>
                                    <?php echo strtoupper($resource['file_type']); ?>
                                </span>
                                <span class="resource-size">
                                    <i class="fas fa-weight"></i>
                                    <?php echo !empty($resource['file_size']) ? formatFileSize($resource['file_size']) : 'Unknown size'; ?>
                                </span>
                            </div>
                            <a href="downloadable-resources.php?download=<?php echo $resource['id']; ?>" class="btn btn-burgundy download-btn">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </div>
                    </div>
            <?php endwhile; else: ?>
                <div class="no-resources">
                    <i class="fas fa-file-download no-resources-icon"></i>
                    <h3>No resources found</h3>
                    <p>Please try a different search term or browse all categories.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Need Help Section -->
        <div class="need-help-section" data-aos="fade-up">
            <div class="need-help-content">
                <h2>Need Help Finding Resources?</h2>
                <p>Our team is here to assist you with finding the right documents and information for your immigration needs.</p>
                <a href="contact.php" class="btn btn-burgundy">Contact Us</a>
            </div>
        </div>
    </div>
</section>

<!-- CSS Styles -->
<style>
    .resources-section {
        padding: 80px 0;
        background-color: #f9f9f9;
    }
    
    .resource-filters {
        background-color: #fff;
        border-radius: 8px;
        padding: 25px;
        margin-bottom: 40px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    .filter-row {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        align-items: flex-end;
    }
    
    .filter-group {
        flex: 1;
        min-width: 200px;
    }
    
    .filter-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #333;
    }
    
    .filter-group select,
    .filter-group input {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-family: var(--font-primary);
        transition: all 0.3s ease;
    }
    
    .filter-group select:focus,
    .filter-group input:focus {
        border-color: #6D2323;
        outline: none;
        box-shadow: 0 0 0 2px rgba(109, 35, 35, 0.1);
    }
    
    .search-input-wrapper {
        position: relative;
    }
    
    .search-btn {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #6D2323;
        cursor: pointer;
    }
    
    .filter-buttons {
        display: flex;
        gap: 10px;
    }
    
    .resources-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 30px;
        margin-bottom: 60px;
    }
    
    .resource-card {
        background-color: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    
    .resource-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    }
    
    .resource-icon {
        background-color: #6D2323;
        color: #fff;
        font-size: 30px;
        height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .resource-content {
        padding: 25px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    .resource-content h3 {
        margin: 0 0 15px 0;
        font-size: 20px;
        color: #333;
    }
    
    .resource-description {
        margin-bottom: 20px;
        color: #666;
        flex: 1;
    }
    
    .resource-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 20px;
        font-size: 14px;
        color: #777;
    }
    
    .resource-meta span {
        display: flex;
        align-items: center;
    }
    
    .resource-meta i {
        margin-right: 5px;
        color: #6D2323;
    }
    
    .download-btn {
        align-self: flex-start;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }
    
    .btn-burgundy {
        background-color: #6D2323;
        color: #fff;
        padding: 12px 25px;
        border-radius: 5px;
        border: none;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }
    
    .btn-burgundy:hover {
        background-color: #8a2c2c;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(109, 35, 35, 0.2);
    }
    
    .btn-outline {
        background-color: transparent;
        color: #6D2323;
        border: 1px solid #6D2323;
        padding: 11px 25px;
        border-radius: 5px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }
    
    .btn-outline:hover {
        background-color: #f9f1e7;
    }
    
    .need-help-section {
        background-color: #6D2323;
        color: #fff;
        border-radius: 8px;
        padding: 40px;
        margin-top: 60px;
        text-align: center;
    }
    
    .need-help-content h2 {
        font-size: 28px;
        margin-bottom: 15px;
    }
    
    .need-help-content p {
        font-size: 16px;
        max-width: 600px;
        margin: 0 auto 25px;
        opacity: 0.9;
    }
    
    .need-help-section .btn-burgundy {
        background-color: #fff;
        color: #6D2323;
    }
    
    .need-help-section .btn-burgundy:hover {
        background-color: #f9f1e7;
    }
    
    .no-resources {
        grid-column: 1 / -1;
        text-align: center;
        padding: 60px 0;
        color: #777;
    }
    
    .no-resources-icon {
        font-size: 50px;
        color: #ddd;
        margin-bottom: 20px;
    }
    
    .no-resources h3 {
        font-size: 24px;
        margin-bottom: 10px;
        color: #444;
    }
    
    /* Responsive Styles */
    @media (max-width: 768px) {
        .filter-row {
            flex-direction: column;
        }
        
        .filter-group {
            width: 100%;
        }
        
        .resources-grid {
            grid-template-columns: 1fr;
        }
        
        .need-help-section {
            padding: 30px 20px;
        }
    }
</style>

<?php include('../includes/footer.php'); ?> 