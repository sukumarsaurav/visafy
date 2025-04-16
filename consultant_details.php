<?php
$page_title = "Book a Consultation | CANEXT Immigration";
include('includes/header.php');

// Get booking details from URL parameters
$consultation_type = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : '';
$consultation_date = isset($_GET['date']) ? htmlspecialchars($_GET['date']) : '';
$consultation_time = isset($_GET['time']) ? htmlspecialchars($_GET['time']) : '';

// Redirect back to first step if parameters are missing
if (!$consultation_type || !$consultation_date || !$consultation_time) {
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
        <h1 data-aos="fade-up">Book a Consultation</h1>
        <p data-aos="fade-up" data-aos-delay="100" style="max-width: 700px; margin: 20px auto 0;">Schedule a personalized consultation with one of our licensed immigration consultants</p>
    </div>
</section>

<!-- Booking Steps -->
<section class="section booking-section" style="padding: 60px 0;">
    <div class="container">
        <!-- Progress Steps -->
        <div class="booking-progress" style="display: flex; justify-content: center; align-items: center; margin-bottom: 50px;">
            <div class="progress-step completed" style="display: flex; align-items: center;">
                <div class="step-number" style="width: 40px; height: 40px; background-color: var(--color-burgundy); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600;">✓</div>
                <span class="step-text" style="margin-left: 10px; color: var(--color-burgundy); font-weight: 500;">Select Service</span>
            </div>
            <div style="width: 100px; height: 2px; background-color: var(--color-burgundy); margin: 0 15px;"></div>
            <div class="progress-step active" style="display: flex; align-items: center;">
                <div class="step-number" style="width: 40px; height: 40px; background-color: var(--color-burgundy); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600;">2</div>
                <span class="step-text" style="margin-left: 10px; color: var(--color-burgundy); font-weight: 500;">Your Details</span>
            </div>
            <div style="width: 100px; height: 2px; background-color: #e0e0e0; margin: 0 15px;"></div>
            <div class="progress-step" style="display: flex; align-items: center;">
                <div class="step-number" style="width: 40px; height: 40px; background-color: #e0e0e0; color: #666; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600;">3</div>
                <span class="step-text" style="margin-left: 10px; color: #666;">Confirmation</span>
            </div>
        </div>

        <!-- Booking Summary -->
        <div class="booking-summary" style="max-width: 800px; margin: 0 auto 40px; background: var(--color-cream); padding: 20px; border-radius: 10px;">
            <h3 style="color: var(--color-burgundy); margin-bottom: 15px;">Booking Summary</h3>
            <div style="display: grid; grid-template-columns: auto 1fr; gap: 10px;">
                <strong>Service:</strong>
                <span><?php echo $consultation_type; ?></span>
                <strong>Date:</strong>
                <span><?php echo date('F j, Y', strtotime($consultation_date)); ?></span>
                <strong>Time:</strong>
                <span><?php echo date('g:i A', strtotime($consultation_time)); ?> ET</span>
            </div>
        </div>

        <h2 class="section-title" style="text-align: center; margin-bottom: 40px;">Enter Your Details</h2>

        <!-- Personal Details Form -->
        <form id="detailsForm" action="process_booking.php" method="POST" style="max-width: 800px; margin: 0 auto;">
            <input type="hidden" name="consultation_type" value="<?php echo $consultation_type; ?>">
            <input type="hidden" name="consultation_date" value="<?php echo $consultation_date; ?>">
            <input type="hidden" name="consultation_time" value="<?php echo $consultation_time; ?>">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <!-- Personal Information -->
                <div class="form-group">
                    <label style="display: block; margin-bottom: 8px; color: var(--color-dark); font-weight: 500;">First Name *</label>
                    <input type="text" name="first_name" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit;">
                </div>
                
                <div class="form-group">
                    <label style="display: block; margin-bottom: 8px; color: var(--color-dark); font-weight: 500;">Last Name *</label>
                    <input type="text" name="last_name" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit;">
                </div>
                
                <div class="form-group">
                    <label style="display: block; margin-bottom: 8px; color: var(--color-dark); font-weight: 500;">Email Address *</label>
                    <input type="email" name="email" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit;">
                </div>
                
                <div class="form-group">
                    <label style="display: block; margin-bottom: 8px; color: var(--color-dark); font-weight: 500;">Phone Number *</label>
                    <input type="tel" name="phone" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit;">
                </div>
            </div>

            <!-- Immigration Information -->
            <div style="margin-top: 30px;">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 8px; color: var(--color-dark); font-weight: 500;">What is your current immigration status? *</label>
                    <select name="immigration_status" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit;">
                        <option value="">Select your status</option>
                        <option value="citizen">Canadian Citizen</option>
                        <option value="pr">Permanent Resident</option>
                        <option value="work_permit">Work Permit Holder</option>
                        <option value="study_permit">Study Permit Holder</option>
                        <option value="visitor">Visitor</option>
                        <option value="none">None of the above</option>
                    </select>
                </div>

                <div class="form-group" style="margin-top: 20px;">
                    <label style="display: block; margin-bottom: 8px; color: var(--color-dark); font-weight: 500;">What services are you interested in? *</label>
                    <select name="service_interest" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit;">
                        <option value="">Select service</option>
                        <option value="express_entry">Express Entry</option>
                        <option value="study_permit">Study Permit</option>
                        <option value="work_permit">Work Permit</option>
                        <option value="family_sponsorship">Family Sponsorship</option>
                        <option value="visitor_visa">Visitor Visa</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group" style="margin-top: 20px;">
                    <label style="display: block; margin-bottom: 8px; color: var(--color-dark); font-weight: 500;">Additional Information</label>
                    <textarea name="additional_info" rows="4" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit; resize: vertical;" placeholder="Please provide any additional information that may help us better assist you."></textarea>
                </div>
            </div>

            <!-- Error Message -->
            <div id="errorMessage" style="display: none; color: #dc3545; text-align: center; margin: 20px auto 0; padding: 10px; border-radius: 5px; background-color: #ffe6e6;"></div>

            <!-- Navigation Buttons -->
            <div class="booking-navigation" style="display: flex; justify-content: space-between; margin-top: 40px;">
                <a href="javascript:history.back()" class="btn btn-secondary" style="min-width: 150px;">Back</a>
                <button type="submit" class="btn btn-primary" style="min-width: 150px;">Continue</button>
            </div>
        </form>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const detailsForm = document.getElementById('detailsForm');
    const errorMessage = document.getElementById('errorMessage');

    detailsForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Basic form validation
        const requiredFields = this.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.style.borderColor = '#dc3545';
            } else {
                field.style.borderColor = '#ddd';
            }
        });

        if (!isValid) {
            showError('Please fill in all required fields');
            return;
        }

        // If validation passes, submit the form
        this.submit();
    });

    function showError(message) {
        errorMessage.textContent = message;
        errorMessage.style.display = 'block';
        
        // Hide error message after 3 seconds
        setTimeout(() => {
            errorMessage.style.display = 'none';
        }, 3000);
    }
});
</script>

<?php include('includes/footer.php'); ?> 