<?php
include('../admin/includes/db_connection.php');

// Get the requested guide
$slug = isset($_GET['slug']) ? sanitize($_GET['slug']) : '';

if (empty($slug)) {
    // Redirect to guides list if no slug provided
    header('Location: guides-tutorials.php');
    exit;
}

// Get the guide
$sql = "SELECT g.*, c.name as category_name, c.slug as category_slug 
        FROM guides g
        JOIN guide_categories c ON g.category_id = c.id
        WHERE g.slug = '$slug' AND g.status = 'published'";
$result = executeQuery($sql);

if (!$result || $result->num_rows === 0) {
    // Redirect to guides list if guide not found
    header('Location: guides-tutorials.php');
    exit;
}

$guide = $result->fetch_assoc();
$page_title = $guide['title'] . " | CANEXT Immigration";

// Get related guides from the same category
$related_sql = "SELECT id, title, slug, featured_image, publish_date, excerpt 
               FROM guides 
               WHERE category_id = {$guide['category_id']} 
                 AND id != {$guide['id']} 
                 AND status = 'published' 
               ORDER BY publish_date DESC 
               LIMIT 2";
$related_guides = executeQuery($related_sql);

// Get guide downloads if any
$downloads_sql = "SELECT * FROM guide_downloads WHERE guide_id = {$guide['id']}";
$downloads = executeQuery($downloads_sql);

// Function to format date
function formatDate($date) {
    return date('F j, Y', strtotime($date));
}

include('../includes/header.php');
?>

<!-- Page Header -->
<section class="page-header" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('../<?php echo !empty($guide['featured_image']) ? $guide['featured_image'] : 'images/resources/guides-header.jpg'; ?>'); background-size: cover; background-position: center; padding: 100px 0; color: var(--color-light); text-align: center;background-color: var(--color-burgundy);">
<div style="position: absolute; width: 300px; height: 300px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); top: -100px; left: -100px;"></div>
    <div style="position: absolute; width: 200px; height: 200px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); bottom: -50px; right: 10%; animation: pulse 4s infinite alternate;"></div>
    <div style="position: absolute; width: 100px; height: 100px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); top: 30%; right: 20%; animation: pulse 3s infinite alternate;"></div>
    <div class="container">
        <h1 data-aos="fade-up"><?php echo $guide['title']; ?></h1>
        <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <ol class="breadcrumb" style="display: flex; justify-content: center; list-style: none; padding: 0; margin: 20px 0 0 0;">
                <li class="breadcrumb-item"><a href="../index.php" style="color: var(--color-cream);">Home</a></li>
                <li class="breadcrumb-item"><a href="../resources.php" style="color: var(--color-cream);">Resources</a></li>
                <li class="breadcrumb-item"><a href="guides-tutorials.php" style="color: var(--color-cream);">Guides & Tutorials</a></li>
                <li class="breadcrumb-item active" aria-current="page" style="color: var(--color-light);"><?php echo $guide['title']; ?></li>
            </ol>
        </nav>
    </div>
</section>

