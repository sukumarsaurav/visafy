<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

// Check if user is logged in and is a professional
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id']) || $_SESSION['user_type'] != 'professional') {
    header("Location: ../../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Get user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Get professional data if exists
$stmt = $conn->prepare("SELECT * FROM professionals WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$profile_exists = $result->num_rows > 0;
$profile_data = $profile_exists ? $result->fetch_assoc() : null;
$stmt->close();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic validation
    if (empty($_POST['license_number']) || empty($_POST['years_experience']) || empty($_POST['phone'])) {
        $error_message = "Please fill in all required fields.";
    } else {
        // Sanitize inputs
        $license_number = htmlspecialchars(trim($_POST['license_number']));
        $years_experience = (int)$_POST['years_experience'];
        $education = htmlspecialchars(trim($_POST['education']));
        $specializations = htmlspecialchars(trim($_POST['specializations']));
        $bio = htmlspecialchars(trim($_POST['bio']));
        $phone = htmlspecialchars(trim($_POST['phone']));
        $website = htmlspecialchars(trim($_POST['website']));
        $languages = htmlspecialchars(trim($_POST['languages']));
        $availability_status = htmlspecialchars(trim($_POST['availability_status']));
        $profile_completed = 1;
        
        // Handle profile image upload
        $profile_image = null;
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['profile_image']['name'];
            $filetype = pathinfo($filename, PATHINFO_EXTENSION);
            
            if (in_array(strtolower($filetype), $allowed)) {
                $new_filename = 'professional_' . $user_id . '_' . time() . '.' . $filetype;
                $upload_path = '../../uploads/professionals/' . $new_filename;
                
                // Make sure directory exists
                if (!file_exists('../../uploads/professionals/')) {
                    mkdir('../../uploads/professionals/', 0777, true);
                }
                
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                    $profile_image = 'uploads/professionals/' . $new_filename;
                }
            }
        }
        
        if ($profile_exists) {
            // Update existing profile
            $sql = "UPDATE professionals SET 
                    license_number = ?, 
                    years_experience = ?, 
                    education = ?, 
                    specializations = ?, 
                    bio = ?, 
                    phone = ?, 
                    website = ?, 
                    languages = ?, 
                    profile_completed = ?, 
                    availability_status = ?";
            
            $params = [$license_number, $years_experience, $education, $specializations, $bio, $phone, $website, $languages, $profile_completed, $availability_status];
            $types = "sississsi";
            
            if ($profile_image) {
                // Check if profile_image column exists
                $column_check = $conn->query("SHOW COLUMNS FROM `professionals` LIKE 'profile_image'");
                if($column_check->num_rows > 0) {
                    $sql .= ", profile_image = ?";
                    $params[] = $profile_image;
                    $types .= "s";
                }
            }
            
            $sql .= " WHERE user_id = ?";
            $params[] = $user_id;
            $types .= "i";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            
        } else {
            // Create new profile
            $sql = "INSERT INTO professionals (
                    user_id, license_number, years_experience, education, 
                    specializations, bio, phone, website, languages, 
                    profile_completed, availability_status";
            
            $params = [$user_id, $license_number, $years_experience, $education, 
                       $specializations, $bio, $phone, $website, $languages, 
                       $profile_completed, $availability_status];
            $types = "isissssssi";
            
            if ($profile_image) {
                // Check if profile_image column exists
                $column_check = $conn->query("SHOW COLUMNS FROM `professionals` LIKE 'profile_image'");
                if($column_check->num_rows > 0) {
                    $sql .= ", profile_image";
                    $params[] = $profile_image;
                    $types .= "s";
                }
            }
            
            $sql .= ") VALUES (" . str_repeat("?,", count($params) - 1) . "?)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
        }
        
        if ($stmt->execute()) {
            $success_message = "Profile saved successfully!";
            
            // Refresh profile data
            $stmt = $conn->prepare("SELECT * FROM professionals WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $profile_exists = $result->num_rows > 0;
            $profile_data = $profile_exists ? $result->fetch_assoc() : null;
            $stmt->close();
        } else {
            $error_message = "Error saving profile: " . $stmt->error;
        }
        
        $stmt->close();
    }
}

