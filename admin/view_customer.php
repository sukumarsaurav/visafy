<?php
include('includes/header.php');
include('includes/db_connection.php');

// Get customer ID from URL parameter
$customer_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get customer details from database
$sql = "SELECT * FROM customers WHERE id = $customer_id";
$result = executeQuery($sql);

// Check if customer exists
if ($result && $result->num_rows > 0) {
    $customer = $result->fetch_assoc();
} else {
    // Redirect to customers page if customer not found
    header('Location: customers.php');
    exit;
}

// Get customer's appointments
$sql_appointments = "SELECT * FROM appointments WHERE email = '{$customer['email']}' ORDER BY appointment_datetime DESC";
$appointments_result = executeQuery($sql_appointments);
?>

<div class="admin-content-header">
    <h1>Customer Details</h1>
    <div class="header-actions">
        <a href="customers.php" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Customers
        </a>
        <a href="edit_customer.php?id=<?php echo $customer_id; ?>" class="btn-primary">
            <i class="fas fa-edit"></i> Edit Customer
        </a>
    </div>
</div>

<div class="customer-detail-container">
    <div class="customer-detail-card">
        <div class="customer-detail-header">
            <div class="customer-id">
                <h3>Customer #<?php echo str_pad($customer['id'], 4, '0', STR_PAD_LEFT); ?></h3>
                <span class="detail-created-date">
                    Created on <?php echo date('M j, Y', strtotime($customer['created_at'])); ?>
                </span>
            </div>
            <div class="customer-actions">
                <div class="dropdown">
                    <button class="dropdown-toggle">
                        Actions <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a href="edit_customer.php?id=<?php echo $customer_id; ?>">Edit Details</a>
                        <a href="#" id="add-note">Add Note</a>
                        <a href="#" id="create-appointment">Schedule Appointment</a>
                        <a href="#" id="delete-customer" class="text-danger">Delete Customer</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="customer-detail-body">
            <div class="customer-detail-section">
                <h4><i class="fas fa-user"></i> Personal Information</h4>
                <div class="detail-row">
                    <div class="detail-group">
                        <span class="detail-label">Full Name</span>
                        <span class="detail-value"><?php echo $customer['first_name'] . ' ' . $customer['last_name']; ?></span>
                    </div>
                    <div class="detail-group">
                        <span class="detail-label">Email</span>
                        <span class="detail-value"><?php echo $customer['email']; ?></span>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-group">
                        <span class="detail-label">Phone</span>
                        <span class="detail-value"><?php echo !empty($customer['phone']) ? $customer['phone'] : 'N/A'; ?></span>
                    </div>
                    <div class="detail-group">
                        <span class="detail-label">Date of Birth</span>
                        <span class="detail-value"><?php echo !empty($customer['date_of_birth']) ? date('F j, Y', strtotime($customer['date_of_birth'])) : 'N/A'; ?></span>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-group">
                        <span class="detail-label">Country</span>
                        <span class="detail-value"><?php echo !empty($customer['country']) ? $customer['country'] : 'N/A'; ?></span>
                    </div>
                    <div class="detail-group">
                        <span class="detail-label">Citizenship</span>
                        <span class="detail-value"><?php echo !empty($customer['citizenship']) ? $customer['citizenship'] : 'N/A'; ?></span>
                    </div>
                </div>
            </div>
            
            <div class="customer-detail-section">
                <h4><i class="fas fa-passport"></i> Immigration Information</h4>
                <div class="detail-row">
                    <div class="detail-group">
                        <span class="detail-label">Immigration Status</span>
                        <span class="detail-value"><?php echo !empty($customer['immigration_status']) ? ucfirst(str_replace('_', ' ', $customer['immigration_status'])) : 'N/A'; ?></span>
                    </div>
                    <div class="detail-group">
                        <span class="detail-label">Passport Number</span>
                        <span class="detail-value"><?php echo !empty($customer['passport_number']) ? $customer['passport_number'] : 'N/A'; ?></span>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($customer['notes'])): ?>
            <div class="customer-detail-section">
                <h4><i class="fas fa-sticky-note"></i> Notes</h4>
                <div class="detail-row">
                    <div class="detail-group full-width">
                        <div class="customer-notes"><?php echo nl2br($customer['notes']); ?></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="customer-appointments-card">
        <div class="card-header">
            <h3>Consultation History</h3>
            <a href="add_booking.php?customer_id=<?php echo $customer_id; ?>" class="btn-primary">
                <i class="fas fa-plus"></i> Schedule Consultation
            </a>
        </div>
        
        <div class="appointments-list">
            <?php if ($appointments_result && $appointments_result->num_rows > 0): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Date & Time</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($appointment = $appointments_result->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo str_pad($appointment['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                <td><?php echo $appointment['consultation_type']; ?></td>
                                <td>
                                    <div class="booking-datetime">
                                        <span class="booking-date"><?php echo date('M j, Y', strtotime($appointment['appointment_datetime'])); ?></span>
                                        <span class="booking-time"><?php echo date('g:i A', strtotime($appointment['appointment_datetime'])); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($appointment['status']); ?>">
                                        <?php echo ucfirst($appointment['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($appointment['payment_status']); ?>">
                                        <?php echo ucfirst($appointment['payment_status']); ?>
                                    </span>
                                </td>
                                <td class="actions-cell">
                                    <div class="action-buttons">
                                        <a href="view_booking.php?id=<?php echo $appointment['id']; ?>" class="action-btn view-btn" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="edit_booking.php?id=<?php echo $appointment['id']; ?>" class="action-btn edit-btn" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-appointments">
                    <p>No consultations booked yet.</p>
                    <a href="add_booking.php?customer_id=<?php echo $customer_id; ?>" class="btn-primary">Schedule Consultation</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Note Modal -->
<div class="modal" id="note-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add Customer Note</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="note-form">
                <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
                
                <div class="form-group">
                    <label for="note-content">Note</label>
                    <textarea id="note-content" name="note_content" rows="6" required><?php echo $customer['notes']; ?></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary modal-close">Cancel</button>
            <button class="btn-primary" id="save-note">Save Note</button>
        </div>
    </div>
</div>

<!-- Delete Customer Confirmation Modal -->
<div class="modal" id="delete-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Delete Customer</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this customer? This action cannot be undone.</p>
            <p><strong>Note:</strong> All appointments associated with this customer will also be deleted.</p>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary modal-close">Cancel</button>
            <button class="btn-danger" id="confirm-delete">Delete Customer</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add note button
    const addNoteBtn = document.getElementById('add-note');
    const noteModal = document.getElementById('note-modal');
    
    addNoteBtn.addEventListener('click', function(e) {
        e.preventDefault();
        noteModal.style.display = 'flex';
    });
    
    // Delete customer button
    const deleteCustomerBtn = document.getElementById('delete-customer');
    const deleteModal = document.getElementById('delete-modal');
    
    deleteCustomerBtn.addEventListener('click', function(e) {
        e.preventDefault();
        deleteModal.style.display = 'flex';
    });
    
    // Confirm delete button
    const confirmDeleteBtn = document.getElementById('confirm-delete');
    confirmDeleteBtn.addEventListener('click', function() {
        window.location.href = 'customers.php?delete=<?php echo $customer_id; ?>';
    });
    
    // Save note
    const saveNoteBtn = document.getElementById('save-note');
    saveNoteBtn.addEventListener('click', function() {
        if (document.getElementById('note-content').value.trim() === '') {
            alert('Please enter a note.');
            return;
        }
        
        // In a real app, you would send this to the server via AJAX
        document.getElementById('note-form').submit();
    });
    
    // Close modals
    const modalCloseButtons = document.querySelectorAll('.modal-close');
    modalCloseButtons.forEach(button => {
        button.addEventListener('click', function() {
            noteModal.style.display = 'none';
            deleteModal.style.display = 'none';
        });
    });
    
    // Close modal if clicked outside
    window.addEventListener('click', function(event) {
        if (event.target === noteModal) {
            noteModal.style.display = 'none';
        }
        if (event.target === deleteModal) {
            deleteModal.style.display = 'none';
        }
    });
});
</script>

<style>
.customer-detail-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.customer-detail-card, .customer-appointments-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.customer-detail-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #eee;
}

.customer-id h3 {
    margin: 0 0 5px 0;
    color: var(--color-primary);
}

.detail-created-date {
    font-size: 14px;
    color: #777;
}

.customer-detail-body {
    padding: 20px;
}

.customer-detail-section {
    margin-bottom: 30px;
}

.customer-detail-section h4 {
    margin: 0 0 15px 0;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
    color: var(--color-primary);
}

.detail-row {
    display: flex;
    flex-wrap: wrap;
    margin-bottom: 15px;
}

.detail-group {
    flex: 1;
    min-width: 250px;
    margin-bottom: 10px;
}

.detail-group.full-width {
    flex: 100%;
}

.detail-label {
    display: block;
    font-size: 14px;
    color: #777;
    margin-bottom: 5px;
}

.detail-value {
    display: block;
    font-weight: 500;
}

.customer-notes {
    padding: 15px;
    background: #f9f9f9;
    border-radius: 5px;
    line-height: 1.5;
}

.customer-appointments-card .card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #eee;
}

.card-header h3 {
    margin: 0;
    color: var(--color-primary);
}

.appointments-list {
    padding: 20px;
}

.empty-appointments {
    text-align: center;
    padding: 40px 20px;
}

.empty-appointments p {
    margin-bottom: 20px;
    color: #777;
}

@media (min-width: 992px) {
    .customer-detail-container {
        flex-direction: row;
        align-items: flex-start;
    }
    
    .customer-detail-card {
        flex: 3;
    }
    
    .customer-appointments-card {
        flex: 2;
    }
}
</style>

<?php include('includes/footer.php'); ?> 