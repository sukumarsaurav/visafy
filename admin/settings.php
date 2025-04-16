<?php include('includes/header.php'); ?>

<div class="admin-content-header">
    <h1>System Settings</h1>
    <p>Configure application settings and preferences</p>
</div>

<div class="settings-container">
    <div class="settings-sidebar">
        <ul class="settings-nav">
            <li class="active" data-target="general-settings">General Settings</li>
   
        </ul>
    </div>
    
    <div class="settings-content">
        <!-- General Settings -->
        <div class="settings-section active" id="general-settings">
            <h2>General Settings</h2>
            
            <form class="settings-form">
                <div class="form-group">
                    <label for="site-name">Site Name</label>
                    <input type="text" id="site-name" name="site_name" value="CANEXT Immigration Consultancy">
                </div>
                
                <div class="form-group">
                    <label for="site-email">Contact Email</label>
                    <input type="email" id="site-email" name="site_email" value="info@canext.com">
                </div>
                
                <div class="form-group">
                    <label for="site-phone">Contact Phone</label>
                    <input type="tel" id="site-phone" name="site_phone" value="+1 (647) 226-7436">
                </div>
                
                <div class="form-group">
                    <label for="site-address">Office Address</label>
                    <textarea id="site-address" name="site_address" rows="3">2233 Argentina Rd, Mississauga ON L5N 2X7, Canada</textarea>
                </div>
                
                <div class="form-group">
                    <label for="business-hours">Business Hours</label>
                    <input type="text" id="business-hours" name="business_hours" value="Mon-Fri: 9am-5pm">
                </div>
                
                <div class="form-group">
                    <label for="timezone">Timezone</label>
                    <select id="timezone" name="timezone">
                        <option value="America/New_York">Eastern Time (ET)</option>
                        <option value="America/Chicago">Central Time (CT)</option>
                        <option value="America/Denver">Mountain Time (MT)</option>
                        <option value="America/Los_Angeles">Pacific Time (PT)</option>
                        <option value="America/Toronto" selected>Eastern Time - Toronto</option>
                    </select>
                </div>
                
                <button type="submit" class="btn-primary">Save Changes</button>
            </form>
        </div>
        
    
        
 
 
        


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Settings tab navigation
        const settingsNavItems = document.querySelectorAll('.settings-nav li');
        const settingsSections = document.querySelectorAll('.settings-section');
        
        settingsNavItems.forEach(item => {
            item.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                
                // Remove active class from all items and sections
                settingsNavItems.forEach(navItem => navItem.classList.remove('active'));
                settingsSections.forEach(section => section.classList.remove('active'));
                
                // Add active class to clicked item and corresponding section
                this.classList.add('active');
                document.getElementById(targetId).classList.add('active');
            });
        });
        
        // Business hours checkbox toggle
        const dayCheckboxes = document.querySelectorAll('.business-hours-row input[type="checkbox"]');
        dayCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const timeInputs = this.closest('.business-hours-row').querySelectorAll('input[type="time"]');
                timeInputs.forEach(input => {
                    input.disabled = !this.checked;
                });
            });
        });
    });
</script>

<?php include('includes/footer.php'); ?> 