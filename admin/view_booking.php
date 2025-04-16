<?php
include('includes/header.php');
include('includes/db_connection.php');

// Get booking ID from URL parameter
$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get booking details from database
$sql = "SELECT a.*, CONCAT(c.first_name, ' ', c.last_name) as customer_name, c.email as customer_email, c.phone as customer_phone, c.id as customer_id
        FROM appointments a 
        LEFT JOIN customers c ON a.email = c.email
        WHERE a.id = $booking_id";
$result = executeQuery($sql);

// Check if booking exists
if ($result && $result->num_rows > 0) {
    $booking = $result->fetch_assoc();
} else {
    // Redirect to bookings page if booking not found
    header('Location: bookings.php');
    exit;
}

// Get consultation notes
$sql_notes = "SELECT n.*, CONCAT(u.first_name, ' ', u.last_name) as admin_name 
             FROM consultation_notes n
             JOIN admin_users u ON n.admin_user_id = u.id
             WHERE n.appointment_id = $booking_id
             ORDER BY n.created_at DESC";
$notes_result = executeQuery($sql_notes);
?>

<div class="admin-content-header">
    <h1>Booking Details</h1>
    <div class="header-actions">
        <a href="bookings.php" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Bookings
        </a>
        <a href="edit_booking.php?id=<?php echo $booking_id; ?>" class="btn-primary">
            <i class="fas fa-edit"></i> Edit Booking
        </a>
    </div>
</div>

<div class="admin-content-body">
    <div class="booking-details-container">
        <!-- Booking Information Card -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h2>Booking Information</h2>
                <div class="booking-status">
                    <span class="status-badge status-<?php echo strtolower($booking['status']); ?>"><?php echo $booking['status']; ?></span>
                </div>
            </div>
            <div class="admin-card-body">
                <div class="detail-section">
                    <div class="detail-row">
                        <div class="detail-group">
                            <div class="detail-label">Booking ID</div>
                            <div class="detail-value"><?php echo $booking['id']; ?></div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Service Type</div>
                            <div class="detail-value"><?php echo $booking['consultation_type']; ?></div>
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-group">
                            <div class="detail-label">Appointment Date</div>
                            <div class="detail-value"><?php echo date('F j, Y', strtotime($booking['appointment_datetime'])); ?></div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Appointment Time</div>
                            <div class="detail-value"><?php echo date('g:i A', strtotime($booking['appointment_datetime'])); ?></div>
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-group">
                            <div class="detail-label">Booking Date</div>
                            <div class="detail-value"><?php echo date('F j, Y', strtotime($booking['created_at'])); ?></div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Consultant</div>
                            <div class="detail-value"><?php echo $booking['consultant_name'] ?? 'Not Assigned'; ?></div>
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-group">
                            <div class="detail-label">Payment Status</div>
                            <div class="detail-value">
                                <span class="payment-badge payment-<?php echo strtolower($booking['payment_status']); ?>">
                                    <?php echo $booking['payment_status']; ?>
                                </span>
                            </div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Payment Amount</div>
                            <div class="detail-value">$<?php echo number_format($booking['payment_amount'], 2); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Information Card -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h2>Customer Information</h2>
                <?php if (isset($booking['customer_id']) && !empty($booking['customer_id'])): ?>
                <div class="header-actions">
                    <a href="view_customer.php?id=<?php echo $booking['customer_id']; ?>" class="btn-text">
                        <i class="fas fa-external-link-alt"></i> View Customer Profile
                    </a>
                </div>
                <?php endif; ?>
            </div>
            <div class="admin-card-body">
                <?php if (isset($booking['customer_id']) && !empty($booking['customer_id'])): ?>
                <div class="detail-section">
                    <div class="detail-row">
                        <div class="detail-group">
                            <div class="detail-label">Name</div>
                            <div class="detail-value"><?php echo $booking['customer_name']; ?></div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Email</div>
                            <div class="detail-value"><?php echo $booking['customer_email']; ?></div>
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-group">
                            <div class="detail-label">Phone</div>
                            <div class="detail-value"><?php echo $booking['customer_phone']; ?></div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Country</div>
                            <div class="detail-value"><?php echo $booking['country']; ?></div>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <p>No customer information available</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Additional Information Card -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h2>Additional Information</h2>
            </div>
            <div class="admin-card-body">
                <div class="detail-section">
                    <div class="detail-row full-width">
                        <div class="detail-group">
                            <div class="detail-label">Special Requests</div>
                            <div class="detail-value"><?php echo !empty($booking['special_requests']) ? $booking['special_requests'] : 'None'; ?></div>
                        </div>
                    </div>
                    <div class="detail-row full-width">
                        <div class="detail-group">
                            <div class="detail-label">Referral Source</div>
                            <div class="detail-value"><?php echo !empty($booking['referral_source']) ? $booking['referral_source'] : 'Not specified'; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Consultation Notes Card -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h2>Consultation Notes</h2>
                <button class="btn-primary" id="add-note-btn">
                    <i class="fas fa-plus"></i> Add Note
                </button>
            </div>
            <div class="admin-card-body">
                <div class="notes-container">
                    <?php if ($notes_result && $notes_result->num_rows > 0): ?>
                        <?php while ($note = $notes_result->fetch_assoc()): ?>
                        <div class="note-item">
                            <div class="note-header">
                                <div class="note-meta">
                                    <span class="note-author"><?php echo $note['admin_name']; ?></span>
                                    <span class="note-date"><?php echo date('M j, Y g:i A', strtotime($note['created_at'])); ?></span>
                                </div>
                                <div class="note-actions">
                                    <button class="action-btn edit-btn edit-note-btn" data-note-id="<?php echo $note['id']; ?>" data-note-text="<?php echo htmlspecialchars($note['notes']); ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="action-btn delete-btn delete-note-btn" data-note-id="<?php echo $note['id']; ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="note-content"><?php echo nl2br($note['notes']); ?></div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <p>No consultation notes yet</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Note Modal -->