// Page title
$page_title = "Professional Profile | Visafy";
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Professional Profile</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="index.php" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>

            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Your Professional Profile</h5>
                </div>
                <div class="card-body">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="row mb-4">
                            <div class="col-md-3 text-center">
                                <?php if ($profile_exists && !empty($profile_data['profile_image'])): ?>
                                    <img src="<?php echo '../../' . $profile_data['profile_image']; ?>" alt="Profile Image" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 150px; height: 150px;">
                                        <i class="bi bi-person-circle" style="font-size: 80px;"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="mb-3">
                                    <label for="profile_image" class="form-label">Profile Image</label>
                                    <input type="file" class="form-control" id="profile_image" name="profile_image">
                                    <div class="form-text">Recommended size: 300x300 pixels</div>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" class="form-control" id="name" value="<?php echo htmlspecialchars($user_data['name']); ?>" disabled>
                                        <div class="form-text">To change your name, update your account settings</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="license_number" class="form-label">License Number*</label>
                                        <input type="text" class="form-control" id="license_number" name="license_number" required value="<?php echo $profile_exists ? htmlspecialchars($profile_data['license_number']) : ''; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="years_experience" class="form-label">Years of Experience*</label>
                                        <input type="number" class="form-control" id="years_experience" name="years_experience" min="0" max="50" required value="<?php echo $profile_exists ? htmlspecialchars($profile_data['years_experience']) : ''; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Phone Number*</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" required value="<?php echo $profile_exists ? htmlspecialchars($profile_data['phone']) : ''; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="website" class="form-label">Website</label>
                                        <input type="url" class="form-control" id="website" name="website" value="<?php echo $profile_exists ? htmlspecialchars($profile_data['website']) : ''; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="education" class="form-label">Education</label>
                            <textarea class="form-control" id="education" name="education" rows="2"><?php echo $profile_exists ? htmlspecialchars($profile_data['education']) : ''; ?></textarea>
                            <div class="form-text">List your educational qualifications (degrees, universities, etc.)</div>
                        </div>

                        <div class="mb-3">
                            <label for="specializations" class="form-label">Specializations</label>
                            <textarea class="form-control" id="specializations" name="specializations" rows="2"><?php echo $profile_exists ? htmlspecialchars($profile_data['specializations']) : ''; ?></textarea>
                            <div class="form-text">Enter your areas of expertise (e.g., Express Entry, Family Sponsorship, Study Permits)</div>
                        </div>

                        <div class="mb-3">
                            <label for="bio" class="form-label">Professional Bio</label>
                            <textarea class="form-control" id="bio" name="bio" rows="4"><?php echo $profile_exists ? htmlspecialchars($profile_data['bio']) : ''; ?></textarea>
                            <div class="form-text">Tell potential clients about yourself and your experience (up to 500 words)</div>
                        </div>

                        <div class="mb-3">
                            <label for="languages" class="form-label">Languages Spoken</label>
                            <input type="text" class="form-control" id="languages" name="languages" value="<?php echo $profile_exists ? htmlspecialchars($profile_data['languages']) : ''; ?>">
                            <div class="form-text">Separate multiple languages with commas (e.g., English, French, Spanish)</div>
                        </div>

                        <div class="mb-3">
                            <label for="availability_status" class="form-label">Availability Status</label>
                            <select class="form-select" id="availability_status" name="availability_status">
                                <option value="available" <?php echo ($profile_exists && $profile_data['availability_status'] == 'available') ? 'selected' : ''; ?>>Available (Accepting new clients)</option>
                                <option value="busy" <?php echo ($profile_exists && $profile_data['availability_status'] == 'busy') ? 'selected' : ''; ?>>Busy (Limited availability)</option>
                                <option value="unavailable" <?php echo ($profile_exists && $profile_data['availability_status'] == 'unavailable') ? 'selected' : ''; ?>>Unavailable (Not accepting new clients)</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">Save Profile</button>
                        </div>
                    </form>
                </div>
            </div>

            <?php if ($profile_exists): ?>
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Verification Status</h5>
                </div>
                <div class="card-body">
                    <?php if ($profile_data['is_verified']): ?>
                        <div class="alert alert-success mb-0">
                            <i class="bi bi-check-circle-fill me-2"></i> Your profile has been verified by Visafy. Clients will see a verified badge on your profile.
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning mb-3">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> Your profile is pending verification. Our team will review your credentials and update your status.
                        </div>
                        <p>To expedite verification, please ensure your license number is correct and consider uploading supporting documents.</p>
                        <a href="documents.php" class="btn btn-outline-primary">Upload Verification Documents</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?> 