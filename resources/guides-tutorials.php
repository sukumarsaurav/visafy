<?php
$page_title = "Guides & Tutorials | CANEXT Immigration";

include('../admin/includes/db_connection.php');

// Get selected category (if any)
$category_filter = '';
$category_name = '';
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $category_slug = sanitize($_GET['category']);
    $category_sql = "SELECT id, name FROM guide_categories WHERE slug = '$category_slug'";
    $category_result = executeQuery($category_sql);
    
    if ($category_result && $category_result->num_rows > 0) {
        $category = $category_result->fetch_assoc();
        $category_filter = "AND g.category_id = " . $category['id'];
        $category_name = $category['name'];
    }
}

// Get all guide categories
$sql = "SELECT * FROM guide_categories WHERE guide_count > 0 ORDER BY display_order, name";
$categories = executeQuery($sql);

// Get guides
$guides_sql = "SELECT g.*, c.name as category_name, c.slug as category_slug 
              FROM guides g
              JOIN guide_categories c ON g.category_id = c.id
              WHERE g.status = 'published' $category_filter
              ORDER BY g.publish_date DESC";
$guides = executeQuery($guides_sql);

// Get video tutorials
$videos_sql = "SELECT * FROM video_tutorials WHERE status = 'published' ORDER BY display_order, created_at DESC LIMIT 3";
$videos = executeQuery($videos_sql);

// Get downloadable resources
$resources_sql = "SELECT * FROM downloadable_resources WHERE status = 'published' ORDER BY display_order, created_at DESC LIMIT 3";
$resources = executeQuery($resources_sql);

// Function to format date
function formatDate($date) {
    return date('F j, Y', strtotime($date));
}

include('../includes/header.php');
?>

<!-- Page Header -->
<section class="page-header" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('../images/resources/guides-header.jpg'); background-size: cover; background-position: center; padding: 100px 0; color: var(--color-light); text-align: center;background-color: var(--color-burgundy);">
<div style="position: absolute; width: 300px; height: 300px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); top: -100px; left: -100px;"></div>
    <div style="position: absolute; width: 200px; height: 200px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); bottom: -50px; right: 10%; animation: pulse 4s infinite alternate;"></div>
    <div style="position: absolute; width: 100px; height: 100px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); top: 30%; right: 20%; animation: pulse 3s infinite alternate;"></div>
    <div class="container">
        <h1 data-aos="fade-up">Guides & Tutorials<?php echo !empty($category_name) ? ': ' . $category_name : ''; ?></h1>
        <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <ol class="breadcrumb" style="display: flex; justify-content: center; list-style: none; padding: 0; margin: 20px 0 0 0;">
                <li class="breadcrumb-item"><a href="../index.php" style="color: var(--color-cream);">Home</a></li>
                <li class="breadcrumb-item"><a href="../resources.php" style="color: var(--color-cream);">Resources</a></li>
                <li class="breadcrumb-item active" aria-current="page" style="color: var(--color-light);">Guides & Tutorials</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Categories Section -->
<?php if ($categories && $categories->num_rows > 0): ?>
<section class="section categories-section" style="background-color: var(--color-cream);">
    <div class="container">
        <div class="categories-container" style="display: flex; justify-content: center; flex-wrap: wrap; gap: 15px; margin-bottom: 20px;">
            <a href="guides-tutorials.php" class="category-pill <?php echo empty($category_filter) ? 'active' : ''; ?>" style="padding: 8px 20px; border-radius: 30px; background-color: <?php echo empty($category_filter) ? 'var(--color-burgundy)' : 'white'; ?>; color: <?php echo empty($category_filter) ? 'white' : 'var(--color-burgundy)'; ?>; text-decoration: none; font-weight: 500; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">All Guides</a>
            
            <?php while ($category = $categories->fetch_assoc()): ?>
            <a href="?category=<?php echo $category['slug']; ?>" class="category-pill <?php echo isset($category_slug) && $category_slug === $category['slug'] ? 'active' : ''; ?>" style="padding: 8px 20px; border-radius: 30px; background-color: <?php echo isset($category_slug) && $category_slug === $category['slug'] ? 'var(--color-burgundy)' : 'white'; ?>; color: <?php echo isset($category_slug) && $category_slug === $category['slug'] ? 'white' : 'var(--color-burgundy)'; ?>; text-decoration: none; font-weight: 500; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                <i class="<?php echo $category['icon']; ?>" style="margin-right: 5px;"></i><?php echo $category['name']; ?>
            </a>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Popular Guides Section -->
