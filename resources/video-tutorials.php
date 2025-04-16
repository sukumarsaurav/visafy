<?php
$page_title = "Video Tutorials";
include('../includes/header.php');
include('../admin/includes/db_connection.php');

// Get categories for filter
$categories_sql = "SELECT * FROM guide_categories ORDER BY name";
$categories = executeQuery($categories_sql);

// Process filter
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$search_term = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Build query with filters
$sql = "SELECT vt.*, gc.name as category_name 
        FROM video_tutorials vt 
        LEFT JOIN guide_categories gc ON vt.category_id = gc.id 
        WHERE vt.status = 'published'";

if ($category_filter > 0) {
    $sql .= " AND vt.category_id = $category_filter";
}

if (!empty($search_term)) {
    $sql .= " AND (vt.title LIKE '%$search_term%' OR vt.description LIKE '%$search_term%')";
}

$sql .= " ORDER BY vt.display_order, vt.title";
$tutorials = executeQuery($sql);

// Function to get video thumbnail from YouTube URL
function getYouTubeThumbnail($url) {
    $video_id = '';
    
    // Extract video ID from different YouTube URL formats
    if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $url, $matches)) {
        $video_id = $matches[1];
    } else if (preg_match('/youtube\.com\/embed\/([^\&\?\/]+)/', $url, $matches)) {
        $video_id = $matches[1];
    } else if (preg_match('/youtube\.com\/v\/([^\&\?\/]+)/', $url, $matches)) {
        $video_id = $matches[1];
    } else if (preg_match('/youtu\.be\/([^\&\?\/]+)/', $url, $matches)) {
        $video_id = $matches[1];
    }
    
    if ($video_id) {
        // Return high quality thumbnail
        return "https://img.youtube.com/vi/{$video_id}/hqdefault.jpg";
    }
    
    return ''; // Return empty if no ID found
}

// Format duration function
function formatDuration($seconds) {
    if ($seconds < 60) {
        return $seconds . " sec";
    } else if ($seconds < 3600) {
        $minutes = floor($seconds / 60);
        return $minutes . " min";
    } else {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        return $hours . " hr " . $minutes . " min";
    }
}

// Track video view (this would be called via AJAX when a user plays a video)
if (isset($_GET['view']) && !empty($_GET['view'])) {
    $video_id = (int)$_GET['view'];
    
    // Update view count
    $update_sql = "UPDATE video_tutorials SET view_count = view_count + 1 WHERE id = $video_id";
    executeQuery($update_sql);
    
    // Redirect back to the page
    header("Location: video-tutorials.php");
    exit;
}
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1>Video Tutorials</h1>
        <p>Learn about the immigration process through our helpful video guides</p>
    </div>
</div>

<!-- Videos Section -->
<section class="videos-section section-padding">
    <div class="container">
        <!-- Filter and Search -->
        <div class="video-filters">
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
                            <input type="text" name="search" id="search" class="form-control" placeholder="Search videos..." value="<?php echo htmlspecialchars($search_term); ?>">
                            <button type="submit" class="search-btn">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="filter-group filter-buttons">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="video-tutorials.php" class="btn btn-outline">Reset</a>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Videos Grid -->
        <div class="videos-grid">
            <?php if ($tutorials && $tutorials->num_rows > 0): 
                while ($tutorial = $tutorials->fetch_assoc()): 
                    $thumbnail = getYouTubeThumbnail($tutorial['video_url']);
                ?>
                    <div class="video-card" data-aos="fade-up">
                        <div class="video-thumbnail">
                            <img src="<?php echo $thumbnail ? $thumbnail : 'images/default-video-thumb.jpg'; ?>" alt="<?php echo $tutorial['title']; ?>">
                            <div class="play-button" data-video-id="<?php echo $tutorial['id']; ?>" data-video-url="<?php echo $tutorial['video_url']; ?>">
                                <i class="fas fa-play"></i>
                            </div>
                            <?php if (!empty($tutorial['duration'])): ?>
                            <span class="video-duration"><?php echo formatDuration($tutorial['duration']); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="video-content">
                            <h3><?php echo $tutorial['title']; ?></h3>
                            <p class="video-description"><?php echo $tutorial['description']; ?></p>
                            <div class="video-meta">
                                <span class="video-category">
                                    <i class="fas fa-folder"></i>
                                    <?php echo !empty($tutorial['category_name']) ? $tutorial['category_name'] : 'General'; ?>
                                </span>
                                <?php if (!empty($tutorial['view_count'])): ?>
                                <span class="video-views">
                                    <i class="fas fa-eye"></i>
                                    <?php echo number_format($tutorial['view_count']); ?> views
                                </span>
                                <?php endif; ?>
                            </div>
                            <button class="btn btn-burgundy watch-btn" data-video-id="<?php echo $tutorial['id']; ?>" data-video-url="<?php echo $tutorial['video_url']; ?>">
                                <i class="fas fa-play-circle"></i> Watch Now
                            </button>
                        </div>
                    </div>
            <?php endwhile; else: ?>
                <div class="no-videos">
                    <i class="fas fa-video no-videos-icon"></i>
                    <h3>No tutorials found</h3>
                    <p>Please try a different search term or browse all categories.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Need Help Section -->
        <div class="need-help-section" data-aos="fade-up">
            <div class="need-help-content">
                <h2>Can't Find What You're Looking For?</h2>
                <p>Our team is here to assist you with personalized guidance for your immigration journey.</p>
                <a href="contact.php" class="btn btn-burgundy">Contact Us</a>
            </div>
        </div>
    </div>