<!-- Guide Content -->
<section class="section guide-content">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8" data-aos="fade-up">
                <div class="content-box" style="background: white; border-radius: 10px; padding: 40px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 30px;">
                    <!-- Guide Meta -->
                    <div class="guide-meta" style="margin-bottom: 30px; display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding-bottom: 15px;">
                        <div>
                            <span style="display: inline-block; margin-right: 20px;"><i class="fas fa-user" style="color: var(--color-burgundy); margin-right: 5px;"></i> <?php echo $guide['author']; ?></span>
                            <?php if (!empty($guide['publish_date'])): ?>
                            <span style="display: inline-block; margin-right: 20px;"><i class="fas fa-calendar" style="color: var(--color-burgundy); margin-right: 5px;"></i> <?php echo formatDate($guide['publish_date']); ?></span>
                            <?php endif; ?>
                            <span><i class="fas fa-folder" style="color: var(--color-burgundy); margin-right: 5px;"></i> <a href="guides-tutorials.php?category=<?php echo $guide['category_slug']; ?>" style="color: var(--color-burgundy); text-decoration: none;"><?php echo $guide['category_name']; ?></a></span>
                        </div>
                        <div class="social-share">
                            <a href="https://facebook.com/sharer/sharer.php?u=<?php echo urlencode("https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"); ?>" target="_blank" style="color: #3b5998; margin-left: 10px;"><i class="fab fa-facebook"></i></a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode("https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"); ?>&text=<?php echo urlencode($guide['title']); ?>" target="_blank" style="color: #1da1f2; margin-left: 10px;"><i class="fab fa-twitter"></i></a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode("https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"); ?>&title=<?php echo urlencode($guide['title']); ?>" target="_blank" style="color: #0077b5; margin-left: 10px;"><i class="fab fa-linkedin"></i></a>
                        </div>
                    </div>
                    
                    <!-- Guide Content -->
                    <div class="guide-content" style="line-height: 1.8; font-size: 16px;">
                        <?php echo $guide['content']; ?>
                    </div>
                    
                    <!-- Category -->
                    <div class="guide-category" style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee;">
                        <strong>Category:</strong> <a href="guides-tutorials.php?category=<?php echo $guide['category_slug']; ?>" style="color: var(--color-burgundy); text-decoration: none; display: inline-block; margin-right: 10px; padding: 4px 12px; background-color: #f5f5f5; border-radius: 20px;"><?php echo $guide['category_name']; ?></a>
                    </div>
                </div>
                
                <!-- CTA Box -->
                <div class="cta-box" style="background: linear-gradient(to right, var(--color-burgundy), #8a3a3a); color: white; border-radius: 10px; padding: 30px; text-align: center; margin-bottom: 30px;">
                    <h3 style="margin-bottom: 15px;">Need Help With Your Immigration Process?</h3>
                    <p style="margin-bottom: 20px;">Our consultants can guide you through the entire immigration process.</p>
                    <a href="../contact.php" class="btn btn-primary" style="background-color: white; color: var(--color-burgundy);">Schedule a Consultation</a>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="sidebar" data-aos="fade-up" data-aos-delay="100">
                    <!-- Quick Navigation -->
                    <div class="sidebar-box" style="background: white; border-radius: 10px; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 30px;">
                        <h4 style="color: var(--color-burgundy); margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #eee;">About the Author</h4>
                        <div style="display: flex; align-items: center;">
                            <div style="width: 60px; height: 60px; border-radius: 50%; background-color: #f5f5f5; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                <i class="fas fa-user" style="font-size: 24px; color: #aaa;"></i>
                            </div>
                            <div>
                                <h4 style="margin-bottom: 5px;"><?php echo $guide['author']; ?></h4>
                                <p style="font-size: 14px; color: #777;">Immigration Expert at CANEXT</p>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($downloads && $downloads->num_rows > 0): ?>
                    <!-- Downloads -->
                    <div class="sidebar-box" style="background: linear-gradient(135deg, #f9f5f1, #efe6dd); border-radius: 10px; padding: 25px; margin-bottom: 30px; text-align: center;">
                        <i class="fas fa-file-pdf" style="font-size: 2.5rem; color: var(--color-burgundy); margin-bottom: 15px;"></i>
                        <h4 style="color: var(--color-burgundy); margin-bottom: 15px;">Guide Downloads</h4>
                        <ul style="list-style: none; padding: 0; text-align: left;">
                            <?php while ($download = $downloads->fetch_assoc()): ?>
                            <li style="margin-bottom: 10px;">
                                <a href="../<?php echo $download['file_path']; ?>" class="btn btn-secondary" style="width: 100%; text-align: left;">
                                    <i class="fas fa-download" style="margin-right: 10px;"></i> <?php echo $download['title']; ?>
                                </a>
                            </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Related Guides -->
                    <?php if ($related_guides && $related_guides->num_rows > 0): ?>
                    <div class="sidebar-box" style="background: white; border-radius: 10px; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 30px;">
                        <h4 style="color: var(--color-burgundy); margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #eee;">Related Guides</h4>
                        <ul style="list-style: none; padding: 0;">
                            <?php while ($related = $related_guides->fetch_assoc()): ?>
                            <li style="margin-bottom: 15px;">
                                <a href="guide-details.php?slug=<?php echo $related['slug']; ?>" style="display: flex; color: inherit; text-decoration: none;">
                                    <div style="width: 80px; height: 60px; background-image: url('../<?php echo !empty($related['featured_image']) ? $related['featured_image'] : 'images/resources/guide-default.jpg'; ?>'); background-size: cover; background-position: center; border-radius: 5px; margin-right: 15px;"></div>
                                    <div>
                                        <h5 style="font-size: 1rem; margin: 0 0 5px; color: var(--color-burgundy);"><?php echo $related['title']; ?></h5>
                                        <p style="font-size: 0.9rem; color: #666; margin: 0;"><?php echo substr($related['excerpt'], 0, 50) . '...'; ?></p>
                                    </div>
                                </a>
                            </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <!-- More Resources -->
                    <div class="sidebar-box" style="background: white; border-radius: 10px; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                        <h4 style="color: var(--color-burgundy); margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #eee;">More CANEXT Resources</h4>
                        <ul style="list-style: none; padding: 0;">
                            <li style="margin-bottom: 10px;"><a href="blog.php" style="color: var(--color-burgundy); text-decoration: none;"><i class="fas fa-angle-right" style="margin-right: 8px;"></i>Immigration Blog</a></li>
                            <li style="margin-bottom: 10px;"><a href="faq.php" style="color: var(--color-burgundy); text-decoration: none;"><i class="fas fa-angle-right" style="margin-right: 8px;"></i>FAQ</a></li>
                            <li style="margin-bottom: 10px;"><a href="../services.php" style="color: var(--color-burgundy); text-decoration: none;"><i class="fas fa-angle-right" style="margin-right: 8px;"></i>Our Services</a></li>
                            <li style="margin-bottom: 10px;"><a href="../contact.php" style="color: var(--color-burgundy); text-decoration: none;"><i class="fas fa-angle-right" style="margin-right: 8px;"></i>Contact Us</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Guides Section -->
