<?php
$page_title = "Immigration Blog | CANEXT Immigration";
include('../includes/header.php');
include('../admin/includes/db_connection.php');

// Pagination settings
$posts_per_page = 6;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $posts_per_page;

// Get selected category (if any)
$category_filter = '';
$category_name = '';
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $category_slug = sanitize($_GET['category']);
    $category_sql = "SELECT id, name FROM blog_categories WHERE slug = '$category_slug'";
    $category_result = executeQuery($category_sql);
    
    if ($category_result && $category_result->num_rows > 0) {
        $category = $category_result->fetch_assoc();
        $category_filter = "AND p.category_id = " . $category['id'];
        $category_name = $category['name'];
    }
}

// Get all blog categories
$sql = "SELECT * FROM blog_categories WHERE post_count > 0 ORDER BY display_order, name";
$categories = executeQuery($sql);

// Get featured posts (latest 3 published posts)
$sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
        FROM blog_posts p
        JOIN blog_categories c ON p.category_id = c.id
        WHERE p.status = 'published' 
        ORDER BY p.publish_date DESC 
        LIMIT 3";
$featured_posts = executeQuery($sql);

// Count total posts for pagination
$count_sql = "SELECT COUNT(*) as total FROM blog_posts p 
              JOIN blog_categories c ON p.category_id = c.id
              WHERE p.status = 'published' $category_filter";
$count_result = executeQuery($count_sql);
$total_posts = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_posts / $posts_per_page);

// Get posts for current page
$posts_sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
              FROM blog_posts p
              JOIN blog_categories c ON p.category_id = c.id
              WHERE p.status = 'published' $category_filter
              ORDER BY p.publish_date DESC 
              LIMIT $offset, $posts_per_page";
$blog_posts = executeQuery($posts_sql);

// Get recent posts for sidebar
$recent_posts_sql = "SELECT id, title, slug, featured_image, publish_date 
                    FROM blog_posts 
                    WHERE status = 'published' 
                    ORDER BY publish_date DESC 
                    LIMIT 5";
$recent_posts = executeQuery($recent_posts_sql);

// Function to format date
function formatDate($date) {
    return date('F j, Y', strtotime($date));
}
?>

