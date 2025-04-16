<?php include('includes/header.php');
include('includes/db_connection.php');

// Get bookings from database
$sql = "SELECT * FROM appointments ORDER BY appointment_datetime DESC";
$result = executeQuery($sql);
?>

<div class="admin-content-header">
    <h1>Appointments</h1>
    <p>Manage all consultation bookings</p>
</div>

<div class="admin-controls">
    <div class="admin-search">
        <input type="text" placeholder="Search bookings..." id="booking-search">
        <button><i class="fas fa-search"></i></button>
    </div>
    
    <div class="admin-filters">
        <select id="status-filter">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
            <option value="no-show">No-Show</option>
        </select>
        
        <select id="date-filter">
            <option value="">All Dates</option>
            <option value="today">Today</option>
            <option value="tomorrow">Tomorrow</option>
            <option value="this-week">This Week</option>
            <option value="next-week">Next Week</option>
            <option value="this-month">This Month</option>
        </select>
        
        <select id="type-filter">
            <option value="">All Types</option>
            <option value="Video Consultation">Video</option>
            <option value="Phone Consultation">Phone</option>
            <option value="In-Person Consultation">In-Person</option>
        </select>
    </div>
    
    <div class="admin-actions">
        <button class="btn-primary" id="add-booking-btn">
            <i class="fas fa-plus"></i> Add New Booking
        </button>
    </div>
</div>