<?php if ($related_guides && $related_guides->num_rows > 0): ?>
<section class="section related-guides-section" style="background-color: #f9f9f9;">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up" style="text-align: center; margin-bottom: 40px;">Related Guides</h2>
        
        <div class="related-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
            <?php 
            // Reset the pointer to beginning
            $related_guides->data_seek(0);
            while ($related = $related_guides->fetch_assoc()): 
            ?>
            <article class="related-guide" data-aos="fade-up" style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                <div class="guide-image" style="height: 200px; background-image: url('../<?php echo !empty($related['featured_image']) ? $related['featured_image'] : 'images/resources/guide-default.jpg'; ?>'); background-size: cover; background-position: center;"></div>
                <div class="guide-content" style="padding: 20px;">
                    <div class="guide-meta" style="color: var(--color-burgundy); margin-bottom: 10px;"><?php echo formatDate($related['publish_date']); ?></div>
                    <h3 style="color: var(--color-burgundy); margin-bottom: 15px;"><?php echo $related['title']; ?></h3>
                    <a href="guide-details.php?slug=<?php echo $related['slug']; ?>" class="btn btn-secondary">Read Guide</a>
                </div>
            </article>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Call to Action -->
<section class="section cta-section" style="background-color: var(--color-burgundy); color: white; text-align: center; padding: 60px 0;">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up" style="color: white;">Ready to Start Your Immigration Journey?</h2>
        <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100" style="margin-bottom: 30px;">Contact our experts today for a personalized consultation.</p>
        <div data-aos="fade-up" data-aos-delay="200">
            <a href="../contact.php" class="btn btn-light" style="background-color: white; color: var(--color-burgundy);">Contact Us</a>
        </div>
    </div>
</section>

<?php include('../includes/footer.php'); ?> 