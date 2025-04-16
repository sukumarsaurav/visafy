<?php
include('includes/header.php');
include('includes/db_connection.php');

// Initialize variables
$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$customer_id = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : 0;
$first_name = '';
$last_name = '';
$email = '';
$phone = '';
$country = '';
$consultation_type = '';
$appointment_date = '';
$appointment_time = '';
$appointment_datetime = '';
$status = 'pending';
$payment_status = 'unpaid';
$payment_amount = 0;
$immigration_purpose = '';
$special_requests = '';
$additional_notes = '';

// Get existing customer info if customer_id is provided
if ($customer_id > 0 && $booking_id === 0) {
    $sql = "SELECT * FROM customers WHERE id = $customer_id";
    $result = executeQuery($sql);
    
    if ($result && $result->num_rows > 0) {
        $customer = $result->fetch_assoc();
        $first_name = $customer['first_name'];
        $last_name = $customer['last_name'];
        $email = $customer['email'];
        $phone = $customer['phone'];
        $country = $customer['country'];
    }
}

// Check if editing an existing booking
if ($booking_id > 0) {
    $sql = "SELECT * FROM appointments WHERE id = $booking_id";
    $result = executeQuery($sql);
    
    if ($result && $result->num_rows > 0) {
        $booking = $result->fetch_assoc();
        $first_name = $booking['first_name'];
        $last_name = $booking['last_name'];
        $email = $booking['email'];
        $phone = $booking['phone'];
        $country = $booking['country'];
        $consultation_type = $booking['consultation_type'];
        $appointment_datetime = $booking['appointment_datetime'];
        // Split datetime into date and time
        if (!empty($appointment_datetime)) {
            $appointment_date = date('Y-m-d', strtotime($appointment_datetime));
            $appointment_time = date('H:i', strtotime($appointment_datetime));
        }
        $status = $booking['status'];
        $payment_status = $booking['payment_status'];
        $payment_amount = $booking['payment_amount'];
        $immigration_purpose = $booking['immigration_purpose'];
        $special_requests = $booking['special_requests'];
        $additional_notes = $booking['additional_notes'];
    } else {
        // Booking not found, redirect to bookings page
        header('Location: bookings.php');
        exit;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $first_name = sanitize($_POST['first_name']);
    $last_name = sanitize($_POST['last_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $country = sanitize($_POST['country']);
    $consultation_type = sanitize($_POST['consultation_type']);
    $appointment_date = sanitize($_POST['appointment_date']);
    $appointment_time = sanitize($_POST['appointment_time']);
    $status = sanitize($_POST['status']);
    $payment_status = sanitize($_POST['payment_status']);
    $payment_amount = floatval($_POST['payment_amount']);
    $immigration_purpose = sanitize($_POST['immigration_purpose']);
    $special_requests = sanitize($_POST['special_requests']);
    $additional_notes = sanitize($_POST['additional_notes']);
    
    // Combine date and time into datetime
    $appointment_datetime = $appointment_date . ' ' . $appointment_time;
    
    // Validate required fields
    $errors = [];
    if (empty($first_name)) $errors[] = "First name is required";
    if (empty($last_name)) $errors[] = "Last name is required";
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    if (empty($phone)) $errors[] = "Phone is required";
    if (empty($consultation_type)) $errors[] = "Consultation type is required";
    if (empty($appointment_date)) $errors[] = "Appointment date is required";
    if (empty($appointment_time)) $errors[] = "Appointment time is required";
    
    // If no errors, save to database
    if (empty($errors)) {
        // Check if customer exists, add them if not
        $check_customer_sql = "SELECT * FROM customers WHERE email = '$email'";
        $check_customer_result = executeQuery($check_customer_sql);
        
        if ($check_customer_result && $check_customer_result->num_rows === 0) {
            // Add customer to database
            $add_customer_sql = "INSERT INTO customers (
                first_name, last_name, email, phone, country, created_at, updated_at
            ) VALUES (
                '$first_name', '$last_name', '$email', '$phone', '$country', NOW(), NOW()
            )";
            executeQuery($add_customer_sql);
        }
        
        if ($booking_id > 0) {
            // Update existing booking
            $sql = "UPDATE appointments SET 
                    first_name = '$first_name',
                    last_name = '$last_name',
                    email = '$email',
                    phone = '$phone',
                    country = '$country',
                    consultation_type = '$consultation_type',
                    appointment_datetime = '$appointment_datetime',
                    status = '$status',
                    payment_status = '$payment_status',
                    payment_amount = $payment_amount,
                    immigration_purpose = '$immigration_purpose',
                    special_requests = '$special_requests',
                    additional_notes = '$additional_notes',
                    updated_at = NOW()
                    WHERE id = $booking_id";
                    
            if (executeQuery($sql)) {
                // Redirect to booking view page
                header("Location: view_booking.php?id=$booking_id&success=updated");
                exit;
            } else {
                $errors[] = "Error updating booking. Please try again.";
            }
        } else {
            // Insert new booking
            $sql = "INSERT INTO appointments (
                    first_name, last_name, email, phone, country, 
                    consultation_type, appointment_datetime, status, payment_status, 
                    payment_amount, immigration_purpose, special_requests, additional_notes, 
                    created_at, updated_at
                ) VALUES (
                    '$first_name', '$last_name', '$email', '$phone', '$country',
                    '$consultation_type', '$appointment_datetime', '$status', '$payment_status',
                    $payment_amount, '$immigration_purpose', '$special_requests', '$additional_notes',
                    NOW(), NOW()
                )";
                
            if (executeQuery($sql)) {
                $new_booking_id = $GLOBALS['db']->insert_id;
                // Redirect to bookings list
                header("Location: bookings.php?success=added");
                exit;
            } else {
                $errors[] = "Error adding booking. Please try again.";
            }
        }
    }
}
?>

<div class="admin-content-header">
    <h1><?php echo $booking_id > 0 ? 'Edit Booking' : 'Add New Booking'; ?></h1>
    <div class="header-actions">
        <?php if ($booking_id > 0): ?>
        <a href="view_booking.php?id=<?php echo $booking_id; ?>" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Booking Details
        </a>
        <?php else: ?>
        <a href="bookings.php" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Bookings
        </a>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <ul>
        <?php foreach ($errors as $error): ?>
        <li><?php echo $error; ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2><i class="fas fa-calendar-edit"></i> <?php echo $booking_id > 0 ? 'Edit Booking Information' : 'Add New Booking'; ?></h2>
    </div>
    <div class="admin-card-body">
        <form method="post" action="">
            <h3>Customer Information</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name <span class="required">*</span></label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo $first_name; ?>" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name <span class="required">*</span></label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo $last_name; ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email <span class="required">*</span></label>
                    <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone <span class="required">*</span></label>
                    <input type="tel" id="phone" name="phone" value="<?php echo $phone; ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="country">Country</label>
                    <input type="text" id="country" name="country" value="<?php echo $country; ?>">
                </div>
            </div>
            
            <h3>Appointment Details</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="consultation_type">Consultation Type <span class="required">*</span></label>
                    <select id="consultation_type" name="consultation_type" required>
                        <option value="">Select Type</option>
                        <option value="Video Consultation" <?php echo $consultation_type === 'Video Consultation' ? 'selected' : ''; ?>>Video Consultation</option>
                        <option value="Phone Consultation" <?php echo $consultation_type === 'Phone Consultation' ? 'selected' : ''; ?>>Phone Consultation</option>
                        <option value="In-Person Consultation" <?php echo $consultation_type === 'In-Person Consultation' ? 'selected' : ''; ?>>In-Person Consultation</option>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="appointment_date">Date <span class="required">*</span></label>
                    <input type="date" id="appointment_date" name="appointment_date" value="<?php echo $appointment_date; ?>" required>
                </div>
                <div class="form-group">
                    <label for="appointment_time">Time <span class="required">*</span></label>
                    <input type="time" id="appointment_time" name="appointment_time" value="<?php echo $appointment_time; ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="confirmed" <?php echo $status === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                        <option value="completed" <?php echo $status === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo $status === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        <option value="no-show" <?php echo $status === 'no-show' ? 'selected' : ''; ?>>No Show</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="payment_status">Payment Status</label>
                    <select id="payment_status" name="payment_status">
                        <option value="unpaid" <?php echo $payment_status === 'unpaid' ? 'selected' : ''; ?>>Unpaid</option>
                        <option value="paid" <?php echo $payment_status === 'paid' ? 'selected' : ''; ?>>Paid</option>
                        <option value="refunded" <?php echo $payment_status === 'refunded' ? 'selected' : ''; ?>>Refunded</option>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="payment_amount">Payment Amount ($)</label>
                    <input type="number" id="payment_amount" name="payment_amount" value="<?php echo $payment_amount; ?>" min="0" step="0.01">
                </div>
            </div>
            
            <h3>Additional Information</h3>
            
            <div class="form-group">
                <label for="immigration_purpose">Immigration Purpose</label>
                <select id="immigration_purpose" name="immigration_purpose">
                    <option value="">Select Purpose</option>
                    <option value="Express Entry" <?php echo $immigration_purpose === 'Express Entry' ? 'selected' : ''; ?>>Express Entry</option>
                    <option value="Family Sponsorship" <?php echo $immigration_purpose === 'Family Sponsorship' ? 'selected' : ''; ?>>Family Sponsorship</option>
                    <option value="Study Permit" <?php echo $immigration_purpose === 'Study Permit' ? 'selected' : ''; ?>>Study Permit</option>
                    <option value="Work Permit" <?php echo $immigration_purpose === 'Work Permit' ? 'selected' : ''; ?>>Work Permit</option>
                    <option value="Visitor Visa" <?php echo $immigration_purpose === 'Visitor Visa' ? 'selected' : ''; ?>>Visitor Visa</option>
                    <option value="Canadian Citizenship" <?php echo $immigration_purpose === 'Canadian Citizenship' ? 'selected' : ''; ?>>Canadian Citizenship</option>
                    <option value="Provincial Nominee Program" <?php echo $immigration_purpose === 'Provincial Nominee Program' ? 'selected' : ''; ?>>Provincial Nominee Program</option>
                    <option value="Other" <?php echo $immigration_purpose === 'Other' ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="special_requests">Special Requests</label>
                <textarea id="special_requests" name="special_requests" rows="3"><?php echo $special_requests; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="additional_notes">Notes</label>
                <textarea id="additional_notes" name="additional_notes" rows="4"><?php echo $additional_notes; ?></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> <?php echo $booking_id > 0 ? 'Update Booking' : 'Save Booking'; ?>
                </button>
                <?php if ($booking_id > 0): ?>
                <a href="view_booking.php?id=<?php echo $booking_id; ?>" class="btn-secondary">Cancel</a>
                <?php else: ?>
                <a href="bookings.php" class="btn-secondary">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<style>
.required {
    color: #dc3545;
}

.admin-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

.admin-card-header {
    padding: 20px;
    border-bottom: 1px solid #eee;
}

.admin-card-header h2 {
    margin: 0;
    color: var(--color-primary);
}

.admin-card-body {
    padding: 20px;
}

.admin-card-body h3 {
    margin: 0 0 20px 0;
    font-size: 1.2rem;
    color: var(--color-primary);
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
}

.admin-card-body h3:not(:first-child) {
    margin-top: 30px;
}

.form-row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -10px 15px;
}

.form-group {
    flex: 1;
    min-width: 250px;
    padding: 0 10px;
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-family: inherit;
    font-size: inherit;
}

.form-group textarea {
    resize: vertical;
}

.form-actions {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #eee;
    display: flex;
    gap: 10px;
}
</style>

<?php include('includes/footer.php'); ?> 