<div class="admin-table-container">
    <div class="add-new-container">
        <a href="add_booking.php" class="add-new-btn">
            <i class="fas fa-plus"></i> Add New Booking
        </a>
    </div>
    <table class="admin-table booking-table">
        <thead>
            <tr>
                <th>
                    <input type="checkbox" id="select-all-bookings">
                </th>
                <th>ID <i class="fas fa-sort"></i></th>
                <th>Customer <i class="fas fa-sort"></i></th>
                <th>Type <i class="fas fa-sort"></i></th>
                <th>Date & Time <i class="fas fa-sort"></i></th>
                <th>Status <i class="fas fa-sort"></i></th>
                <th>Payment <i class="fas fa-sort"></i></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($booking = $result->fetch_assoc()): ?>
                    <tr>
                        <td><input type="checkbox" class="booking-select"></td>
                        <td>#<?php echo str_pad($booking['id'], 6, '0', STR_PAD_LEFT); ?></td>
                        <td>
                            <div class="customer-info">
                                <span class="customer-name"><?php echo $booking['first_name'] . ' ' . $booking['last_name']; ?></span>
                                <span class="customer-email"><?php echo $booking['email']; ?></span>
                            </div>
                        </td>
                        <td><?php echo $booking['consultation_type']; ?></td>
                        <td>
                            <div class="booking-datetime">
                                <span class="booking-date"><?php echo date('M j, Y', strtotime($booking['appointment_datetime'])); ?></span>
                                <span class="booking-time"><?php echo date('g:i A', strtotime($booking['appointment_datetime'])); ?></span>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge status-<?php echo strtolower($booking['status']); ?>">
                                <?php echo ucfirst($booking['status']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="status-badge status-<?php echo strtolower($booking['payment_status']); ?>">
                                <?php echo ucfirst($booking['payment_status']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="view_booking.php?id=<?php echo $booking['id']; ?>" class="action-btn view-btn" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit_booking.php?id=<?php echo $booking['id']; ?>" class="action-btn edit-btn" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="action-btn delete-btn" title="Delete" 
                                        onclick="confirmDelete(<?php echo $booking['id']; ?>, '<?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?>')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center;">No bookings found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="pagination-container">
    <ul class="pagination">
        <?php if ($current_page > 1): ?>
            <li><a href="?page=1"><i class="fas fa-angle-double-left"></i></a></li>
            <li><a href="?page=<?php echo $current_page - 1; ?>"><i class="fas fa-angle-left"></i></a></li>
        <?php else: ?>
            <li class="disabled"><span><i class="fas fa-angle-double-left"></i></span></li>
            <li class="disabled"><span><i class="fas fa-angle-left"></i></span></li>
        <?php endif; ?>
        
        <?php
        for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++):
        ?>
            <li class="<?php echo ($i == $current_page) ? 'active' : ''; ?>">
                <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
        
        <?php if ($current_page < $total_pages): ?>
            <li><a href="?page=<?php echo $current_page + 1; ?>"><i class="fas fa-angle-right"></i></a></li>
            <li><a href="?page=<?php echo $total_pages; ?>"><i class="fas fa-angle-double-right"></i></a></li>
        <?php else: ?>
            <li class="disabled"><span><i class="fas fa-angle-right"></i></span></li>
            <li class="disabled"><span><i class="fas fa-angle-double-right"></i></span></li>
        <?php endif; ?>
    </ul>
</div>

<!-- Add/Edit Booking Modal -->
<div class="modal" id="edit-booking-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add New Booking</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="booking-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="consultation-type">Consultation Type</label>
                        <select id="consultation-type" name="consultation_type" required>
                            <option value="">Select Type</option>
                            <option value="Video Consultation">Video Consultation</option>
                            <option value="Phone Consultation">Phone Consultation</option>
                            <option value="In-Person Consultation">In-Person Consultation</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="booking-date">Date</label>
                        <input type="date" id="booking-date" name="booking_date" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="booking-time">Time</label>
                        <input type="time" id="booking-time" name="booking_time" required>
                    </div>
                    <div class="form-group">
                        <label for="booking-status">Status</label>
                        <select id="booking-status" name="booking_status" required>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="no-show">No-Show</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="client-first-name">First Name</label>
                        <input type="text" id="client-first-name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="client-last-name">Last Name</label>
                        <input type="text" id="client-last-name" name="last_name" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="client-email">Email</label>
                        <input type="email" id="client-email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="client-phone">Phone</label>
                        <input type="tel" id="client-phone" name="phone" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="booking-notes">Notes</label>
                    <textarea id="booking-notes" name="notes" rows="3"></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-secondary modal-close">Cancel</button>
                    <button type="submit" class="btn-primary">Save Booking</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add Booking Button
        const addBookingBtn = document.getElementById('add-booking-btn');
        const editBookingModal = document.getElementById('edit-booking-modal');
        const modalClose = document.querySelectorAll('.modal-close');
        
        addBookingBtn.addEventListener('click', function() {
            // Clear form
            document.getElementById('booking-form').reset();
            // Change modal title
            document.querySelector('#edit-booking-modal .modal-header h3').textContent = 'Add New Booking';
            // Show modal
            editBookingModal.style.display = 'flex';
        });
        
        // Edit Booking Buttons
        const editBookingBtns = document.querySelectorAll('.edit-booking');
        editBookingBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const bookingId = this.getAttribute('data-id');
                // In a real app, you would fetch booking details from server
                // and populate the form
                
                // Change modal title
                document.querySelector('#edit-booking-modal .modal-header h3').textContent = 'Edit Booking #' + bookingId;
                // Show modal
                editBookingModal.style.display = 'flex';
            });
        });
        
        // Delete Booking Buttons
        const deleteBookingBtns = document.querySelectorAll('.delete-booking');
        deleteBookingBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const bookingId = this.getAttribute('data-id');
                if (confirm('Are you sure you want to delete booking #' + bookingId + '?')) {
                    // In a real app, you would send an AJAX request to delete the booking
                    alert('Booking #' + bookingId + ' has been deleted');
                }
            });
        });
        
        // Close Modal
        modalClose.forEach(btn => {
            btn.addEventListener('click', function() {
                editBookingModal.style.display = 'none';
            });
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === editBookingModal) {
                editBookingModal.style.display = 'none';
            }
        });
        
        // Select All Checkbox
        const selectAllCheckbox = document.getElementById('select-all-bookings');
        const bookingCheckboxes = document.querySelectorAll('.booking-select');
        
        selectAllCheckbox.addEventListener('change', function() {
            bookingCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    });
</script>

<?php include('includes/footer.php'); ?> 