<?php
$page_title = "Booking Confirmation | CANEXT Immigration";
include('includes/header.php');
include('admin/includes/db_connection.php');

// Get appointment ID from URL
$appointment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch appointment details from database
$sql = "SELECT * FROM appointments WHERE id = $appointment_id";
$result = executeQuery($sql);

if ($result && $result->num_rows > 0) {
    $appointment = $result->fetch_assoc();
} else {
    header('Location: consultant.php');
    exit;
}
?>

<!-- Page Header -->
<section class="page-header" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('images/consultation-header.jpg'); background-size: cover; background-position: center; padding: 100px 0; color: var(--color-light); text-align: center;background-color: var(--color-burgundy);">
<div style="position: absolute; width: 300px; height: 300px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); top: -100px; left: -100px;"></div>
    <div style="position: absolute; width: 200px; height: 200px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); bottom: -50px; right: 10%; animation: pulse 4s infinite alternate;"></div>
    <div style="position: absolute; width: 100px; height: 100px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.1); top: 30%; right: 20%; animation: pulse 3s infinite alternate;"></div>
    <div class="container">
        <h1 data-aos="fade-up">Booking Confirmation</h1>
        <p data-aos="fade-up" data-aos-delay="100" style="max-width: 700px; margin: 20px auto 0;">Thank you for booking a consultation with CANEXT Immigration</p>
    </div>
</section>

<!-- Confirmation Section -->
<section class="section confirmation-section" style="padding: 60px 0;">
    <div class="container">
        <!-- Success Message -->
        <div class="confirmation-box" data-aos="fade-up" style="max-width: 800px; margin: 0 auto; background-color: var(--color-cream); padding: 40px; border-radius: 10px; text-align: center; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
            <div class="confirmation-icon" style="margin-bottom: 20px;">
                <i class="fas fa-check-circle" style="font-size: 64px; color: #4CAF50;"></i>
            </div>
            <h2 style="color: var(--color-burgundy); margin-bottom: 10px;">Booking Confirmed!</h2>
            <p style="margin-bottom: 25px; font-size: 18px;">Your consultation booking has been successfully confirmed.</p>
            
            <!-- Booking Details -->
            <div class="booking-details" style="text-align: left; background: white; padding: 25px; border-radius: 5px; margin-bottom: 30px;">
                <h3 style="color: var(--color-dark); margin-bottom: 20px; text-align: center;">Booking Details</h3>
                
                <div style="display: grid; grid-template-columns: auto 1fr; gap: 10px 20px; margin-bottom: 15px;">
                    <strong>Booking Reference:</strong>
                    <span>#<?php echo str_pad($appointment['id'], 6, '0', STR_PAD_LEFT); ?></span>
                    
                    <strong>Consultation Type:</strong>
                    <span><?php echo $appointment['consultation_type']; ?></span>
                    
                    <strong>Date and Time:</strong>
                    <span><?php echo date('F j, Y - g:i A', strtotime($appointment['appointment_datetime'])); ?> ET</span>
                    
                    <strong>Client Name:</strong>
                    <span><?php echo $appointment['first_name'] . ' ' . $appointment['last_name']; ?></span>
                    
                    <strong>Contact Email:</strong>
                    <span><?php echo $appointment['email']; ?></span>
                    
                    <strong>Contact Phone:</strong>
                    <span><?php echo $appointment['phone']; ?></span>
                </div>
                
                <div style="background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin-top: 10px;">
                    <p style="margin: 0;"><strong>Status:</strong> <span style="color: #f39c12; font-weight: 500;">Pending payment</span></p>
                </div>
            </div>
            
            <!-- Payment Button -->
            <a href="#" class="btn btn-primary" style="margin-bottom: 20px;">Proceed to Payment</a>
            
            <!-- Next Steps -->
            <div class="next-steps" style="text-align: left; margin-top: 30px;">
                <h4 style="color: var(--color-burgundy); margin-bottom: 15px;">Next Steps:</h4>
                <ol style="padding-left: 20px; line-height: 1.6;">
                    <li style="margin-bottom: 10px;">Complete the payment to secure your consultation slot.</li>
                    <li style="margin-bottom: 10px;">You will receive a confirmation email with all details and instructions.</li>
                    <li style="margin-bottom: 10px;">We will contact you if any additional information is needed before the consultation.</li>
                    <li>Join the consultation at the scheduled time using the provided details.</li>
                </ol>
            </div>
        </div>

        <!-- Additional Actions -->
        <div class="additional-actions" data-aos="fade-up" style="text-align: center; margin-top: 40px;">
            <a href="index.php" class="btn btn-primary" style="margin: 0 10px;">Return to Homepage</a>
            <a href="resources.php" class="btn btn-secondary" style="margin: 0 10px;">Browse Resources</a>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?> 