<div class="modal" id="note-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add Consultation Note</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="note-form">
                <input type="hidden" id="note-id" name="note_id" value="">
                <input type="hidden" id="appointment-id" name="appointment_id" value="<?php echo $booking_id; ?>">
                
                <div class="form-group">
                    <label for="note-content">Note Content</label>
                    <textarea id="note-content" name="note_content" rows="6" required></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary modal-close">Cancel</button>
            <button class="btn-primary" id="save-note">Save Note</button>
        </div>
    </div>
</div>

<!-- Change Status Modal -->
<div class="modal" id="status-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Change Booking Status</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="status-form">
                <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
                
                <div class="form-group">
                    <label for="booking-status">Status</label>
                    <select id="booking-status" name="status" required>
                        <option value="pending" <?php echo $booking['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="confirmed" <?php echo $booking['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                        <option value="completed" <?php echo $booking['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo $booking['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        <option value="no-show" <?php echo $booking['status'] == 'no-show' ? 'selected' : ''; ?>>No Show</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="payment-status">Payment Status</label>
                    <select id="payment-status" name="payment_status" required>
                        <option value="unpaid" <?php echo $booking['payment_status'] == 'unpaid' ? 'selected' : ''; ?>>Unpaid</option>
                        <option value="paid" <?php echo $booking['payment_status'] == 'paid' ? 'selected' : ''; ?>>Paid</option>
                        <option value="refunded" <?php echo $booking['payment_status'] == 'refunded' ? 'selected' : ''; ?>>Refunded</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="status-note">Note (Optional)</label>
                    <textarea id="status-note" name="note" rows="3"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary modal-close">Cancel</button>
            <button class="btn-primary" id="save-status">Save Changes</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add note button
    const addNoteBtn = document.getElementById('add-consultation-note');
    const addNoteModal = document.getElementById('note-modal');
    const noteForm = document.getElementById('note-form');
    
    addNoteBtn.addEventListener('click', function() {
        document.querySelector('#note-modal .modal-header h3').textContent = 'Add Consultation Note';
        document.getElementById('note-id').value = '';
        document.getElementById('note-content').value = '';
        addNoteModal.style.display = 'flex';
    });
    
    // Add note from dropdown
    const addNoteLink = document.getElementById('add-note');
    addNoteLink.addEventListener('click', function(e) {
        e.preventDefault();
        addNoteBtn.click();
    });
    
    // Edit note buttons
    const editNoteButtons = document.querySelectorAll('.edit-note');
    editNoteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const noteId = this.getAttribute('data-id');
            document.querySelector('#note-modal .modal-header h3').textContent = 'Edit Consultation Note';
            document.getElementById('note-id').value = noteId;
            
            // In a real app, you would fetch the note content from the server
            const noteContent = this.closest('.note-item').querySelector('.note-content').textContent;
            document.getElementById('note-content').value = noteContent;
            
            addNoteModal.style.display = 'flex';
        });
    });
    
    // Change status button
    const changeStatusBtn = document.getElementById('change-status');
    const statusModal = document.getElementById('status-modal');
    
    changeStatusBtn.addEventListener('click', function(e) {
        e.preventDefault();
        statusModal.style.display = 'flex';
    });
    
    // Close modals
    const modalCloseButtons = document.querySelectorAll('.modal-close');
    modalCloseButtons.forEach(button => {
        button.addEventListener('click', function() {
            addNoteModal.style.display = 'none';
            statusModal.style.display = 'none';
        });
    });
    
    // Close modal if clicked outside
    window.addEventListener('click', function(event) {
        if (event.target === addNoteModal) {
            addNoteModal.style.display = 'none';
        }
        if (event.target === statusModal) {
            statusModal.style.display = 'none';
        }
    });
    
    // Save note
    const saveNoteBtn = document.getElementById('save-note');
    saveNoteBtn.addEventListener('click', function() {
        if (document.getElementById('note-content').value.trim() === '') {
            alert('Please enter a note.');
            return;
        }
        
        // In a real app, you would send this to the server via AJAX
        alert('Note saved successfully!');
        
        // In a demo, we'll just add a new note to the list
        const noteId = document.getElementById('note-id').value;
        if (!noteId) {
            // This is a new note
            const notesList = document.querySelector('.consultation-notes-list');
            const emptyNotes = notesList.querySelector('.empty-notes');
            if (emptyNotes) {
                emptyNotes.remove();
            }
            
            const newNote = document.createElement('div');
            newNote.className = 'note-item';
            newNote.innerHTML = `
                <div class="note-header">
                    <div class="note-meta">
                        <span class="note-author">${document.querySelector('.admin-user-details h4').textContent}</span>
                        <span class="note-date">Just now</span>
                    </div>
                    <div class="note-actions">
                        <button class="note-action edit-note"><i class="fas fa-edit"></i></button>
                        <button class="note-action delete-note"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
                <div class="note-content">${document.getElementById('note-content').value.replace(/\n/g, '<br>')}</div>
            `;
            
            notesList.insertBefore(newNote, notesList.firstChild);
        } else {
            // This is an edit to an existing note
            const editButtons = document.querySelectorAll('.edit-note');
            let noteElement;
            
            editButtons.forEach(button => {
                if (button.getAttribute('data-id') === noteId) {
                    noteElement = button.closest('.note-item');
                }
            });
            
            if (noteElement) {
                noteElement.querySelector('.note-content').innerHTML = document.getElementById('note-content').value.replace(/\n/g, '<br>');
            }
        }
        
        // Close modal
        addNoteModal.style.display = 'none';
    });
    
    // Save status change
    const saveStatusBtn = document.getElementById('save-status');
    saveStatusBtn.addEventListener('click', function() {
        // In a real app, you would send this to the server via AJAX
        alert('Status updated successfully!');
        
        // Update status badges in the UI
        const newStatus = document.getElementById('booking-status').value;
        const newPaymentStatus = document.getElementById('payment-status').value;
        
        document.querySelectorAll('.status-badge.status-' + '<?php echo strtolower($booking['status']); ?>').forEach(badge => {
            badge.className = 'status-badge status-' + newStatus;
            badge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
        });
        
        document.querySelector('.status-badge.status-' + '<?php echo strtolower($booking['payment_status']); ?>').className = 'status-badge status-' + newPaymentStatus;
        document.querySelector('.status-badge.status-' + newPaymentStatus).textContent = newPaymentStatus.charAt(0).toUpperCase() + newPaymentStatus.slice(1);
        
        // Close modal
        statusModal.style.display = 'none';
    });
});
</script>

