<?php
session_start();
require_once 'config/database.php';

// Get search parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Base query to get professionals
$query = "SELECT p.*, u.name, u.email 
          FROM professionals p 
          JOIN users u ON p.user_id = u.id 
          WHERE u.user_type = 'professional' 
          AND u.status = 'active' 
          AND p.is_verified = 1";

$params = [];
$types = "";

// Add search conditions
if (!empty($search)) {
    $query .= " AND (u.name LIKE ? OR p.specializations LIKE ? OR p.bio LIKE ?)";
    $search_param = "%$search%";
    array_push($params, $search_param, $search_param, $search_param);
    $types .= "sss";
}

$query .= " ORDER BY p.is_featured DESC, p.rating DESC";

// Execute the query with parameters if needed
if (!empty($params)) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($query);
}

// Fetch all consultants
$consultants = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $consultants[] = $row;
    }
}

// Get consultation fees for each professional
$consultation_fees = [];
$fee_query = "SELECT professional_id, consultation_type, fee FROM consultation_fees";
$fee_result = $conn->query($fee_query);

if ($fee_result && $fee_result->num_rows > 0) {
    while ($row = $fee_result->fetch_assoc()) {
        $consultation_fees[$row['professional_id']][$row['consultation_type']] = $row['fee'];
    }
}

$page_title = "Find a Consultant | Visafy";
include('includes/header.php');
?>

<link rel="stylesheet" href="assets/css/consultant.css">

<!-- Page Header -->
<section class="page-header" style="background-color: var(--color-burgundy); padding: 100px 0; color: var(--color-light); text-align: center;">
    <div style="position: absolute; width: 300px; height: 300px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); top: -100px; left: -100px;"></div>
    <div style="position: absolute; width: 200px; height: 200px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); bottom: -50px; right: 10%; animation: pulse 4s infinite alternate;"></div>
    <div style="position: absolute; width: 100px; height: 100px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); top: 30%; right: 20%; animation: pulse 3s infinite alternate;"></div>
    <div class="container">
        <h1 data-aos="fade-up">Find an Immigration Consultant</h1>
        <p class="lead" data-aos="fade-up" data-aos-delay="100">Connect with experienced and verified immigration professionals who can help you with your immigration journey.</p>
        
        <!-- Modern Search Bar -->
        <div class="search-container" data-aos="fade-up" data-aos-delay="200" style="max-width: 700px; margin: 2rem auto 0;">
            <form action="" method="GET">
                <input type="text" name="search" placeholder="Search for consultants by name, specialty, or description..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>
        
        <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="150">
            <ol class="breadcrumb" style="display: flex; justify-content: center; list-style: none; padding: 0; margin: 20px 0 0 0;">
          
            </ol>
        </nav>
    </div>
</section>

