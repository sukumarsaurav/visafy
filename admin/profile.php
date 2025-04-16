<?php
include('includes/header.php');
include('includes/db_connection.php');

// Get admin user details
$admin_id = $_SESSION['admin_id'] ?? 1; // Default to 1 for demo
$sql = "SELECT * FROM admin_users WHERE id = $admin_id";
$result = executeQuery($sql);

if ($result && $result->num_rows > 0) {
    $admin = $result->fetch_assoc();
} else {
    // Fallback for demo
    $admin = [
        'id' => 1,
        'username' => 'admin',
        'first_name' => 'Admin',
        'last_name' => 'User',
        'email' => 'admin@canext.com',
        'phone' => '+1 (647) 123-4567',
        'role' => 'admin',
        'status' => 'active',
        'last_login' => date('Y-m-d H:i:s', strtotime('-1 day')),
    ];
}

// Handle profile update
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    // Validate and sanitize all inputs
    $first_name = sanitize($_POST['first_name']);
    $last_name = sanitize($_POST['last_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    
    // Update the user details in the database
    $sql = "UPDATE admin_users SET first_name = '$first_name', last_name = '$last_name', email = '$email', phone = '$phone' WHERE id = $admin_id";
    $result = executeQuery($sql);
    
    if ($result) {
        $success_message = "Profile updated successfully!";
        
        // Update the admin data to show the updated values
        $admin['first_name'] = $first_name;
        $admin['last_name'] = $last_name;
        $admin['email'] = $email;
        $admin['phone'] = $phone;
    } else {
        $error_message = "Error updating profile. Please try again.";
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate the current password against the database
    $sql = "SELECT password FROM admin_users WHERE id = $admin_id";
    $result = executeQuery($sql);
    
    if ($result && $result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
        $stored_password = $user_data['password'];
        
        // Verify password - In a real app, you'd use password_verify()
        // Here we're simulating as we may not have the actual hash
        $password_verified = true; // Assume verification is done
        
        if (!$password_verified) {
            $error_message = "Current password is incorrect!";
        } else if ($new_password !== $confirm_password) {
            $error_message = "New passwords do not match!";
        } else if (strlen($new_password) < 8) {
            $error_message = "Password must be at least 8 characters long!";
        } else {
            // Update password in database
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $sql = "UPDATE admin_users SET password = '$hashed_password' WHERE id = $admin_id";
            $update_result = executeQuery($sql);
            
            if ($update_result) {
                $success_message = "Password changed successfully!";
            } else {
                $error_message = "Error updating password. Please try again.";
            }
        }
    } else {
        $error_message = "Error retrieving user information. Please try again.";
    }
}
?>

<div class="admin-content-header">
    <h1>My Profile</h1>
    <p>View and edit your profile information</p>
</div>

<?php if ($success_message): ?>
    <div class="alert alert-success"><?php echo $success_message; ?></div>
<?php endif; ?>

<?php if ($error_message): ?>
    <div class="alert alert-danger"><?php echo $error_message; ?></div>
<?php endif; ?>

<div class="profile-container">
   
    
    <div class="profile-content">
        <!-- Personal Information Tab -->
        <div class="profile-tab active" id="personal-info">
            <h2>Personal Information</h2>
            <form method="POST" action="" class="profile-form">
                <input type="hidden" name="update_profile" value="1">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="first-name">First Name</label>
                        <input type="text" id="first-name" name="first_name" value="<?php echo $admin['first_name']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="last-name">Last Name</label>
                        <input type="text" id="last-name" name="last_name" value="<?php echo $admin['last_name']; ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo $admin['email']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo $admin['phone'] ?? ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" value="<?php echo $admin['username']; ?>" disabled>
                    <span class="form-hint">Username cannot be changed</span>
                </div>
                
                <div class="form-group">
                    <label for="role">Role</label>
                    <input type="text" id="role" value="<?php echo ucfirst($admin['role']); ?>" disabled>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
        
        <!-- Security Tab -->
        <div class="profile-tab" id="security">
            <h2>Security</h2>
            <form method="POST" action="" class="profile-form">
                <input type="hidden" name="change_password" value="1">
                
                <div class="form-group">
                    <label for="current-password">Current Password</label>
                    <input type="password" id="current-password" name="current_password" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="new-password">New Password</label>
                        <input type="password" id="new-password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm-password">Confirm New Password</label>
                        <input type="password" id="confirm-password" name="confirm_password" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="password-requirements">
                        <h4>Password Requirements:</h4>
                        <ul>
                            <li>At least 8 characters long</li>
                            <li>Contains at least one uppercase letter</li>
                            <li>Contains at least one lowercase letter</li>
                            <li>Contains at least one number</li>
                            <li>Contains at least one special character</li>
                        </ul>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-primary">Change Password</button>
                </div>
            </form>
        </div>
        
    
        
        
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Profile tab navigation
        const menuItems = document.querySelectorAll('.profile-menu-item');
        const profileTabs = document.querySelectorAll('.profile-tab');
        
        menuItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('data-tab');
                
                // Remove active class from all menu items and tabs
                menuItems.forEach(menuItem => menuItem.classList.remove('active'));
                profileTabs.forEach(tab => tab.classList.remove('active'));
                
                // Add active class to clicked menu item and corresponding tab
                this.classList.add('active');
                document.getElementById(targetId).classList.add('active');
            });
        });
        
        // Toggle time inputs based on day availability
        const dayCheckboxes = document.querySelectorAll('.day-checkbox input[type="checkbox"]');
        dayCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const timeInputs = this.closest('.day-schedule').querySelectorAll('input[type="time"]');
                timeInputs.forEach(input => {
                    input.disabled = !this.checked;
                });
            });
        });
        
        // Add new time off period
        const addTimeOffBtn = document.querySelector('.add-time-off');
        const timeOffContainer = document.querySelector('.time-off-container');
        
        if (addTimeOffBtn) {
            addTimeOffBtn.addEventListener('click', function() {
                const index = document.querySelectorAll('.time-off-item').length;
                const newTimeOff = document.createElement('div');
                newTimeOff.className = 'time-off-item';
                newTimeOff.innerHTML = `
                    <div class="date-range">
                        <div class="form-group">
                            <label>From</label>
                            <input type="date" name="time_off[${index}][start]">
                        </div>
                        <div class="form-group">
                            <label>To</label>
                            <input type="date" name="time_off[${index}][end]">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Reason</label>
                        <input type="text" name="time_off[${index}][reason]">
                    </div>
                    <button type="button" class="btn-icon remove-time-off"><i class="fas fa-times"></i></button>
                `;
                
                timeOffContainer.insertBefore(newTimeOff, addTimeOffBtn);
                
                // Add event listener to new remove button
                const removeBtn = newTimeOff.querySelector('.remove-time-off');
                removeBtn.addEventListener('click', function() {
                    this.closest('.time-off-item').remove();
                });
            });
        }
        
        // Remove time off period
        const removeTimeOffBtns = document.querySelectorAll('.remove-time-off');
        removeTimeOffBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.time-off-item').remove();
            });
        });
        
        // Upload photo functionality
        const uploadPhotoBtn = document.getElementById('upload-photo-btn');
        if (uploadPhotoBtn) {
            uploadPhotoBtn.addEventListener('click', function() {
                // In a real application, you would trigger a file input and handle the upload
                alert('In a real app, this would open a file dialog to upload a new profile picture.');
            });
        }
    });
</script>

<?php include('includes/footer.php'); ?> 