<style>
.booking-detail-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.booking-detail-card, .consultation-notes-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.booking-detail-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #eee;
}

.booking-id h3 {
    margin: 0 0 5px 0;
    color: var(--color-primary);
}

.booking-detail-body {
    padding: 20px;
}

.booking-detail-section {
    margin-bottom: 30px;
}

.booking-detail-section h4 {
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

.consultation-notes-card .card-header {
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

.consultation-notes-list {
    padding: 20px;
}

.note-item {
    background: #f9f9f9;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
}

.note-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.note-meta {
    font-size: 14px;
    color: #777;
}

.note-author {
    font-weight: 600;
    margin-right: 10px;
}

.note-actions {
    display: flex;
    gap: 5px;
}

.note-action {
    background: none;
    border: none;
    color: #777;
    cursor: pointer;
    padding: 2px 5px;
    border-radius: 4px;
}

.note-action:hover {
    background: #eee;
    color: var(--color-primary);
}

.note-content {
    line-height: 1.5;
}

.empty-notes {
    text-align: center;
    padding: 30px 0;
    color: #777;
}

/* Status badges */
.status-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.status-pending {
    background-color: #fff3cd;
    color: #856404;
}

.status-confirmed {
    background-color: #d1ecf1;
    color: #0c5460;
}

.status-completed {
    background-color: #e1f3e8;
    color: #1e7e34;
}

.status-cancelled, .status-no-show {
    background-color: #f8d7da;
    color: #721c24;
}

.status-paid {
    background-color: #e1f3e8;
    color: #1e7e34;
}

.status-unpaid {
    background-color: #fff3cd;
    color: #856404;
}

.status-refunded {
    background-color: #f8d7da;
    color: #721c24;
}

.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-toggle {
    background-color: #f8f9fa;
    border: 1px solid #ddd;
    padding: 8px 12px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
}

.dropdown-menu {
    position: absolute;
    right: 0;
    top: 100%;
    z-index: 10;
    min-width: 180px;
    background: white;
    border-radius: 4px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    display: none;
    padding: 5px 0;
}

.dropdown-toggle:hover + .dropdown-menu,
.dropdown-menu:hover {
    display: block;
}

.dropdown-menu a {
    display: block;
    padding: 8px 15px;
    text-decoration: none;
    color: #333;
}

.dropdown-menu a:hover {
    background: #f8f9fa;
}

.dropdown-menu a.text-danger {
    color: #dc3545;
}

/* Responsive layout */
@media (min-width: 992px) {
    .booking-detail-container {
        flex-direction: row;
        align-items: flex-start;
    }
    
    .booking-detail-card {
        flex: 3;
    }
    
    .consultation-notes-card {
        flex: 2;
    }
}
</style>

<?php include('includes/footer.php'); ?> 