<section class="section guides-section">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up">Immigration Guides<?php echo !empty($category_name) ? ': ' . $category_name : ''; ?></h2>
        <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">Step-by-step instructions to help you navigate through various immigration processes.</p>
        
        <?php if ($guides && $guides->num_rows > 0): ?>
        <div class="guides-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin-top: 40px;">
            <?php while ($guide = $guides->fetch_assoc()): ?>
            <div class="guide-card" data-aos="fade-up" style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                <div class="guide-image" style="height: 200px; background-image: url('../<?php echo !empty($guide['featured_image']) ? $guide['featured_image'] : 'images/resources/guide-default.jpg'; ?>'); background-size: cover; background-position: center;"></div>
                <div class="guide-content" style="padding: 20px;">
                    <div class="guide-meta" style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span style="color: var(--color-burgundy);"><?php echo formatDate($guide['publish_date']); ?></span>
                        <span style="color: var(--color-burgundy);"><?php echo $guide['category_name']; ?></span>
                    </div>
                    <h3 style="color: var(--color-burgundy); margin-bottom: 15px;"><?php echo $guide['title']; ?></h3>
                    <p style="margin-bottom: 20px;"><?php echo $guide['excerpt']; ?></p>
                    <a href="guide-details.php?slug=<?php echo $guide['slug']; ?>" class="btn btn-secondary">View Guide</a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="no-guides" style="text-align: center; padding: 40px 0;">
            <h3>No guides found.</h3>
            <p>Check back soon for new content!</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Video Tutorials Section -->
<section class="section tutorials-section" style="background-color: var(--color-cream);">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up">Video Tutorials</h2>
        <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">Watch our detailed video tutorials on various immigration topics.</p>
        
        <div class="tutorials-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin-top: 40px;">
            <?php if ($videos && $videos->num_rows > 0): ?>
                <?php while($video = $videos->fetch_assoc()): ?>
                    <div class="tutorial-card" data-aos="fade-up" style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                        <div class="video-placeholder" style="height: 200px; background: var(--color-burgundy); display: flex; align-items: center; justify-content: center; position: relative; <?php echo !empty($video['thumbnail']) ? 'background-image: url(../'.$video['thumbnail'].'); background-size: cover; background-position: center;' : ''; ?>">
                            <a href="<?php echo $video['video_url']; ?>" class="video-play-btn" target="_blank" style="position: absolute; width: 60px; height: 60px; background: rgba(255,255,255,0.8); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-play" style="color: var(--color-burgundy); font-size: 1.5rem;"></i>
                            </a>
                            <?php if (!empty($video['duration'])): ?>
                            <span class="video-duration" style="position: absolute; bottom: 10px; right: 10px; background: rgba(0,0,0,0.7); color: white; padding: 2px 8px; border-radius: 4px; font-size: 12px;"><?php echo $video['duration']; ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="tutorial-content" style="padding: 20px;">
                            <h3 style="color: var(--color-burgundy); margin-bottom: 15px;"><?php echo $video['title']; ?></h3>
                            <p><?php echo $video['description']; ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <!-- Tutorial 1 -->
                <div class="tutorial-card" data-aos="fade-up" style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                    <div class="video-placeholder" style="height: 200px; background: var(--color-burgundy); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-play" style="color: white; font-size: 3rem;"></i>
                    </div>
                    <div class="tutorial-content" style="padding: 20px;">
                        <h3 style="color: var(--color-burgundy); margin-bottom: 15px;">CRS Score Calculator Tutorial</h3>
                        <p>Learn how to calculate your Comprehensive Ranking System score.</p>
                    </div>
                </div>
                
                <!-- Tutorial 2 -->
                <div class="tutorial-card" data-aos="fade-up" data-aos-delay="100" style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                    <div class="video-placeholder" style="height: 200px; background: var(--color-burgundy); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-play" style="color: white; font-size: 3rem;"></i>
                    </div>
                    <div class="tutorial-content" style="padding: 20px;">
                        <h3 style="color: var(--color-burgundy); margin-bottom: 15px;">Document Checklist Review</h3>
                        <p>Detailed walkthrough of required documents for immigration.</p>
                    </div>
                </div>
                
                <!-- Tutorial 3 -->
                <div class="tutorial-card" data-aos="fade-up" data-aos-delay="200" style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                    <div class="video-placeholder" style="height: 200px; background: var(--color-burgundy); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-play" style="color: white; font-size: 3rem;"></i>
                    </div>
                    <div class="tutorial-content" style="padding: 20px;">
                        <h3 style="color: var(--color-burgundy); margin-bottom: 15px;">Interview Preparation</h3>
                        <p>Tips and strategies for immigration interviews.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if ($videos && $videos->num_rows > 0): ?>
        <div class="text-center" data-aos="fade-up" style="margin-top: 30px;">
            <a href="video-tutorials.php" class="btn btn-secondary">View All Tutorials</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Resources Section -->
