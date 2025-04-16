<?php
include('../admin/includes/db_connection.php');

// Get the requested blog post
$slug = isset($_GET['slug']) ? sanitize($_GET['slug']) : '';

if (empty($slug)) {
    // Redirect to blog list if no slug provided
    header('Location: blog.php');
    exit;
}

// Get the blog post
$sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
        FROM blog_posts p
        JOIN blog_categories c ON p.category_id = c.id
        WHERE p.slug = '$slug' AND p.status = 'published'";
$result = executeQuery($sql);

if (!$result || $result->num_rows === 0) {
    // Redirect to blog list if post not found
    header('Location: blog.php');
    exit;
}

$post = $result->fetch_assoc();
$page_title = $post['title'] . " | CANEXT Immigration";

// Get related posts from the same category
$related_sql = "SELECT id, title, slug, featured_image, publish_date 
               FROM blog_posts 
               WHERE category_id = {$post['category_id']} 
                 AND id != {$post['id']} 
                 AND status = 'published' 
               ORDER BY publish_date DESC 
               LIMIT 3";
$related_posts = executeQuery($related_sql);

// Function to format date
function formatDate($date) {
    return date('F j, Y', strtotime($date));
}

include('../includes/header.php');
?>

<!-- Page Header -->
<section class="page-header" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('../<?php echo !empty($post['featured_image']) ? $post['featured_image'] : 'images/blog/default.jpg'; ?>'); background-size: cover; background-position: center; padding: 100px 0; color: var(--color-light); text-align: center;background-color: var(--color-burgundy);">
<div style="position: absolute; width: 300px; height: 300px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); top: -100px; left: -100px;"></div>
    <div style="position: absolute; width: 200px; height: 200px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); bottom: -50px; right: 10%; animation: pulse 4s infinite alternate;"></div>
    <div style="position: absolute; width: 100px; height: 100px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); top: 30%; right: 20%; animation: pulse 3s infinite alternate;"></div>
    <div class="container">
        <h1 data-aos="fade-up"><?php echo $post['title']; ?></h1>
        <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <ol class="breadcrumb" style="display: flex; justify-content: center; list-style: none; padding: 0; margin: 20px 0 0 0;">
                <li class="breadcrumb-item"><a href="../index.php" style="color: var(--color-cream);">Home</a></li>
                <li class="breadcrumb-item"><a href="../resources.php" style="color: var(--color-cream);">Resources</a></li>
                <li class="breadcrumb-item"><a href="blog.php" style="color: var(--color-cream);">Blog</a></li>
                <li class="breadcrumb-item active" aria-current="page" style="color: var(--color-light);"><?php echo $post['title']; ?></li>
            </ol>
        </nav>
    </div>
</section>