<!-- Page Header -->
<section class="page-header" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('../images/resources/blog-header.jpg'); background-size: cover; background-position: center; padding: 100px 0; color: var(--color-light); text-align: center;background-color: var(--color-burgundy);">
<div style="position: absolute; width: 300px; height: 300px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); top: -100px; left: -100px;"></div>
    <div style="position: absolute; width: 200px; height: 200px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); bottom: -50px; right: 10%; animation: pulse 4s infinite alternate;"></div>
    <div style="position: absolute; width: 100px; height: 100px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); top: 30%; right: 20%; animation: pulse 3s infinite alternate;"></div>
    <div class="container">
        <h1 data-aos="fade-up">Immigration Blog<?php echo !empty($category_name) ? ': ' . $category_name : ''; ?></h1>
        <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <ol class="breadcrumb" style="display: flex; justify-content: center; list-style: none; padding: 0; margin: 20px 0 0 0;">
                <li class="breadcrumb-item"><a href="../index.php" style="color: var(--color-cream);">Home</a></li>
                <li class="breadcrumb-item"><a href="../resources.php" style="color: var(--color-cream);">Resources</a></li>
                <li class="breadcrumb-item active" aria-current="page" style="color: var(--color-light);">Blog</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Featured Posts Section (only show if we're on the first page and no category filter) -->
<?php if ($current_page == 1 && empty($category_filter) && $featured_posts && $featured_posts->num_rows > 0): ?>
<section class="section featured-section">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up">Featured Posts</h2>
        <div class="featured-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin-top: 40px;">
            <?php while ($post = $featured_posts->fetch_assoc()): ?>
            <article class="featured-post" data-aos="fade-up" style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                <div class="post-image" style="height: 200px; background-image: url('../<?php echo !empty($post['featured_image']) ? $post['featured_image'] : 'images/blog/default.jpg'; ?>'); background-size: cover; background-position: center;"></div>
                <div class="post-content" style="padding: 20px;">
                    <div class="post-meta" style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span style="color: var(--color-burgundy);"><?php echo formatDate($post['publish_date']); ?></span>
                        <span style="color: var(--color-burgundy);"><?php echo $post['category_name']; ?></span>
                    </div>
                    <h3 style="color: var(--color-burgundy); margin-bottom: 15px;"><?php echo $post['title']; ?></h3>
                    <p style="margin-bottom: 20px;"><?php echo $post['excerpt']; ?></p>
                    <a href="blog-detail.php?slug=<?php echo $post['slug']; ?>" class="btn btn-secondary">Read More</a>
                </div>
            </article>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Main Blog Section -->
<section class="section blog-section">
    <div class="container">
        <div class="blog-layout" style="display: grid; grid-template-columns: 1fr 300px; gap: 40px;">
            <!-- Blog Posts -->
            <div class="blog-posts">
                <?php if ($blog_posts && $blog_posts->num_rows > 0): ?>
                <div class="blog-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px;">
                    <?php while ($post = $blog_posts->fetch_assoc()): ?>
                    <article class="post-card" data-aos="fade-up" style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                        <div class="post-image" style="height: 200px; background-image: url('../<?php echo !empty($post['featured_image']) ? $post['featured_image'] : 'images/blog/default.jpg'; ?>'); background-size: cover; background-position: center;"></div>
                        <div class="post-content" style="padding: 20px;">
                            <div class="post-meta" style="color: var(--color-burgundy); margin-bottom: 10px;"><?php echo formatDate($post['publish_date']); ?></div>
                            <h3 style="color: var(--color-burgundy); margin-bottom: 15px;"><?php echo $post['title']; ?></h3>
                            <p style="margin-bottom: 20px;"><?php echo $post['excerpt']; ?></p>
                            <a href="blog-detail.php?slug=<?php echo $post['slug']; ?>" class="btn btn-secondary">Read More</a>
                        </div>
                    </article>
                    <?php endwhile; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="pagination" style="display: flex; justify-content: center; margin-top: 40px;">
                    <?php if ($current_page > 1): ?>
                    <a href="?<?php echo !empty($category_slug) ? "category=$category_slug&" : ""; ?>page=<?php echo $current_page - 1; ?>" class="pagination-link" style="margin: 0 5px; padding: 8px 15px; border: 1px solid #ddd; border-radius: 4px; color: var(--color-burgundy); text-decoration: none;">&laquo; Previous</a>
                    <?php endif; ?>
                    
                    <?php for($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                    <a href="?<?php echo !empty($category_slug) ? "category=$category_slug&" : ""; ?>page=<?php echo $i; ?>" class="pagination-link <?php echo $i == $current_page ? 'active' : ''; ?>" style="margin: 0 5px; padding: 8px 15px; border: 1px solid #ddd; border-radius: 4px; <?php echo $i == $current_page ? 'background-color: var(--color-burgundy); color: white;' : 'color: var(--color-burgundy);'; ?> text-decoration: none;"><?php echo $i; ?></a>
                    <?php endfor; ?>
                    
                    <?php if ($current_page < $total_pages): ?>
                    <a href="?<?php echo !empty($category_slug) ? "category=$category_slug&" : ""; ?>page=<?php echo $current_page + 1; ?>" class="pagination-link" style="margin: 0 5px; padding: 8px 15px; border: 1px solid #ddd; border-radius: 4px; color: var(--color-burgundy); text-decoration: none;">Next &raquo;</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <?php else: ?>
                <div class="no-posts" style="text-align: center; padding: 40px 0;">
                    <h3>No blog posts found.</h3>
                    <p>Check back soon for new content!</p>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar -->
            <div class="blog-sidebar" style="background: white; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); padding: 25px;">
                <!-- Categories -->
                <div class="sidebar-section" style="margin-bottom: 30px;">
                    <h3 style="color: var(--color-burgundy); margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #eee;">Categories</h3>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 10px;">
                            <a href="blog.php" style="color: var(--color-burgundy); text-decoration: none; display: flex; justify-content: space-between;">
                                <span>All Categories</span>
                            </a>
                        </li>
                        <?php if ($categories && $categories->num_rows > 0): ?>
                            <?php while ($category = $categories->fetch_assoc()): ?>
                            <li style="margin-bottom: 10px;">
                                <a href="?category=<?php echo $category['slug']; ?>" style="color: var(--color-burgundy); text-decoration: none; display: flex; justify-content: space-between;">
                                    <span><i class="<?php echo $category['icon']; ?>" style="margin-right: 8px;"></i><?php echo $category['name']; ?></span>
                                    <span>(<?php echo $category['post_count']; ?>)</span>
                                </a>
                            </li>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <!-- Recent Posts -->
                <div class="sidebar-section">
                    <h3 style="color: var(--color-burgundy); margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #eee;">Recent Posts</h3>
                    <?php if ($recent_posts && $recent_posts->num_rows > 0): ?>
                        <?php while ($post = $recent_posts->fetch_assoc()): ?>
                        <div class="recent-post" style="display: flex; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #f5f5f5;">
                            <div class="recent-post-image" style="flex: 0 0 80px; height: 60px; background-image: url('../<?php echo !empty($post['featured_image']) ? $post['featured_image'] : 'images/blog/default.jpg'; ?>'); background-size: cover; background-position: center; margin-right: 15px; border-radius: 5px;"></div>
                            <div>
                                <h4 style="font-size: 14px; margin-bottom: 5px;"><a href="blog-detail.php?slug=<?php echo $post['slug']; ?>" style="color: var(--color-burgundy); text-decoration: none;"><?php echo $post['title']; ?></a></h4>
                                <span style="font-size: 12px; color: #999;"><?php echo formatDate($post['publish_date']); ?></span>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                    <p>No recent posts found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="section newsletter-section" style="background-color: var(--color-cream);">
    <div class="container">
        <div class="newsletter-container" data-aos="fade-up" style="max-width: 600px; margin: 0 auto; text-align: center;">
            <h2 class="section-title">Subscribe to Our Blog</h2>
            <p class="section-subtitle">Get the latest articles and immigration tips delivered straight to your inbox.</p>
            
            <form class="newsletter-form" style="margin-top: 30px;">
                <div style="display: flex; gap: 10px;">
                    <input type="email" placeholder="Enter your email address" style="flex: 1; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                    <button type="submit" class="btn btn-primary">Subscribe</button>
                </div>
            </form>
        </div>
    </div>
</section>

<?php include('../includes/footer.php'); ?>
