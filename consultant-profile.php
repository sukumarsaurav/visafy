<?php
session_start();
require_once 'config/database.php';
$pageTitle = "Consultant Profile | Visafy";
include 'includes/header.php';

// Get consultant ID from URL
$consultant_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Redirect if no valid ID provided
if ($consultant_id <= 0) {
    header("Location: consultant.php");
    exit();
}

// Fetch consultant details
$stmt = $conn->prepare("SELECT p.*, u.email, u.profile_image FROM professionals p 
                        JOIN users u ON p.user_id = u.id
                        WHERE p.id = ? AND p.is_active = 1 AND p.is_verified = 1");
$stmt->bind_param("i", $consultant_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Consultant not found or not active
    header("Location: consultant.php");
    exit();
}

$consultant = $result->fetch_assoc();

// Fetch specialties
$stmt = $conn->prepare("SELECT specialty FROM professional_specialties WHERE professional_id = ?");
$stmt->bind_param("i", $consultant_id);
$stmt->execute();
$specialtyResult = $stmt->get_result();
$specialties = [];
while ($row = $specialtyResult->fetch_assoc()) {
    $specialties[] = $row['specialty'];
}

// Fetch languages
$stmt = $conn->prepare("SELECT language FROM professional_languages WHERE professional_id = ?");
$stmt->bind_param("i", $consultant_id);
$stmt->execute();
$languageResult = $stmt->get_result();
$languages = [];
while ($row = $languageResult->fetch_assoc()) {
    $languages[] = $row['language'];
}

// Fetch reviews
$stmt = $conn->prepare("SELECT r.*, u.first_name, u.last_name FROM reviews r
                        JOIN users u ON r.user_id = u.id
                        WHERE r.professional_id = ? ORDER BY r.created_at DESC");
$stmt->bind_param("i", $consultant_id);
$stmt->execute();
$reviewsResult = $stmt->get_result();
$reviews = [];
$totalRating = 0;
$reviewCount = 0;

while ($row = $reviewsResult->fetch_assoc()) {
    $reviews[] = $row;
    $totalRating += $row['rating'];
    $reviewCount++;
}

$avgRating = $reviewCount > 0 ? round($totalRating / $reviewCount, 1) : 0;

// Format profile image path
$profileImage = !empty($consultant['profile_image']) ? $base . '/uploads/profile/' . $consultant['profile_image'] : $base . '/assets/images/default-avatar.png';

// Handle booking form submission
$bookingSuccess = false;
$bookingError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_consultation'])) {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        $bookingError = 'Please login to book a consultation.';
    } else {
        $userId = $_SESSION['user_id'];
        $date = $_POST['consultation_date'];
        $time = $_POST['consultation_time'];
        $message = $conn->real_escape_string($_POST['consultation_message']);
        $type = $conn->real_escape_string($_POST['consultation_type']);
        
        $datetime = $date . ' ' . $time . ':00';
        
        // Insert booking
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, professional_id, booking_datetime, message, consultation_type, status, created_at) 
                                VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
        $stmt->bind_param("iisss", $userId, $consultant_id, $datetime, $message, $type);
        
        if ($stmt->execute()) {
            $bookingSuccess = true;
        } else {
            $bookingError = 'Failed to book consultation. Please try again.';
        }
    }
}
?>

