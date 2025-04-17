<?php
session_start();
require_once 'config/database.php';

// Get consultant ID from URL
$consultant_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch consultant details
$query = "SELECT p.*, u.name, u.email, u.status 
          FROM professionals p 
          JOIN users u ON p.user_id = u.id 
          WHERE p.id = ? AND u.status = 'active' AND p.is_verified = 1";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $consultant_id);
$stmt->execute();
$result = $stmt->get_result();
$consultant = $result->fetch_assoc();

if (!$consultant) {
    header('Location: consultant.php');
    exit;
}

// Get consultation fees
$query = "SELECT consultation_type, fee FROM consultation_fees WHERE professional_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $consultant['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$consultation_fees = [];

while ($row = $result->fetch_assoc()) {
    $consultation_fees[$row['consultation_type']] = $row['fee'];
}

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $consultation_type = $_POST['consultation_type'];
    $date = $_POST['date'];
    $time_slot = $_POST['time_slot'];
    
    // Validate inputs
    if (empty($consultation_type) || empty($date) || empty($time_slot)) {
        $error = "Please fill in all required fields";
    } else {
        // Check if time slot is available
        $query = "SELECT id FROM time_slots 
                 WHERE professional_id = ? AND date = ? AND start_time = ? AND is_booked = 0";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('iss', $consultant['user_id'], $date, $time_slot);
        $stmt->execute();
        $result = $stmt->get_result();
        $time_slot_data = $result->fetch_assoc();
        
        if (!$time_slot_data) {
            $error = "Selected time slot is no longer available";
        } else {
            // Check if user is logged in
            if (!isset($_SESSION['user_id'])) {
                $error = "Please log in to book a consultation";
            } else {
                // Get price for selected consultation type
                $price = $consultation_fees[$consultation_type] ?? 0;
                
                if ($price <= 0) {
                    $error = "Invalid consultation type selected";
                } else {
                    // Create booking
                    $query = "INSERT INTO bookings (professional_id, client_id, time_slot_id, consultation_type, status, price) 
                             VALUES (?, ?, ?, ?, 'pending', ?)";
                    $stmt = $conn->prepare($query);
                    $client_id = $_SESSION['user_id'];
                    $prof_id = $consultant['user_id'];
                    $stmt->bind_param('iiiss', $prof_id, $client_id, $time_slot_data['id'], $consultation_type, $price);
                    
                    if ($stmt->execute()) {
                        // Update time slot availability
                        $query = "UPDATE time_slots SET is_booked = 1 WHERE id = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param('i', $time_slot_data['id']);
                        $stmt->execute();
                        
                        header('Location: booking-confirmation.php?id=' . $conn->insert_id);
                        exit;
                    } else {
                        $error = "Failed to create booking. Please try again.";
                    }
                }
            }
        }
    }
}

// Get available time slots for the next 30 days
$query = "SELECT date, start_time, end_time 
          FROM time_slots 
          WHERE professional_id = ? AND date >= CURDATE() AND date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) 
          AND is_booked = 0 
          ORDER BY date, start_time";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $consultant['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$time_slots = $result->fetch_all(MYSQLI_ASSOC);

$page_title = $consultant['name'] . " | Visafy";
include('includes/header.php');
?>

<!-- Consultant Profile Header -->
<section class="profile-header py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-3 text-center">
                <img src="<?php echo !empty($consultant['profile_image']) ? htmlspecialchars($consultant['profile_image']) : 'images/default-profile.jpg'; ?>" 
                     alt="<?php echo htmlspecialchars($consultant['name']); ?>" 
                     class="rounded-circle mb-3" 
                     style="width: 150px; height: 150px; object-fit: cover;">
            </div>
            <div class="col-md-9">
                <h1 class="mb-2"><?php echo htmlspecialchars($consultant['name']); ?></h1>
                <p class="text-muted mb-2"><?php echo htmlspecialchars($consultant['license_number']); ?></p>
                <div class="mb-3">
                    <span class="badge bg-primary me-2"><?php echo htmlspecialchars($consultant['years_experience']); ?> years exp</span>
                    <?php if ($consultant['rating']): ?>
                        <span class="badge bg-success">
                            <i class="fas fa-star small"></i> <?php echo number_format($consultant['rating'], 1); ?>
                            (<?php echo $consultant['reviews_count']; ?> reviews)
                        </span>
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <h5>About</h5>
                    <p><?php echo htmlspecialchars($consultant['bio']); ?></p>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h5>Specialties</h5>
                        <p>
                            <?php 
                            $specialties = explode(',', $consultant['specializations']);
                            foreach ($specialties as $index => $specialty): 
                                echo htmlspecialchars(trim($specialty));
                                if ($index < count($specialties) - 1) echo ', ';
                            endforeach; 
                            ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h5>Languages</h5>
                        <p>
                            <?php 
                            $languages = explode(',', $consultant['languages']);
                            foreach ($languages as $index => $language): 
                                echo htmlspecialchars(trim($language));
                                if ($index < count($languages) - 1) echo ', ';
                            endforeach; 
                            ?>
                        </p>
                    </div>
                </div>
                
                <div class="mb-3">
                    <h5>Consultation Fees</h5>
                    <div class="row">
                        <?php if (isset($consultation_fees['video'])): ?>
                            <div class="col-md-4">
                                <div class="card mb-2">
                                    <div class="card-body text-center">
                                        <i class="fas fa-video fa-2x mb-2 text-primary"></i>
                                        <h6>Video</h6>
                                        <p class="mb-0 fw-bold">C$<?php echo number_format($consultation_fees['video'], 2); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($consultation_fees['phone'])): ?>
                            <div class="col-md-4">
                                <div class="card mb-2">
                                    <div class="card-body text-center">
                                        <i class="fas fa-phone fa-2x mb-2 text-primary"></i>
                                        <h6>Phone</h6>
                                        <p class="mb-0 fw-bold">C$<?php echo number_format($consultation_fees['phone'], 2); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($consultation_fees['inperson'])): ?>
                            <div class="col-md-4">
                                <div class="card mb-2">
                                    <div class="card-body text-center">
                                        <i class="fas fa-user fa-2x mb-2 text-primary"></i>
                                        <h6>In-Person</h6>
                                        <p class="mb-0 fw-bold">C$<?php echo number_format($consultation_fees['inperson'], 2); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Booking Form -->