<!-- Blog Post Content -->
<section class="section blog-content-section">
    <div class="container">
        <div class="blog-content-layout" style="display: grid; grid-template-columns: 1fr 300px; gap: 40px;">
            <!-- Main Content -->
            <article class="blog-content" data-aos="fade-up">
                <!-- Post Meta -->
                <div class="post-meta" style="margin-bottom: 30px; display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding-bottom: 15px;">
                    <div>
                        <span style="display: inline-block; margin-right: 20px;"><i class="fas fa-user" style="color: var(--color-burgundy); margin-right: 5px;"></i> <?php echo $post['author']; ?></span>
                        <span style="display: inline-block; margin-right: 20px;"><i class="fas fa-calendar" style="color: var(--color-burgundy); margin-right: 5px;"></i> <?php echo formatDate($post['publish_date']); ?></span>
                        <span><i class="fas fa-folder" style="color: var(--color-burgundy); margin-right: 5px;"></i> <a href="blog.php?category=<?php echo $post['category_slug']; ?>" style="color: var(--color-burgundy); text-decoration: none;"><?php echo $post['category_name']; ?></a></span>
                    </div>
                    <div class="social-share">
                        <a href="https://facebook.com/sharer/sharer.php?u=<?php echo urlencode("https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"); ?>" target="_blank" style="color: #3b5998; margin-left: 10px;"><i class="fab fa-facebook"></i></a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode("https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"); ?>&text=<?php echo urlencode($post['title']); ?>" target="_blank" style="color: #1da1f2; margin-left: 10px;"><i class="fab fa-twitter"></i></a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode("https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"); ?>&title=<?php echo urlencode($post['title']); ?>" target="_blank" style="color: #0077b5; margin-left: 10px;"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                
                <!-- Post Content -->
                <div class="post-content" style="line-height: 1.8; font-size: 16px;">
                    <?php echo $post['content']; ?>
                </div>
                
                <!-- Post Tags (if you want to add tags in the future) -->
                <div class="post-tags" style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee;">
                    <strong>Category:</strong> <a href="blog.php?category=<?php echo $post['category_slug']; ?>" style="color: var(--color-burgundy); text-decoration: none; display: inline-block; margin-right: 10px; padding: 4px 12px; background-color: #f5f5f5; border-radius: 20px;"><?php echo $post['category_name']; ?></a>
                </div>
            </article>
            
            <!-- Sidebar -->
            <div class="blog-sidebar" style="background: white; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); padding: 25px;">
                <!-- About Author -->
                <div class="sidebar-section" style="margin-bottom: 30px;">
                    <h3 style="color: var(--color-burgundy); margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #eee;">About the Author</h3>
                    <div style="display: flex; align-items: center;">
                        <div style="width: 60px; height: 60px; border-radius: 50%; background-color: #f5f5f5; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                            <i class="fas fa-user" style="font-size: 24px; color: #aaa;"></i>
                        </div>
                        <div>
                            <h4 style="margin-bottom: 5px;"><?php echo $post['author']; ?></h4>
                            <p style="font-size: 14px; color: #777;">Immigration Expert at CANEXT</p>
                        </div>
                    </div>
                </div>
                
                <!-- Categories -->
                <div class="sidebar-section">
                    <h3 style="color: var(--color-burgundy); margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #eee;">More From CANEXT</h3>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 10px;"><a href="../services.php" style="color: var(--color-burgundy); text-decoration: none;"><i class="fas fa-angle-right" style="margin-right: 8px;"></i>Our Services</a></li>
                        <li style="margin-bottom: 10px;"><a href="../resources.php" style="color: var(--color-burgundy); text-decoration: none;"><i class="fas fa-angle-right" style="margin-right: 8px;"></i>Resources</a></li>
                        <li style="margin-bottom: 10px;"><a href="faq.php" style="color: var(--color-burgundy); text-decoration: none;"><i class="fas fa-angle-right" style="margin-right: 8px;"></i>FAQ</a></li>
                        <li style="margin-bottom: 10px;"><a href="../contact.php" style="color: var(--color-burgundy); text-decoration: none;"><i class="fas fa-angle-right" style="margin-right: 8px;"></i>Contact Us</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Posts Section -->
<?php if ($related_posts && $related_posts->num_rows > 0): ?>
<section class="section related-posts-section" style="background-color: #f9f9f9;">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up" style="text-align: center; margin-bottom: 40px;">Related Posts</h2>
        
        <div class="related-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
            <?php while ($related = $related_posts->fetch_assoc()): ?>
            <article class="related-post" data-aos="fade-up" style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                <div class="post-image" style="height: 200px; background-image: url('../<?php echo !empty($related['featured_image']) ? $related['featured_image'] : 'images/blog/default.jpg'; ?>'); background-size: cover; background-position: center;"></div>
                <div class="post-content" style="padding: 20px;">
                    <div class="post-meta" style="color: var(--color-burgundy); margin-bottom: 10px;"><?php echo formatDate($related['publish_date']); ?></div>
                    <h3 style="color: var(--color-burgundy); margin-bottom: 15px;"><?php echo $related['title']; ?></h3>
                    <a href="blog-detail.php?slug=<?php echo $related['slug']; ?>" class="btn btn-secondary">Read More</a>
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