<div class="container my-5">
    <div class="row">
        <!-- Consultant Profile -->
        <div class="col-lg-8">
            <div class="card consultant-profile-card mb-4">
                <div class="card-body">
                    <div class="d-flex flex-column flex-md-row">
                        <div class="consultant-profile-photo">
                            <img src="<?php echo $profileImage; ?>" alt="<?php echo htmlspecialchars($consultant['first_name'] . ' ' . $consultant['last_name']); ?>" class="img-fluid rounded-circle">
                        </div>
                        <div class="consultant-profile-info ms-md-4 mt-3 mt-md-0">
                            <h1><?php echo htmlspecialchars($consultant['first_name'] . ' ' . $consultant['last_name']); ?></h1>
                            
                            <div class="d-flex align-items-center mb-2">
                                <div class="me-3">
                                    <i class="fas fa-star text-warning"></i>
                                    <span><?php echo $avgRating; ?> (<?php echo $reviewCount; ?> reviews)</span>
                                </div>
                                <div>
                                    <i class="fas fa-briefcase"></i>
                                    <span><?php echo htmlspecialchars($consultant['experience']); ?> years experience</span>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <strong>Consultation Fee:</strong> $<?php echo number_format($consultant['consultation_fee'], 2); ?> per hour
                            </div>
                            
                            <div class="mb-3">
                                <h5>Specialties</h5>
                                <div class="specialty-badges">
                                    <?php foreach ($specialties as $specialty): ?>
                                        <span class="badge bg-primary me-1 mb-1"><?php echo htmlspecialchars($specialty); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <h5>Languages</h5>
                                <div class="language-badges">
                                    <?php foreach ($languages as $language): ?>
                                        <span class="badge bg-secondary me-1 mb-1"><?php echo htmlspecialchars($language); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h4>About</h4>
                </div>
                <div class="card-body">
                    <?php echo nl2br(htmlspecialchars($consultant['bio'])); ?>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Reviews (<?php echo $reviewCount; ?>)</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($reviews)): ?>
                        <p>No reviews yet.</p>
                    <?php else: ?>
                        <?php foreach ($reviews as $review): ?>
                            <div class="review-item mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <strong><?php echo htmlspecialchars($review['first_name'] . ' ' . $review['last_name']); ?></strong>
                                        <span class="text-muted ms-2">
                                            <?php echo date('M d, Y', strtotime($review['created_at'])); ?>
                                        </span>
                                    </div>
                                    <div class="review-rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?php echo ($i <= $review['rating']) ? 'text-warning' : 'text-muted'; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <p><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Booking Form -->
        <div class="col-lg-4">
            <div class="card booking-card sticky-top" style="top: 90px;">
                <div class="card-header">
                    <h4>Book Consultation</h4>
                </div>
                <div class="card-body">
                    <?php if ($bookingSuccess): ?>
                        <div class="alert alert-success booking-success">
                            <h5><i class="fas fa-check-circle"></i> Booking Successful!</h5>
                            <p>Your consultation request has been sent. The consultant will contact you soon to confirm the appointment.</p>
                        </div>
                    <?php else: ?>
                        <?php if (!empty($bookingError)): ?>
                            <div class="alert alert-danger"><?php echo $bookingError; ?></div>
                        <?php endif; ?>
                        
                        <form method="post" action="">
                            <div class="mb-3">
                                <label for="consultation_type" class="form-label">Consultation Type</label>
                                <select class="form-select" id="consultation_type" name="consultation_type" required>
                                    <option value="">Select Type</option>
                                    <option value="video">Video Call</option>
                                    <option value="phone">Phone Call</option>
                                    <option value="in_person">In Person</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="consultation_date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="consultation_date" name="consultation_date" min="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="consultation_time" class="form-label">Time</label>
                                <select class="form-select" id="consultation_time" name="consultation_time" required>
                                    <option value="">Select Time</option>
                                    <?php
                                    // Generate time slots from 9 AM to 5 PM
                                    for ($hour = 9; $hour <= 17; $hour++) {
                                        $time = sprintf("%02d:00", $hour);
                                        echo "<option value=\"$time\">$time</option>";
                                        
                                        // Add half-hour slots
                                        if ($hour < 17) {
                                            $halfHour = sprintf("%02d:30", $hour);
                                            echo "<option value=\"$halfHour\">$halfHour</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="consultation_message" class="form-label">Message</label>
                                <textarea class="form-control" id="consultation_message" name="consultation_message" rows="4" placeholder="Briefly describe what you'd like to discuss" required></textarea>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" name="book_consultation" class="btn btn-primary booking-btn">
                                    Book Consultation
                                </button>
                            </div>
                            
                            <div class="mt-3 booking-fee-info">
                                <p class="mb-0"><i class="fas fa-info-circle"></i> Consultation Fee: $<?php echo number_format($consultant['consultation_fee'], 2); ?> per hour</p>
                                <small class="text-muted">You will receive payment instructions after the consultant confirms your booking.</small>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 