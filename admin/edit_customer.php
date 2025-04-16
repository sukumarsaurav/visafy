<?php
include('includes/header.php');
include('includes/db_connection.php');

// Initialize variables
$customer_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$first_name = '';
$last_name = '';
$email = '';
$phone = '';
$country = '';
$citizenship = '';
$date_of_birth = '';
$immigration_status = '';
$passport_number = '';
$notes = '';

// Check if editing an existing customer
if ($customer_id > 0) {
    $sql = "SELECT * FROM customers WHERE id = $customer_id";
    $result = executeQuery($sql);
    
    if ($result && $result->num_rows > 0) {
        $customer = $result->fetch_assoc();
        $first_name = $customer['first_name'];
        $last_name = $customer['last_name'];
        $email = $customer['email'];
        $phone = $customer['phone'];
        $country = $customer['country'];
        $citizenship = $customer['citizenship'];
        $date_of_birth = $customer['date_of_birth'];
        $immigration_status = $customer['immigration_status'];
        $passport_number = $customer['passport_number'];
        $notes = $customer['notes'];
    } else {
        // Customer not found, redirect to customers page
        header('Location: customers.php');
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
    $citizenship = sanitize($_POST['citizenship']);
    $date_of_birth = sanitize($_POST['date_of_birth']);
    $immigration_status = sanitize($_POST['immigration_status']);
    $passport_number = sanitize($_POST['passport_number']);
    $notes = sanitize($_POST['notes']);
    
    // Validate required fields
    $errors = [];
    if (empty($first_name)) $errors[] = "First name is required";
    if (empty($last_name)) $errors[] = "Last name is required";
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    // If no errors, save to database
    if (empty($errors)) {
        if ($customer_id > 0) {
            // Update existing customer
            $sql = "UPDATE customers SET 
                    first_name = '$first_name',
                    last_name = '$last_name',
                    email = '$email',
                    phone = '$phone',
                    country = '$country',
                    citizenship = '$citizenship',
                    date_of_birth = " . (!empty($date_of_birth) ? "'$date_of_birth'" : "NULL") . ",
                    immigration_status = '$immigration_status',
                    passport_number = '$passport_number',
                    notes = '$notes',
                    updated_at = NOW()
                    WHERE id = $customer_id";
                    
            if (executeQuery($sql)) {
                // Redirect to customer view page
                header("Location: view_customer.php?id=$customer_id&success=updated");
                exit;
            } else {
                $errors[] = "Error updating customer. Please try again.";
            }
        } else {
            // Insert new customer
            $sql = "INSERT INTO customers (
                    first_name, last_name, email, phone, country, 
                    citizenship, date_of_birth, immigration_status, 
                    passport_number, notes, created_at, updated_at
                ) VALUES (
                    '$first_name', '$last_name', '$email', '$phone', '$country',
                    '$citizenship', " . (!empty($date_of_birth) ? "'$date_of_birth'" : "NULL") . ", '$immigration_status',
                    '$passport_number', '$notes', NOW(), NOW()
                )";
                
            if (executeQuery($sql)) {
                $new_customer_id = $GLOBALS['db']->insert_id;
                // Redirect to customers list
                header("Location: customers.php?success=added");
                exit;
            } else {
                $errors[] = "Error adding customer. Please try again.";
            }
        }
    }
}
?>

<div class="admin-content-header">
    <h1><?php echo $customer_id > 0 ? 'Edit Customer' : 'Add New Customer'; ?></h1>
    <div class="header-actions">
        <?php if ($customer_id > 0): ?>
        <a href="view_customer.php?id=<?php echo $customer_id; ?>" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Customer Details
        </a>
        <?php else: ?>
        <a href="customers.php" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Customers
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
        <h2><i class="fas fa-user-edit"></i> <?php echo $customer_id > 0 ? 'Edit Customer Information' : 'Add New Customer'; ?></h2>
    </div>
    <div class="admin-card-body">
        <form method="post" action="">
            <h3>Personal Information</h3>
            
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
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo $phone; ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="country">Country</label>
                    <input type="text" id="country" name="country" value="<?php echo $country; ?>">
                </div>
                <div class="form-group">
                    <label for="date_of_birth">Date of Birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo $date_of_birth; ?>">
                </div>
            </div>
            
            <h3>Immigration Information</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="citizenship">Citizenship</label>
                    <input type="text" id="citizenship" name="citizenship" value="<?php echo $citizenship; ?>">
                </div>
                <div class="form-group">
                    <label for="immigration_status">Immigration Status</label>
                    <select id="immigration_status" name="immigration_status">
                        <option value="" <?php echo empty($immigration_status) ? 'selected' : ''; ?>>Select Status</option>
                        <option value="citizen" <?php echo $immigration_status === 'citizen' ? 'selected' : ''; ?>>Citizen</option>
                        <option value="permanent_resident" <?php echo $immigration_status === 'permanent_resident' ? 'selected' : ''; ?>>Permanent Resident</option>
                        <option value="work_permit" <?php echo $immigration_status === 'work_permit' ? 'selected' : ''; ?>>Work Permit Holder</option>
                        <option value="study_permit" <?php echo $immigration_status === 'study_permit' ? 'selected' : ''; ?>>Study Permit Holder</option>
                        <option value="visitor" <?php echo $immigration_status === 'visitor' ? 'selected' : ''; ?>>Visitor</option>
                        <option value="temporary_resident" <?php echo $immigration_status === 'temporary_resident' ? 'selected' : ''; ?>>Temporary Resident</option>
                        <option value="refugee" <?php echo $immigration_status === 'refugee' ? 'selected' : ''; ?>>Refugee/Protected Person</option>
                        <option value="none" <?php echo $immigration_status === 'none' ? 'selected' : ''; ?>>None of the above</option>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="passport_number">Passport Number</label>
                    <input type="text" id="passport_number" name="passport_number" value="<?php echo $passport_number; ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea id="notes" name="notes" rows="4"><?php echo $notes; ?></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> <?php echo $customer_id > 0 ? 'Update Customer' : 'Save Customer'; ?>
                </button>
                <?php if ($customer_id > 0): ?>
                <a href="view_customer.php?id=<?php echo $customer_id; ?>" class="btn-secondary">Cancel</a>
                <?php else: ?>
                <a href="customers.php" class="btn-secondary">Cancel</a>
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