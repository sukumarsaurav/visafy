<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$error = '';
$success = '';

// Handle Registration
if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $user_type = $_POST['user_type'];

    // Basic validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password) || empty($user_type)) {
        $error = "All fields are required";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "Email already exists";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Set account status based on user type
            $status = ($user_type === 'professional') ? 'pending' : 'active';
            
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, user_type, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $hashed_password, $user_type, $status);
            
            if ($stmt->execute()) {
                $success = ($user_type === 'professional') 
                    ? "Registration successful! Your account is pending admin approval."
                    : "Registration successful! You can now login.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}

// Handle Login
if (isset($_POST['login'])) {
    $email = trim($_POST['login_email']);
    $password = $_POST['login_password'];

    if (empty($email) || empty($password)) {
        $error = "Both email and password are required";
    } else {
        $stmt = $conn->prepare("SELECT id, email, password, user_type, status FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                // Allow professionals with pending status to login
                if ($user['user_type'] === 'professional' || $user['status'] === 'active') {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_type'] = $user['user_type'];
                    $_SESSION['user_status'] = $user['status'];
                    
                    // Redirect based on user type
                    switch($user['user_type']) {
                        case 'applicant':
                            header("Location: dashboard/applicant/index.php");
                            break;
                        case 'employer':
                            header("Location: dashboard/employer/index.php");
                            break;
                        case 'professional':
                            header("Location: dashboard/professional/index.php");
                            break;
                    }
                    exit();
                } else if ($user['status'] === 'pending') {
                    $error = "Your account is pending approval";
                } else {
                    $error = "Your account has been suspended or deactivated";
                }
            } else {
                $error = "Invalid email or password";
            }
        } else {
            $error = "Invalid email or password";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Register - Visafy</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #042167;
            --secondary-color: #eaaa34;
            --error-color: #dc3545;
            --success-color: #28a745;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            line-height: 1.6;
        }

        .container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .auth-container {
            display: flex;
            gap: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .auth-section {
            flex: 1;
            padding: 40px;
        }

        .auth-section h2 {
            color: var(--primary-color);
            margin-bottom: 20px;
            font-size: 24px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-group select {
            background-color: white;
        }

        .btn {
            background-color: var(--secondary-color);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: var(--primary-color);
        }

        .error-message {
            color: var(--error-color);
            margin-bottom: 20px;
            padding: 10px;
            background-color: rgba(220, 53, 69, 0.1);
            border-radius: 5px;
        }

        .success-message {
            color: var(--success-color);
            margin-bottom: 20px;
            padding: 10px;
            background-color: rgba(40, 167, 69, 0.1);
            border-radius: 5px;
        }

        .divider {
            width: 1px;
            background-color: #ddd;
        }

        .user-type-options {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .user-type-option {
            flex: 1;
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s;
        }

        .user-type-option:hover {
            border-color: var(--secondary-color);
        }

        .user-type-option.selected {
            border-color: var(--secondary-color);
            background-color: rgba(234, 170, 52, 0.1);
        }

        .user-type-option i {
            font-size: 24px;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .user-type-option h4 {
            margin: 0;
            color: var(--primary-color);
        }

        @media (max-width: 768px) {
            .auth-container {
                flex-direction: column;
            }

            .divider {
                width: 100%;
                height: 1px;
            }

            .user-type-options {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="auth-container">
            <!-- Login Section -->
            <div class="auth-section">
                <h2>Login to Your Account</h2>
                <?php if ($error && isset($_POST['login'])): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="login_email">Email Address</label>
                        <input type="email" id="login_email" name="login_email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="login_password">Password</label>
                        <input type="password" id="login_password" name="login_password" required>
                    </div>
                    
                    <button type="submit" name="login" class="btn">Login</button>
                </form>
            </div>

            <div class="divider"></div>

            <!-- Register Section -->
            <div class="auth-section">
                <h2>Create New Account</h2>
                <?php if ($error && isset($_POST['register'])): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="success-message"><?php echo $success; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label>I am a:</label>
                        <div class="user-type-options">
                            <div class="user-type-option" data-type="applicant" onclick="selectUserType('applicant')">
                                <i class="fas fa-user"></i>
                                <h4>Applicant</h4>
                            </div>
                            <div class="user-type-option" data-type="employer" onclick="selectUserType('employer')">
                                <i class="fas fa-building"></i>
                                <h4>Employer</h4>
                            </div>
                            <div class="user-type-option" data-type="professional" onclick="selectUserType('professional')">
                                <i class="fas fa-user-tie"></i>
                                <h4>Immigration Professional</h4>
                            </div>
                        </div>
                        <input type="hidden" id="user_type" name="user_type" required>
                    </div>

                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>

                    <button type="submit" name="register" class="btn">Register</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function selectUserType(type) {
            // Remove selected class from all options
            document.querySelectorAll('.user-type-option').forEach(option => {
                option.classList.remove('selected');
            });
            
            // Add selected class to clicked option
            document.querySelector(`.user-type-option[data-type="${type}"]`).classList.add('selected');
            
            // Set the hidden input value
            document.getElementById('user_type').value = type;
        }
    </script>
</body>
</html>
