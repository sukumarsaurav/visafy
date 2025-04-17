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

<section class="consultant-hero bg-light py-5">
    <div class="container">
        <h1 class="text-center mb-4">Find an Immigration Consultant</h1>
        <p class="text-center lead mb-5">Connect with experienced and verified immigration consultants who can help you with your immigration journey.</p>
        
        <!-- Search and Filters Section -->
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <form action="" method="GET" class="row g-3">
                    <div class="col-md-6">
                        <label for="search" class="form-label">Search by name, specialty, or description</label>
                        <input type="text" class="form-control" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="E.g., Express Entry, Student Visa">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="specialty" class="form-label">Specialty</label>
                        <select class="form-select" id="specialty" name="specialty">
                            <option value="">All Specialties</option>
                            <?php foreach ($specialties as $spec): ?>
                                <option value="<?php echo htmlspecialchars($spec); ?>" <?php echo $specialty === $spec ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($spec); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="language" class="form-label">Language</label>
                        <select class="form-select" id="language" name="language">
                            <option value="">All Languages</option>
                            <?php foreach ($languages as $lang): ?>
                                <option value="<?php echo htmlspecialchars($lang); ?>" <?php echo $language === $lang ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($lang); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="min_price" class="form-label">Min. Price (C$)</label>
                        <input type="number" class="form-control" id="min_price" name="min_price" value="<?php echo htmlspecialchars($min_price ?? ''); ?>" placeholder="Min price">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="max_price" class="form-label">Max. Price (C$)</label>
                        <input type="number" class="form-control" id="max_price" name="max_price" value="<?php echo htmlspecialchars($max_price ?? ''); ?>" placeholder="Max price">
                    </div>
                    
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Apply Filters</button>
                        <a href="consultant.php" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Results Section -->
        <div class="row">
            <?php if (empty($consultants)): ?>
                <div class="col-12 text-center py-5">
                    <div class="alert alert-info">
                        <h4 class="alert-heading">No consultants found</h4>
                        <p>Sorry, we couldn't find any consultants matching your criteria. Try adjusting your filters or search terms.</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($consultants as $consultant): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm <?php echo $consultant['is_featured'] ? 'border-primary' : ''; ?>">
                            <?php if ($consultant['is_featured']): ?>
                                <div class="position-absolute top-0 end-0 badge bg-primary m-2">Featured</div>
                            <?php endif; ?>
                            
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <img src="<?php echo !empty($consultant['profile_image']) ? htmlspecialchars($consultant['profile_image']) : 'images/default-profile.jpg'; ?>" 
                                         alt="<?php echo htmlspecialchars($consultant['name']); ?>" 
                                         class="rounded-circle mb-2" 
                                         style="width: 100px; height: 100px; object-fit: cover;">
                                    <h5 class="card-title"><?php echo htmlspecialchars($consultant['name']); ?></h5>
                                    <p class="text-muted small mb-2"><?php echo htmlspecialchars($consultant['license_number']); ?></p>
                                    
                                    <div class="mb-2">
                                        <span class="badge bg-primary"><?php echo htmlspecialchars($consultant['years_experience']); ?> years exp</span>
                                        <?php if ($consultant['rating']): ?>
                                            <span class="badge bg-success">
                                                <i class="fas fa-star small"></i> <?php echo number_format($consultant['rating'], 1); ?>
                                                (<?php echo $consultant['reviews_count']; ?>)
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <h6 class="card-subtitle mb-1">Specialties:</h6>
                                    <p class="card-text small">
                                        <?php 
                                        $specs = explode(',', $consultant['specializations']); 
                                        foreach ($specs as $index => $spec): 
                                            echo htmlspecialchars(trim($spec));
                                            if ($index < count($specs) - 1) echo ', ';
                                        endforeach; 
                                        ?>
                                    </p>
                                </div>
                                
                                <div class="mb-3">
                                    <h6 class="card-subtitle mb-1">Languages:</h6>
                                    <p class="card-text small">
                                        <?php 
                                        $langs = explode(',', $consultant['languages']); 
                                        foreach ($langs as $index => $lang): 
                                            echo htmlspecialchars(trim($lang));
                                            if ($index < count($langs) - 1) echo ', ';
                                        endforeach; 
                                        ?>
                                    </p>
                                </div>
                                
                                <div class="mb-3">
                                    <h6 class="card-subtitle mb-1">Consultation Fees:</h6>
                                    <ul class="list-unstyled small">
                                        <?php if (isset($consultation_fees[$consultant['user_id']])): ?>
                                            <?php if (isset($consultation_fees[$consultant['user_id']]['video'])): ?>
                                                <li>Video: C$<?php echo number_format($consultation_fees[$consultant['user_id']]['video'], 2); ?></li>
                                            <?php endif; ?>
                                            
                                            <?php if (isset($consultation_fees[$consultant['user_id']]['phone'])): ?>
                                                <li>Phone: C$<?php echo number_format($consultation_fees[$consultant['user_id']]['phone'], 2); ?></li>
                                            <?php endif; ?>
                                            
                                            <?php if (isset($consultation_fees[$consultant['user_id']]['inperson'])): ?>
                                                <li>In-person: C$<?php echo number_format($consultation_fees[$consultant['user_id']]['inperson'], 2); ?></li>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <li>Contact for pricing</li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="card-footer bg-white border-top-0 text-center">
                                <a href="consultant-profile.php?id=<?php echo $consultant['id']; ?>" class="btn btn-primary">View Profile</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?>