<section class="booking-form py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Book a Consultation</h2>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <div class="alert alert-warning">
                                <p><strong>Please <a href="login.php">log in</a> to book a consultation.</strong></p>
                            </div>
                        <?php else: ?>
                            <form method="POST" id="bookingForm">
                                <div class="mb-4">
                                    <label class="form-label">Consultation Type</label>
                                    <div class="row g-3">
                                        <?php if (isset($consultation_fees['video'])): ?>
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="consultation_type" id="video" value="video" required>
                                                    <label class="form-check-label" for="video">
                                                        <i class="fas fa-video me-2"></i>Video Consultation
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (isset($consultation_fees['phone'])): ?>
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="consultation_type" id="phone" value="phone">
                                                    <label class="form-check-label" for="phone">
                                                        <i class="fas fa-phone me-2"></i>Phone Consultation
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (isset($consultation_fees['inperson'])): ?>
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="consultation_type" id="inperson" value="inperson">
                                                    <label class="form-check-label" for="inperson">
                                                        <i class="fas fa-user me-2"></i>In-Person Consultation
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="date" class="form-label">Select Date</label>
                                    <input type="date" class="form-control" id="date" name="date" required 
                                           min="<?php echo date('Y-m-d'); ?>" 
                                           max="<?php echo date('Y-m-d', strtotime('+30 days')); ?>">
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label">Available Time Slots</label>
                                    <div id="timeSlots" class="row g-2">
                                        <!-- Time slots will be loaded dynamically -->
                                    </div>
                                </div>
                                
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-lg">Book Now</button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('date');
    const timeSlotsContainer = document.getElementById('timeSlots');
    const consultationTypes = document.querySelectorAll('input[name="consultation_type"]');
    
    // Function to load time slots
    function loadTimeSlots(date) {
        timeSlotsContainer.innerHTML = '';
        
        // Filter time slots for selected date
        const availableSlots = <?php echo json_encode($time_slots); ?>.filter(slot => slot.date === date);
        
        if (availableSlots.length === 0) {
            timeSlotsContainer.innerHTML = '<div class="col-12"><div class="alert alert-info">No available time slots for this date.</div></div>';
            return;
        }
        
        // Create time slot buttons
        availableSlots.forEach(slot => {
            const startTime = new Date('1970-01-01T' + slot.start_time);
            const endTime = new Date('1970-01-01T' + slot.end_time);
            
            const button = document.createElement('div');
            button.className = 'col-md-3';
            button.innerHTML = `
                <input type="radio" class="btn-check" name="time_slot" id="slot_${slot.start_time}" value="${slot.start_time}" required>
                <label class="btn btn-outline-primary w-100" for="slot_${slot.start_time}">
                    ${startTime.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})} - 
                    ${endTime.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                </label>
            `;
            timeSlotsContainer.appendChild(button);
        });
    }
    
    // Load time slots when date is selected
    if (dateInput) {
        dateInput.addEventListener('change', function() {
            loadTimeSlots(this.value);
        });
        
        // Validate form before submission
        const bookingForm = document.getElementById('bookingForm');
        if (bookingForm) {
            bookingForm.addEventListener('submit', function(e) {
                if (!dateInput.value) {
                    e.preventDefault();
                    alert('Please select a date');
                }
            });
        }
    }
});
</script>

<?php include('includes/footer.php'); ?> 