</section>

<!-- Video Modal -->
<div class="video-modal">
    <div class="video-modal-content">
        <button class="close-modal">&times;</button>
        <div class="video-container">
            <iframe id="video-iframe" src="" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
    </div>
</div>

<!-- CSS Styles -->
<style>
    .videos-section {
        padding: 80px 0;
        background-color: #f9f9f9;
    }
    
    .video-filters {
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
    
    .videos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 30px;
        margin-bottom: 60px;
    }
    
    .video-card {
        background-color: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    
    .video-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    }
    
    .video-thumbnail {
        position: relative;
        overflow: hidden;
        padding-top: 56.25%; /* 16:9 aspect ratio */
    }
    
    .video-thumbnail img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .play-button {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background-color: rgba(109, 35, 35, 0.8);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        cursor: pointer;
        opacity: 0.8;
        transition: all 0.3s ease;
    }
    
    .video-thumbnail:hover .play-button {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1.1);
    }
    
    .video-duration {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background-color: rgba(0, 0, 0, 0.7);
        color: #fff;
        padding: 3px 6px;
        border-radius: 3px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .video-content {
        padding: 25px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    .video-content h3 {
        margin: 0 0 15px 0;
        font-size: 20px;
        color: #333;
    }
    
    .video-description {
        margin-bottom: 20px;
        color: #666;
        flex: 1;
    }
    
    .video-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 20px;
        font-size: 14px;
        color: #777;
    }
    
    .video-meta span {
        display: flex;
        align-items: center;
    }
    
    .video-meta i {
        margin-right: 5px;
        color: #6D2323;
    }
    
    .watch-btn {
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
        cursor: pointer;
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
    
    .no-videos {
        grid-column: 1 / -1;
        text-align: center;
        padding: 60px 0;
        color: #777;
    }
    
    .no-videos-icon {
        font-size: 50px;
        color: #ddd;
        margin-bottom: 20px;
    }
    
    .no-videos h3 {
        font-size: 24px;
        margin-bottom: 10px;
        color: #444;
    }
    
    /* Video Modal */
    .video-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }
    
    .video-modal-content {
        width: 90%;
        max-width: 900px;
        position: relative;
    }
    
    .close-modal {
        position: absolute;
        top: -40px;
        right: 0;
        color: white;
        font-size: 30px;
        background: none;
        border: none;
        cursor: pointer;
    }
    
    .video-container {
        position: relative;
        padding-bottom: 56.25%; /* 16:9 aspect ratio */
        height: 0;
        overflow: hidden;
    }
    
    .video-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
    
    /* Responsive Styles */
    @media (max-width: 768px) {
        .filter-row {
            flex-direction: column;
        }
        
        .filter-group {
            width: 100%;
        }
        
        .videos-grid {
            grid-template-columns: 1fr;
        }
        
        .need-help-section {
            padding: 30px 20px;
        }
    }
</style>

<!-- JavaScript for Video Modal -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const playButtons = document.querySelectorAll('.play-button, .watch-btn');
    const videoModal = document.querySelector('.video-modal');
    const closeModal = document.querySelector('.close-modal');
    const videoIframe = document.getElementById('video-iframe');
    
    // Open modal when a play button is clicked
    playButtons.forEach(button => {
        button.addEventListener('click', function() {
            const videoId = this.getAttribute('data-video-id');
            let videoUrl = this.getAttribute('data-video-url');
            
            // Convert YouTube URL to embed URL if needed
            if (videoUrl.includes('youtube.com/watch')) {
                videoUrl = videoUrl.replace('watch?v=', 'embed/');
            } else if (videoUrl.includes('youtu.be/')) {
                videoUrl = videoUrl.replace('youtu.be/', 'youtube.com/embed/');
            }
            
            // Add autoplay parameter
            if (!videoUrl.includes('?')) {
                videoUrl += '?autoplay=1';
            } else {
                videoUrl += '&autoplay=1';
            }
            
            // Set iframe source
            videoIframe.src = videoUrl;
            
            // Show modal
            videoModal.style.display = 'flex';
            
            // Track view
            fetch('video-tutorials.php?view=' + videoId, {
                method: 'GET'
            });
        });
    });
    
    // Close modal
    closeModal.addEventListener('click', function() {
        videoModal.style.display = 'none';
        videoIframe.src = '';
    });
    
    // Close modal when clicking outside the content
    videoModal.addEventListener('click', function(e) {
        if (e.target === videoModal) {
            videoModal.style.display = 'none';
            videoIframe.src = '';
        }
    });
    
    // Close modal on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && videoModal.style.display === 'flex') {
            videoModal.style.display = 'none';
            videoIframe.src = '';
        }
    });
});
</script>

<?php include('../includes/footer.php'); ?> 