<?php
$page_title = "Frequently Asked Questions | CANEXT Immigration";
include('../includes/header.php');
include('../admin/includes/db_connection.php');

// Get all FAQ categories
$categories_sql = "SELECT * FROM faq_categories ORDER BY display_order ASC";
$categories_result = executeQuery($categories_sql);

// Get active category (default to first category)
$active_category_id = isset($_GET['category']) ? intval($_GET['category']) : 0;

// If no category is specified, use the first one
if ($active_category_id === 0 && $categories_result && $categories_result->num_rows > 0) {
    $first_category = $categories_result->fetch_assoc();
    $active_category_id = $first_category['id'];
    // Reset the result pointer
    $categories_result->data_seek(0);
}
?>

<section class="page-header" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('../images/resources/faq-header.jpg'); background-size: cover; background-position: center; padding: 100px 0; color: var(--color-light); text-align: center;background-color: var(--color-burgundy);">
<div style="position: absolute; width: 300px; height: 300px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); top: -100px; left: -100px;"></div>
    <div style="position: absolute; width: 200px; height: 200px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); bottom: -50px; right: 10%; animation: pulse 4s infinite alternate;"></div>
    <div style="position: absolute; width: 100px; height: 100px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); top: 30%; right: 20%; animation: pulse 3s infinite alternate;"></div>
    <div class="container">
        <h1 data-aos="fade-up">Frequently Asked Questions</h1>
        <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <ol class="breadcrumb" style="display: flex; justify-content: center; list-style: none; padding: 0; margin: 20px 0 0 0;">
                <li class="breadcrumb-item"><a href="../index.php" style="color: var(--color-cream);">Home</a></li>
                <li class="breadcrumb-item"><a href="../resources.php" style="color: var(--color-cream);">Resources</a></li>
                <li class="breadcrumb-item active" aria-current="page" style="color: var(--color-light);">FAQ</li>
            </ol>
        </nav>
    </div>
</section>

<section class="faq-section section-padding">
    <div class="container">
        <!-- Mobile Category Navigation - Icon Based -->
        <div class="mobile-category-nav">
            <div class="category-icons-container">
                <?php 
                $categories_result->data_seek(0);
                if ($categories_result && $categories_result->num_rows > 0) {
                    while ($category = $categories_result->fetch_assoc()) {
                        $isActive = $category['id'] == $active_category_id ? 'active' : '';
                        echo '<div class="category-icon-item ' . $isActive . '" data-category-id="' . $category['id'] . '">';
                        echo '<div class="icon-circle"><i class="' . $category['icon'] . '"></i></div>';
                        echo '<span class="icon-label">' . $category['name'] . '</span>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>

        <div class="faq-container">
            <!-- FAQ Category Sidebar -->
            <div class="faq-sidebar">
                <div class="sticky-sidebar">
                   
                    <ul class="category-list">
                        <?php 
                        $categories_result->data_seek(0);
                        if ($categories_result && $categories_result->num_rows > 0) {
                            while ($category = $categories_result->fetch_assoc()) {
                                $isActive = $category['id'] == $active_category_id ? 'active' : '';
                                echo '<li class="category-item ' . $isActive . '" data-category-id="' . $category['id'] . '">';
                                echo '<i class="' . $category['icon'] . '"></i>';
                                echo '<span class="category-name">' . $category['name'] . '</span>';
                                echo '</li>';
                            }
                        }
                        ?>
                    </ul>
                </div>
            </div>
            
            <!-- FAQ Content Area -->
            <div class="faq-content">
                <?php
                // Get all FAQ categories for content
                $categories_sql = "SELECT * FROM faq_categories ORDER BY display_order ASC";
                $categories_content = executeQuery($categories_sql);
                
                if ($categories_content && $categories_content->num_rows > 0) {
                    while ($category = $categories_content->fetch_assoc()) {
                        $category_id = $category['id'];
                        $display = $category_id == $active_category_id ? 'block' : 'none';
                        
                        echo '<div class="faq-category-content" id="category-' . $category_id . '" style="display: ' . $display . ';">';
                        echo '<h2 class="category-title">' . $category['name'] . '</h2>';
                        
                        // Get questions for this category
                        $questions_sql = "SELECT * FROM faq_items WHERE category_id = $category_id ORDER BY display_order ASC";
                        $questions_result = executeQuery($questions_sql);
                        
                        if ($questions_result && $questions_result->num_rows > 0) {
                            echo '<div class="faq-items">';
                            while ($question = $questions_result->fetch_assoc()) {
                                echo '<div class="faq-item">';
                                echo '<div class="faq-question">';
                                echo $question['question'];
                                echo '<span class="toggle-icon"><i class="fas fa-chevron-down"></i></span>';
                                echo '</div>';
                                echo '<div class="faq-answer">' . $question['answer'] . '</div>';
                                echo '</div>';
                            }
                            echo '</div>';
                        } else {
                            echo '<p>No FAQs found for this category.</p>';
                        }
                        
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
</section>

<section class="cta-section" data-aos="fade-up">
    <div class="container">
        <h2 class="section-title text-white">Still Have Questions?</h2>
        <p class="section-subtitle">Our immigration experts are here to help you with personalized answers.</p>
        <div class="cta-buttons">
            <a href="../contact.php" class="btn btn-light">Contact Us</a>
            <a href="../appointment.php" class="btn btn-outline-light">Book a Consultation</a>
        </div>
    </div>
</section>


<!-- Add JavaScript for FAQ interactions -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // FAQ item click handling
    const faqItems = document.querySelectorAll('.faq-item');
    
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        
        question.addEventListener('click', () => {
            // Toggle current item
            item.classList.toggle('active');
        });
    });
    
    // Category selection handling - desktop
    const categoryItems = document.querySelectorAll('.category-item');
    
    categoryItems.forEach(item => {
        item.addEventListener('click', () => {
            const categoryId = item.getAttribute('data-category-id');
            updateActiveCategory(categoryId);
        });
    });
    
    // Category selection handling - mobile icons
    const categoryIconItems = document.querySelectorAll('.category-icon-item');
    
    categoryIconItems.forEach(item => {
        item.addEventListener('click', () => {
            const categoryId = item.getAttribute('data-category-id');
            updateActiveCategory(categoryId);
        });
    });
    
    // Function to update active category
    function updateActiveCategory(categoryId) {
        // Update desktop sidebar
        document.querySelectorAll('.category-item').forEach(item => {
            item.classList.remove('active');
            if (item.getAttribute('data-category-id') === categoryId) {
                item.classList.add('active');
            }
        });
        
        // Update mobile icons
        document.querySelectorAll('.category-icon-item').forEach(item => {
            item.classList.remove('active');
            if (item.getAttribute('data-category-id') === categoryId) {
                item.classList.add('active');
            }
        });
        
        // Show selected category content
        document.querySelectorAll('.faq-category-content').forEach(content => {
            content.style.display = 'none';
        });
        
        const selectedContent = document.getElementById('category-' + categoryId);
        if (selectedContent) {
            selectedContent.style.display = 'block';
            
            // Scroll to content on mobile
            if (window.innerWidth < 768) {
                selectedContent.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    }
});
</script>

<?php include('../includes/footer.php'); ?>