<section class="section" style="background-color: var(--color-cream); padding: 50px 0;">
    <div class="container">
        <!-- Results Section -->
        <?php if (empty($consultants)): ?>
            <div class="no-results" style="text-align: center; padding: 50px; background-color: white; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);" data-aos="fade-up">
                <div style="font-size: 4rem; color: var(--color-secondary); margin-bottom: 20px;">
                    <i class="fas fa-search"></i>
                </div>
                <h3 style="color: var(--color-primary); margin-bottom: 15px;">No consultants found</h3>
                <p style="color: var(--color-gray); max-width: 500px; margin: 0 auto;">Sorry, we couldn't find any consultants matching your criteria. Try adjusting your search term.</p>
            </div>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(500px, 1fr)); gap: 30px;">
                <?php foreach ($consultants as $consultant): ?>
                    <a href="consultant-profile.php?id=<?php echo $consultant['id']; ?>" style="text-decoration: none; color: inherit;">
                        <div class="consultant-card" style="display: flex; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); transition: all 0.3s ease; height: 100%; cursor: pointer; border-left: <?php echo $consultant['is_featured'] ? '4px solid var(--color-secondary)' : 'none'; ?>;" data-aos="fade-up">
                            <?php if ($consultant['is_featured']): ?>
                                <div style="position: absolute; top: 15px; right: 15px; background-color: var(--color-secondary); color: white; padding: 5px 10px; font-size: 0.75rem; border-radius: 20px; font-weight: 600; z-index: 1;">Featured</div>
                            <?php endif; ?>
                            
                            <div style="padding: 30px; display: flex; width: 100%; flex-direction: column;">
                                <div style="display: flex; margin-bottom: 20px;">
                                    <div style="flex: 0 0 100px;">
                                        <img src="<?php echo !empty($consultant['profile_image']) ? htmlspecialchars($consultant['profile_image']) : 'assets/images/logo-Visafy-light.png'; ?>" 
                                             alt="<?php echo htmlspecialchars($consultant['name']); ?>" 
                                             style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                                    </div>
                                    
                                    <div style="flex: 1; padding-left: 25px;">
                                        <h3 style="color: var(--color-primary); font-size: 1.4rem; margin-bottom: 5px;"><?php echo htmlspecialchars($consultant['name']); ?></h3>
                                        <div style="color: var(--color-gray); font-size: 0.9rem; margin-bottom: 10px;"><?php echo htmlspecialchars($consultant['license_number']); ?></div>
                                        
                                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                            <span style="background-color: rgba(4, 33, 103, 0.1); color: var(--color-primary); padding: 5px 10px; border-radius: 20px; font-size: 0.8rem; display: inline-flex; align-items: center;">
                                                <i class="fas fa-briefcase" style="margin-right: 5px;"></i> <?php echo htmlspecialchars($consultant['years_experience']); ?> years
                                            </span>
                                            
                                            <?php if ($consultant['rating']): ?>
                                                <span style="background-color: rgba(234, 170, 52, 0.1); color: var(--color-secondary); padding: 5px 10px; border-radius: 20px; font-size: 0.8rem; display: inline-flex; align-items: center;">
                                                    <i class="fas fa-star" style="margin-right: 5px;"></i> <?php echo number_format($consultant['rating'], 1); ?>
                                                    (<?php echo $consultant['reviews_count']; ?>)
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div style="margin-bottom: 15px;">
                                    <div style="font-weight: 600; font-size: 0.85rem; margin-bottom: 5px; color: var(--color-dark); display: flex; align-items: center;">
                                        <i class="fas fa-clipboard-list" style="margin-right: 8px; color: var(--color-primary);"></i> Specialties
                                    </div>
                                    <div style="display: flex; flex-wrap: wrap; gap: 5px;">
                                        <?php 
                                        $specs = explode(',', $consultant['specializations']); 
                                        foreach ($specs as $spec): 
                                            $spec = trim($spec);
                                            if (!empty($spec)):
                                        ?>
                                            <span style="background-color: rgba(4, 33, 103, 0.08); color: var(--color-primary); padding: 4px 12px; border-radius: 20px; font-size: 0.8rem;"><?php echo htmlspecialchars($spec); ?></span>
                                        <?php 
                                            endif;
                                        endforeach; 
                                        ?>
                                    </div>
                                </div>
                                
                                <div style="margin-bottom: 15px;">
                                    <div style="font-weight: 600; font-size: 0.85rem; margin-bottom: 5px; color: var(--color-dark); display: flex; align-items: center;">
                                        <i class="fas fa-globe" style="margin-right: 8px; color: var(--color-secondary);"></i> Languages
                                    </div>
                                    <div style="display: flex; flex-wrap: wrap; gap: 5px;">
                                        <?php 
                                        $langs = explode(',', $consultant['languages']); 
                                        foreach ($langs as $lang): 
                                            $lang = trim($lang);
                                            if (!empty($lang)):
                                        ?>
                                            <span style="background-color: rgba(234, 170, 52, 0.08); color: var(--color-secondary); padding: 4px 12px; border-radius: 20px; font-size: 0.8rem;"><?php echo htmlspecialchars($lang); ?></span>
                                        <?php 
                                            endif;
                                        endforeach; 
                                        ?>
                                    </div>
                                </div>
                                
                                <div>
                                    <div style="font-weight: 600; font-size: 0.85rem; margin-bottom: 5px; color: var(--color-dark); display: flex; align-items: center;">
                                        <i class="fas fa-comments" style="margin-right: 8px; color: var(--color-primary);"></i> Consultation Options
                                    </div>
                                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                                        <?php if (isset($consultation_fees[$consultant['user_id']])): ?>
                                            <?php if (isset($consultation_fees[$consultant['user_id']]['video'])): ?>
                                                <span style="background-color: rgba(4, 33, 103, 0.08); color: var(--color-primary); width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center;" title="Video">
                                                    <i class="fas fa-video"></i>
                                                </span>
                                            <?php endif; ?>
                                            
                                            <?php if (isset($consultation_fees[$consultant['user_id']]['phone'])): ?>
                                                <span style="background-color: rgba(4, 33, 103, 0.08); color: var(--color-primary); width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center;" title="Phone">
                                                    <i class="fas fa-phone"></i>
                                                </span>
                                            <?php endif; ?>
                                            
                                            <?php if (isset($consultation_fees[$consultant['user_id']]['inperson'])): ?>
                                                <span style="background-color: rgba(4, 33, 103, 0.08); color: var(--color-primary); width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center;" title="In-person">
                                                    <i class="fas fa-user"></i>
                                                </span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span style="background-color: rgba(4, 33, 103, 0.08); color: var(--color-primary); padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; display: inline-flex; align-items: center;">
                                                <i class="fas fa-info-circle" style="margin-right: 5px;"></i> Contact for options
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize AOS
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            once: true
        });
    }
});
</script>

<?php include('includes/footer.php'); ?>

