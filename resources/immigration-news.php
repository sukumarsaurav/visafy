<?php
$page_title = "Immigration News | CANEXT Immigration";
include('../includes/header.php');
include('../admin/includes/db_connection.php');

// Get published news articles
$sql = "SELECT * FROM news_articles WHERE status = 'published' ORDER BY publish_date DESC LIMIT 12";
$result = executeQuery($sql);
?>

<!-- Page Header -->
<section class="page-header" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('../images/resources/news-header.jpg'); background-size: cover; background-position: center; padding: 100px 0; color: var(--color-light); text-align: center;background-color: var(--color-burgundy);">
<div style="position: absolute; width: 300px; height: 300px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); top: -100px; left: -100px;"></div>
    <div style="position: absolute; width: 200px; height: 200px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); bottom: -50px; right: 10%; animation: pulse 4s infinite alternate;"></div>
    <div style="position: absolute; width: 100px; height: 100px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); top: 30%; right: 20%; animation: pulse 3s infinite alternate;"></div>
    <div class="container">
        <h1 data-aos="fade-up">Immigration News</h1>
        <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <ol class="breadcrumb" style="display: flex; justify-content: center; list-style: none; padding: 0; margin: 20px 0 0 0;">
                <li class="breadcrumb-item"><a href="../index.php" style="color: var(--color-cream);">Home</a></li>
                <li class="breadcrumb-item"><a href="../resources.php" style="color: var(--color-cream);">Resources</a></li>
                <li class="breadcrumb-item active" aria-current="page" style="color: var(--color-light);">Immigration News</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Latest News Section -->
<section class="section news-section">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up">Latest Immigration Updates</h2>
        <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">Stay informed about the latest changes and developments in Canadian immigration policies and programs.</p>
        
        <div class="news-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin-top: 40px;">
            <?php 
            if ($result && $result->num_rows > 0):
                $delay = 0;
                while ($article = $result->fetch_assoc()):
                    $delay += 100;
                    $image_url = $article['image'] ? '../images/news/' . $article['image'] : '../images/resources/news-default.jpg';
            ?>
                <!-- News Item -->
                <article class="news-card" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>" style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                    <div class="news-image" style="height: 200px; background-image: url('<?php echo $image_url; ?>'); background-size: cover; background-position: center;"></div>
                    <div class="news-content" style="padding: 20px;">
                        <div class="news-date" style="color: var(--color-burgundy); font-size: 0.9rem; margin-bottom: 10px;"><?php echo date('F j, Y', strtotime($article['publish_date'])); ?></div>
                        <h3 style="color: var(--color-burgundy); margin-bottom: 15px;"><?php echo $article['title']; ?></h3>
                        <p><?php echo $article['excerpt']; ?></p>
                        <a href="../immigration-news/<?php echo $article['slug']; ?>" class="btn btn-secondary" style="margin-top: 15px;">Read More</a>
                    </div>
                </article>
            <?php 
                endwhile;
            else:
            ?>
                <div class="no-articles" style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                    <p>No news articles available at the moment. Please check back later.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="section newsletter-section">
    <div class="container">
        <div class="newsletter-container" data-aos="fade-up" style="max-width: 600px; margin: 0 auto; text-align: center;">
            <h2 class="section-title">Stay Updated</h2>
            <p class="section-subtitle">Subscribe to our newsletter to receive the latest immigration news and updates directly in your inbox.</p>
            
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

