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
$stmt = $conn->prepare("SELECT p.*, u.email, u.name FROM professionals p 
                        JOIN users u ON p.user_id = u.id
                        WHERE p.id = ? AND p.is_verified = 1");
$stmt->bind_param("i", $consultant_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Consultant not found or not active
    header("Location: consultant.php");
    exit();
}

$consultant = $result->fetch_assoc();

// Get specializations and languages from the comma-separated values in the database
$specializations = !empty($consultant['specializations']) ? explode(',', $consultant['specializations']) : [];
$languages = !empty($consultant['languages']) ? explode(',', $consultant['languages']) : [];

// Fetch reviews
$stmt = $conn->prepare("SELECT r.*, u.name FROM reviews r
                        JOIN users u ON r.user_id = u.id
                        WHERE r.professional_id = ? ORDER BY r.created_at DESC");
$stmt->bind_param("i", $consultant['user_id']);
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

// Get consultation fees
$stmt = $conn->prepare("SELECT consultation_type, fee FROM consultation_fees WHERE professional_id = ?");
$stmt->bind_param("i", $consultant['user_id']);
$stmt->execute();
$feesResult = $stmt->get_result();
$consultationFees = [];

while ($row = $feesResult->fetch_assoc()) {
    $consultationFees[$row['consultation_type']] = $row['fee'];
}

// Format profile image path
$profileImage = !empty($consultant['profile_image']) ? $base . '/' . $consultant['profile_image'] : $base . '/assets/images/logo-Visafy-light.png';

// Make sure consultant name is never null
$consultantName = !empty($consultant['name']) ? $consultant['name'] : 'Consultant';

// Handle booking form submission
$bookingSuccess = false;
$bookingError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_consultation'])) {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        $bookingError = 'Please login to book a consultation.';
    } else {
        $userId = $_SESSION['user_id'];
        $professionalId = $consultant['user_id']; // Use the professional's user_id
        $date = $_POST['consultation_date'];
        $time = $_POST['consultation_time'];
        $message = $conn->real_escape_string($_POST['consultation_message']);
        $type = $conn->real_escape_string($_POST['consultation_type']);
        
        // Get fee for selected consultation type
        $consultationFee = isset($consultationFees[$type]) ? $consultationFees[$type] : 0;
        
        // Create a time slot entry first
        $stmt = $conn->prepare("INSERT INTO time_slots (professional_id, date, start_time, end_time, is_video_available, is_phone_available, is_inperson_available, is_booked) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
        
        // Calculate end time (assume 30 minute slots)
        $startTime = $time . ":00";
        $endTime = date('H:i:s', strtotime($startTime . ' + 30 minutes'));
        
        // Set availability flags based on consultation type
        $isVideoAvailable = ($type == 'video') ? 1 : 0;
        $isPhoneAvailable = ($type == 'phone') ? 1 : 0;
        $isInPersonAvailable = ($type == 'inperson') ? 1 : 0;
        
        $stmt->bind_param("isssiiii", $professionalId, $date, $startTime, $endTime, $isVideoAvailable, $isPhoneAvailable, $isInPersonAvailable);
        
        if ($stmt->execute()) {
            $timeSlotId = $conn->insert_id;
            
            // Now insert the booking
            $stmt = $conn->prepare("INSERT INTO bookings (professional_id, client_id, time_slot_id, consultation_type, status, price, created_at) 
                                    VALUES (?, ?, ?, ?, 'pending', ?, NOW())");
            $stmt->bind_param("iiisd", $professionalId, $userId, $timeSlotId, $type, $consultationFee);
            
            if ($stmt->execute()) {
                $bookingSuccess = true;
            } else {
                $bookingError = 'Failed to book consultation. Please try again.';
            }
        } else {
            $bookingError = 'Failed to create time slot. Please try again.';
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
                            <img src="<?php echo $profileImage; ?>" alt="<?php echo htmlspecialchars($consultantName); ?>" class="img-fluid rounded-circle">
                        </div>
                        <div class="consultant-profile-info ms-md-4 mt-3 mt-md-0">
                            <h1><?php echo htmlspecialchars($consultantName); ?></h1>
                            
                            <div class="d-flex align-items-center mb-2">
                                <div class="me-3">
                                    <i class="fas fa-star text-warning"></i>
                                    <span><?php echo $avgRating; ?> (<?php echo $reviewCount; ?> reviews)</span>
                                </div>
                                <div>
                                    <i class="fas fa-briefcase"></i>
                                    <span><?php echo htmlspecialchars($consultant['years_experience']); ?> years experience</span>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <strong>License Number:</strong> <?php echo htmlspecialchars($consultant['license_number']); ?>
                            </div>
                            
                            <div class="mb-3">
                                <h5>Specialties</h5>
                                <div class="specialty-badges">
                                    <?php 
                                    foreach ($specializations as $spec): 
                                        $spec = trim($spec);
                                        if (!empty($spec)):
                                    ?>
                                        <span class="badge bg-primary me-1 mb-1"><?php echo htmlspecialchars($spec); ?></span>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <h5>Languages</h5>
                                <div class="language-badges">
                                    <?php 
                                    foreach ($languages as $lang): 
                                        $lang = trim($lang);
                                        if (!empty($lang)):
                                    ?>
                                        <span class="badge bg-secondary me-1 mb-1"><?php echo htmlspecialchars($lang); ?></span>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
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
                                        <strong><?php echo htmlspecialchars(!empty($review['name']) ? $review['name'] : 'Anonymous'); ?></strong>
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
                                <p><?php echo nl2br(htmlspecialchars(!empty($review['comment']) ? $review['comment'] : '')); ?></p>
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
                                    <?php if (!empty($consultationFees)): ?>
                                        <?php if (isset($consultationFees['video'])): ?>
                                            <option value="video">Video Call - $<?php echo number_format($consultationFees['video'], 2); ?></option>
                                        <?php endif; ?>
                                        <?php if (isset($consultationFees['phone'])): ?>
                                            <option value="phone">Phone Call - $<?php echo number_format($consultationFees['phone'], 2); ?></option>
                                        <?php endif; ?>
                                        <?php if (isset($consultationFees['inperson'])): ?>
                                            <option value="inperson">In Person - $<?php echo number_format($consultationFees['inperson'], 2); ?></option>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <option value="video">Video Call</option>
                                        <option value="phone">Phone Call</option>
                                        <option value="inperson">In Person</option>
                                    <?php endif; ?>
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
                                <?php if (!empty($consultationFees)): ?>
                                <p class="mb-0"><i class="fas fa-info-circle"></i> Consultation Fees:</p>
                                <ul class="list-unstyled ps-3 mb-1">
                                    <?php foreach ($consultationFees as $type => $fee): ?>
                                    <li><?php echo ucfirst($type); ?>: $<?php echo number_format($fee, 2); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php else: ?>
                                <p class="mb-0"><i class="fas fa-info-circle"></i> Contact for pricing details</p>
                                <?php endif; ?>
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