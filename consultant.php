<?php
session_start();
require_once 'config/database.php';

// Get search parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$specialty = isset($_GET['specialty']) ? trim($_GET['specialty']) : '';
$language = isset($_GET['language']) ? trim($_GET['language']) : '';
$min_price = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (float)$_GET['min_price'] : null;
$max_price = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (float)$_GET['max_price'] : null;

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

if (!empty($specialty)) {
    $query .= " AND p.specializations LIKE ?";
    array_push($params, "%$specialty%");
    $types .= "s";
}

if (!empty($language)) {
    $query .= " AND p.languages LIKE ?";
    array_push($params, "%$language%");
    $types .= "s";
}

// Add price filtering from consultation_fees
if ($min_price !== null || $max_price !== null) {
    $query .= " AND EXISTS (SELECT 1 FROM consultation_fees cf WHERE cf.professional_id = p.user_id";
    
    if ($min_price !== null) {
        $query .= " AND cf.fee >= ?";
        array_push($params, $min_price);
        $types .= "d";
    }
    
    if ($max_price !== null) {
        $query .= " AND cf.fee <= ?";
        array_push($params, $max_price);
        $types .= "d";
    }
    
    $query .= ")";
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

// Extract unique specialties and languages for filter options
$specialties = [];
$languages = [];

foreach ($consultants as $consultant) {
    $consultant_specialties = explode(',', $consultant['specializations']);
    $consultant_languages = explode(',', $consultant['languages']);
    
    foreach ($consultant_specialties as $spec) {
        $spec = trim($spec);
        if (!empty($spec) && !in_array($spec, $specialties)) {
            $specialties[] = $spec;
        }
    }
    
    foreach ($consultant_languages as $lang) {
        $lang = trim($lang);
        if (!empty($lang) && !in_array($lang, $languages)) {
            $languages[] = $lang;
        }
    }
}

// Sort filter options alphabetically
sort($specialties);
sort($languages);

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

<section class="consultant-hero">
    <div class="container">
        <h1 class="text-center">Find an Immigration Consultant</h1>
        <p class="text-center lead mb-5">Connect with experienced and verified immigration consultants who can help you with your immigration journey.</p>
        
        <!-- Modern Search Bar -->
        <div class="search-container">
            <form action="" method="GET">
                <input type="text" name="search" placeholder="Search for consultants by name, specialty, or description..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit"><i class="fas fa-search"></i> Search</button>
            </form>
        </div>
        
        <!-- Advanced Filters (Hidden by Default) -->
        <div class="filters-section" id="advancedFilters" style="<?php echo (!empty($specialty) || !empty($language) || $min_price !== null || $max_price !== null) ? 'display:block' : 'display:none'; ?>">
            <form action="" method="GET" class="filter-form">
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                
                <div class="form-group">
                    <label for="specialty">Specialty</label>
                    <select class="form-control" id="specialty" name="specialty">
                        <option value="">All Specialties</option>
                        <?php foreach ($specialties as $spec): ?>
                            <option value="<?php echo htmlspecialchars($spec); ?>" <?php echo $specialty === $spec ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($spec); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="language">Language</label>
                    <select class="form-control" id="language" name="language">
                        <option value="">All Languages</option>
                        <?php foreach ($languages as $lang): ?>
                            <option value="<?php echo htmlspecialchars($lang); ?>" <?php echo $language === $lang ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($lang); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="min_price">Min. Price (C$)</label>
                    <input type="number" class="form-control" id="min_price" name="min_price" value="<?php echo htmlspecialchars($min_price ?? ''); ?>" placeholder="Min price">
                </div>
                
                <div class="form-group">
                    <label for="max_price">Max. Price (C$)</label>
                    <input type="number" class="form-control" id="max_price" name="max_price" value="<?php echo htmlspecialchars($max_price ?? ''); ?>" placeholder="Max price">
                </div>
                
                <div class="filter-buttons">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="consultant.php" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
        
        <div class="text-center mb-4">
            <button id="toggleFilters" class="btn btn-sm btn-outline-secondary">
                <span id="filterButtonText"><?php echo (!empty($specialty) || !empty($language) || $min_price !== null || $max_price !== null) ? 'Hide Advanced Filters' : 'Show Advanced Filters'; ?></span>
            </button>
        </div>
        
        <!-- Results Section -->
        <?php if (empty($consultants)): ?>
            <div class="no-results">
                <h4>No consultants found</h4>
                <p>Sorry, we couldn't find any consultants matching your criteria. Try adjusting your filters or search terms.</p>
            </div>
        <?php else: ?>
            <div class="consultant-grid">
                <?php foreach ($consultants as $consultant): ?>
                    <div class="consultant-card <?php echo $consultant['is_featured'] ? 'featured' : ''; ?>">
                        <?php if ($consultant['is_featured']): ?>
                            <div class="featured-badge">Featured</div>
                        <?php endif; ?>
                        
                        <div class="consultant-header">
                            <img src="<?php echo !empty($consultant['profile_image']) ? htmlspecialchars($consultant['profile_image']) : 'assets/images/logo-Visafy-light.png'; ?>" 
                                 alt="<?php echo htmlspecialchars($consultant['name']); ?>" 
                                 class="consultant-image">
                                 
                            <div class="consultant-info">
                                <h3 class="consultant-name"><?php echo htmlspecialchars($consultant['name']); ?></h3>
                                <div class="consultant-license"><?php echo htmlspecialchars($consultant['license_number']); ?></div>
                                
                                <div class="consultant-badges">
                                    <span class="experience-badge">
                                        <i class="fas fa-briefcase"></i> <?php echo htmlspecialchars($consultant['years_experience']); ?> years
                                    </span>
                                    
                                    <?php if ($consultant['rating']): ?>
                                        <span class="rating-badge">
                                            <i class="fas fa-star"></i> <?php echo number_format($consultant['rating'], 1); ?>
                                            (<?php echo $consultant['reviews_count']; ?>)
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="consultant-body">
                            <div class="section-title">Specialties</div>
                            <div class="specialty-tabs">
                                <?php 
                                $specs = explode(',', $consultant['specializations']); 
                                foreach ($specs as $spec): 
                                    $spec = trim($spec);
                                    if (!empty($spec)):
                                ?>
                                    <span class="specialty-tag"><?php echo htmlspecialchars($spec); ?></span>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </div>
                            
                            <div class="section-title">Languages</div>
                            <div class="language-tabs">
                                <?php 
                                $langs = explode(',', $consultant['languages']); 
                                foreach ($langs as $lang): 
                                    $lang = trim($lang);
                                    if (!empty($lang)):
                                ?>
                                    <span class="language-tag"><?php echo htmlspecialchars($lang); ?></span>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </div>
                            
                            <div class="section-title">Consultation Options</div>
                            <div class="consultation-modes">
                                <?php if (isset($consultation_fees[$consultant['user_id']])): ?>
                                    <?php if (isset($consultation_fees[$consultant['user_id']]['video'])): ?>
                                        <div class="mode-item">
                                            <div class="mode-icon"><i class="fas fa-video"></i></div>
                                            <span>Video: C$<?php echo number_format($consultation_fees[$consultant['user_id']]['video'], 2); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($consultation_fees[$consultant['user_id']]['phone'])): ?>
                                        <div class="mode-item">
                                            <div class="mode-icon"><i class="fas fa-phone"></i></div>
                                            <span>Phone: C$<?php echo number_format($consultation_fees[$consultant['user_id']]['phone'], 2); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($consultation_fees[$consultant['user_id']]['inperson'])): ?>
                                        <div class="mode-item">
                                            <div class="mode-icon"><i class="fas fa-user"></i></div>
                                            <span>In-person: C$<?php echo number_format($consultation_fees[$consultant['user_id']]['inperson'], 2); ?></span>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="mode-item">
                                        <div class="mode-icon"><i class="fas fa-info-circle"></i></div>
                                        <span>Contact for pricing</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="consultant-footer">
                            <a href="consultant-profile.php?id=<?php echo $consultant['id']; ?>" class="view-profile-btn">View Profile</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- JavaScript for filter toggle -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleButton = document.getElementById('toggleFilters');
    const filtersSection = document.getElementById('advancedFilters');
    const filterButtonText = document.getElementById('filterButtonText');
    
    if (toggleButton && filtersSection && filterButtonText) {
        toggleButton.addEventListener('click', function() {
            if (filtersSection.style.display === 'none') {
                filtersSection.style.display = 'block';
                filterButtonText.textContent = 'Hide Advanced Filters';
            } else {
                filtersSection.style.display = 'none';
                filterButtonText.textContent = 'Show Advanced Filters';
            }
        });
    }
});
</script>

<?php include('includes/footer.php'); ?>