<section class="section resources-section">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up">Downloadable Resources</h2>
        <div class="resources-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin-top: 40px;">
            <?php if ($resources && $resources->num_rows > 0): ?>
                <?php while($resource = $resources->fetch_assoc()): ?>
                    <div class="resource-card" data-aos="fade-up" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                        <i class="<?php echo $resource['icon']; ?>" style="font-size: 2rem; color: var(--color-burgundy); margin-bottom: 20px;"></i>
                        <h3 style="color: var(--color-burgundy); margin-bottom: 15px;"><?php echo $resource['title']; ?></h3>
                        <p style="margin-bottom: 20px;"><?php echo $resource['description']; ?></p>
                        <a href="../<?php echo $resource['file_path']; ?>" class="btn btn-secondary" download>Download <?php echo strtoupper($resource['file_type']); ?></a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <!-- Resource 1 -->
                <div class="resource-card" data-aos="fade-up" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                    <i class="fas fa-file-pdf" style="font-size: 2rem; color: var(--color-burgundy); margin-bottom: 20px;"></i>
                    <h3 style="color: var(--color-burgundy); margin-bottom: 15px;">Document Checklist</h3>
                    <p style="margin-bottom: 20px;">Comprehensive checklist of required documents for various visa applications.</p>
                    <a href="#" class="btn btn-secondary">Download PDF</a>
                </div>
                
                <!-- Resource 2 -->
                <div class="resource-card" data-aos="fade-up" data-aos-delay="100" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                    <i class="fas fa-file-excel" style="font-size: 2rem; color: var(--color-burgundy); margin-bottom: 20px;"></i>
                    <h3 style="color: var(--color-burgundy); margin-bottom: 15px;">Cost Calculator</h3>
                    <p style="margin-bottom: 20px;">Excel sheet to calculate immigration costs and living expenses.</p>
                    <a href="#" class="btn btn-secondary">Download Excel</a>
                </div>
                
                <!-- Resource 3 -->
                <div class="resource-card" data-aos="fade-up" data-aos-delay="200" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                    <i class="fas fa-file-word" style="font-size: 2rem; color: var(--color-burgundy); margin-bottom: 20px;"></i>
                    <h3 style="color: var(--color-burgundy); margin-bottom: 15px;">Letter Templates</h3>
                    <p style="margin-bottom: 20px;">Templates for reference letters, statements of purpose, and more.</p>
                    <a href="#" class="btn btn-secondary">Download Word</a>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if ($resources && $resources->num_rows > 0): ?>
        <div class="text-center" data-aos="fade-up" style="margin-top: 30px;">
            <a href="downloadable-resources.php" class="btn btn-secondary">View All Resources</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include('../includes/footer.php'); ?>
