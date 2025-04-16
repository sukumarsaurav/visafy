<?php
$page_title = "Book a Consultation | CANEXT Immigration";
include('includes/header.php');
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
            <div class="progress-step active" style="display: flex; align-items: center;">
                <div class="step-number" style="width: 40px; height: 40px; background-color: var(--color-burgundy); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600;">1</div>
                <span class="step-text" style="margin-left: 10px; color: var(--color-burgundy); font-weight: 500;">Select Service</span>
            </div>
            <div style="width: 100px; height: 2px; background-color: #e0e0e0; margin: 0 15px;"></div>
            <div class="progress-step" style="display: flex; align-items: center;">
                <div class="step-number" style="width: 40px; height: 40px; background-color: #e0e0e0; color: #666; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600;">2</div>
                <span class="step-text" style="margin-left: 10px; color: #666;">Your Details</span>
            </div>
            <div style="width: 100px; height: 2px; background-color: #e0e0e0; margin: 0 15px;"></div>
            <div class="progress-step" style="display: flex; align-items: center;">
                <div class="step-number" style="width: 40px; height: 40px; background-color: #e0e0e0; color: #666; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600;">3</div>
                <span class="step-text" style="margin-left: 10px; color: #666;">Confirmation</span>
            </div>
        </div>
        
        <?php if(isset($_SESSION['booking_error'])): ?>
            <div class="alert alert-danger" style="max-width: 800px; margin: 0 auto 30px; padding: 15px; background-color: #f8d7da; color: #721c24; border-radius: 5px; text-align: center;">
                <?php echo $_SESSION['booking_error']; unset($_SESSION['booking_error']); ?>
            </div>
        <?php endif; ?>

        <div class="booking-container" style="max-width: 800px; margin: 0 auto;">
            <h2 class="section-title" style="text-align: center; margin-bottom: 40px;">Select Consultation Type</h2>
            
            <!-- Consultation Types -->
            <div class="consultation-types" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px; margin-bottom: 40px;">
                <!-- Video Consultation -->
                <div class="consultation-type" data-type="Video Consultation" style="background: white; border-radius: 10px; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); cursor: pointer; transition: transform 0.3s, box-shadow 0.3s;" data-aos="fade-up">
                    <div style="display: flex; align-items: center; margin-bottom: 15px;">
                        <div style="width: 50px; height: 50px; background-color: var(--color-cream); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                            <i class="fas fa-video" style="color: var(--color-burgundy); font-size: 24px;"></i>
                        </div>
                        <h3 style="margin: 0; font-size: 18px; color: var(--color-dark);">Video Consultation</h3>
                    </div>
                    <p style="margin-bottom: 15px; color: #666; font-size: 14px;">Meet with our consultant via video call from anywhere in the world for personalized advice.</p>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 600; color: var(--color-dark);">C$150</span>
                        <span style="font-size: 14px; color: #666;">60 minutes</span>
                    </div>
                </div>
                
                <!-- Phone Consultation -->
                <div class="consultation-type" data-type="Phone Consultation" style="background: white; border-radius: 10px; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); cursor: pointer; transition: transform 0.3s, box-shadow 0.3s;" data-aos="fade-up" data-aos-delay="100">
                    <div style="display: flex; align-items: center; margin-bottom: 15px;">
                        <div style="width: 50px; height: 50px; background-color: var(--color-cream); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                            <i class="fas fa-phone-alt" style="color: var(--color-burgundy); font-size: 24px;"></i>
                        </div>
                        <h3 style="margin: 0; font-size: 18px; color: var(--color-dark);">Phone Consultation</h3>
                    </div>
                    <p style="margin-bottom: 15px; color: #666; font-size: 14px;">Speak directly with our immigration expert via phone call for convenient guidance.</p>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 600; color: var(--color-dark);">C$120</span>
                        <span style="font-size: 14px; color: #666;">45 minutes</span>
                    </div>
                </div>
                
                <!-- In-Person Consultation -->
                <div class="consultation-type" data-type="In-Person Consultation" style="background: white; border-radius: 10px; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); cursor: pointer; transition: transform 0.3s, box-shadow 0.3s;" data-aos="fade-up" data-aos-delay="200">
                    <div style="display: flex; align-items: center; margin-bottom: 15px;">
                        <div style="width: 50px; height: 50px; background-color: var(--color-cream); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                            <i class="fas fa-user" style="color: var(--color-burgundy); font-size: 24px;"></i>
                        </div>
                        <h3 style="margin: 0; font-size: 18px; color: var(--color-dark);">In-Person Consultation</h3>
                    </div>
                    <p style="margin-bottom: 15px; color: #666; font-size: 14px;">Visit our office in Toronto for a face-to-face meeting with our licensed consultant.</p>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 600; color: var(--color-dark);">C$200</span>
                        <span style="font-size: 14px; color: #666;">60 minutes</span>
                    </div>
                </div>
            </div>
            
            <!-- Date and Time Selection -->
            <div id="dateTimeSelection" style="display: none;">
                <h3 style="margin-bottom: 20px; text-align: center; color: var(--color-dark);">Select Date and Time</h3>
                
                <!-- Date Selection -->
                <div class="date-selection" style="margin-bottom: 30px;">
                    <label style="display: block; margin-bottom: 10px; color: var(--color-dark); font-weight: 500;">Select Date:</label>
                    <input type="date" id="consultationDate" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit;" min="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d', strtotime('+60 days')); ?>">
                </div>
                
                <!-- Time Slots -->
                <div class="time-selection" style="margin-bottom: 30px;">
                    <label style="display: block; margin-bottom: 10px; color: var(--color-dark); font-weight: 500;">Select Time:</label>
                    <div class="time-slots" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 15px;">
                        <div class="time-slot" data-time="09:00:00" style="text-align: center; padding: 10px; border: 1px solid #ddd; border-radius: 5px; cursor: pointer; transition: all 0.3s;">9:00 AM</div>
                        <div class="time-slot" data-time="10:00:00" style="text-align: center; padding: 10px; border: 1px solid #ddd; border-radius: 5px; cursor: pointer; transition: all 0.3s;">10:00 AM</div>
                        <div class="time-slot" data-time="11:00:00" style="text-align: center; padding: 10px; border: 1px solid #ddd; border-radius: 5px; cursor: pointer; transition: all 0.3s;">11:00 AM</div>
                        <div class="time-slot" data-time="12:00:00" style="text-align: center; padding: 10px; border: 1px solid #ddd; border-radius: 5px; cursor: pointer; transition: all 0.3s;">12:00 PM</div>
                        <div class="time-slot" data-time="13:00:00" style="text-align: center; padding: 10px; border: 1px solid #ddd; border-radius: 5px; cursor: pointer; transition: all 0.3s;">1:00 PM</div>
                        <div class="time-slot" data-time="14:00:00" style="text-align: center; padding: 10px; border: 1px solid #ddd; border-radius: 5px; cursor: pointer; transition: all 0.3s;">2:00 PM</div>
                        <div class="time-slot" data-time="15:00:00" style="text-align: center; padding: 10px; border: 1px solid #ddd; border-radius: 5px; cursor: pointer; transition: all 0.3s;">3:00 PM</div>
                        <div class="time-slot" data-time="16:00:00" style="text-align: center; padding: 10px; border: 1px solid #ddd; border-radius: 5px; cursor: pointer; transition: all 0.3s;">4:00 PM</div>
                    </div>
                </div>
                
                <!-- Error Message -->
                <div id="errorMessage" style="display: none; color: #dc3545; text-align: center; margin: 20px auto 0; padding: 10px; border-radius: 5px; background-color: #ffe6e6;"></div>
                
                <!-- Continue Button -->
                <div class="booking-navigation" style="text-align: center; margin-top: 30px;">
                    <button id="continueBtn" class="btn btn-primary" style="min-width: 150px;" disabled>Continue</button>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const consultationTypes = document.querySelectorAll('.consultation-type');
    const dateTimeSelection = document.getElementById('dateTimeSelection');
    const consultationDate = document.getElementById('consultationDate');
    const timeSlots = document.querySelectorAll('.time-slot');
    const continueBtn = document.getElementById('continueBtn');
    const errorMessage = document.getElementById('errorMessage');
    
    let selectedType = '';
    let selectedTime = '';
    
    // Select consultation type
    consultationTypes.forEach(type => {
        type.addEventListener('click', function() {
            // Remove selected class from all types
            consultationTypes.forEach(t => t.style.boxShadow = '0 5px 15px rgba(0,0,0,0.05)');
            
            // Add selected class to clicked type
            this.style.boxShadow = '0 5px 20px rgba(0,0,0,0.15)';
            selectedType = this.getAttribute('data-type');
            
            // Show date and time selection
            dateTimeSelection.style.display = 'block';
            
            // Scroll to date and time selection
            dateTimeSelection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
    
    // Date input change
    consultationDate.addEventListener('change', function() {
        // Reset time slot selection
        timeSlots.forEach(slot => slot.classList.remove('selected'));
        timeSlots.forEach(slot => slot.style.backgroundColor = '');
        timeSlots.forEach(slot => slot.style.color = '');
        timeSlots.forEach(slot => slot.style.borderColor = '#ddd');
        
        selectedTime = '';
        continueBtn.disabled = true;
        
        // In a real app, you would check availability from database
        // For this demo, we'll just enable all time slots
        timeSlots.forEach(slot => {
            slot.style.display = 'block';
        });
    });
    
    // Select time slot
    timeSlots.forEach(slot => {
        slot.addEventListener('click', function() {
            if (consultationDate.value === '') {
                showError('Please select a date first');
                return;
            }
            
            // Remove selected class from all slots
            timeSlots.forEach(s => s.classList.remove('selected'));
            timeSlots.forEach(s => s.style.backgroundColor = '');
            timeSlots.forEach(s => s.style.color = '');
            timeSlots.forEach(s => s.style.borderColor = '#ddd');
            
            // Add selected class to clicked slot
            this.classList.add('selected');
            this.style.backgroundColor = 'var(--color-burgundy)';
            this.style.color = 'white';
            this.style.borderColor = 'var(--color-burgundy)';
            
            selectedTime = this.getAttribute('data-time');
            
            // Enable continue button
            continueBtn.disabled = false;
        });
    });
    
    // Continue button click
    continueBtn.addEventListener('click', function() {
        if (selectedType === '' || consultationDate.value === '' || selectedTime === '') {
            showError('Please complete all selections');
            return;
        }
        
        // Redirect to details page with selected options
        window.location.href = `consultant_details.php?type=${encodeURIComponent(selectedType)}&date=${encodeURIComponent(consultationDate.value)}&time=${encodeURIComponent(selectedTime)}`;
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

