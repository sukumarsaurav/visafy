<?php include('includes/header.php');
include('includes/db_connection.php');

// Get customers from database
$sql = "SELECT * FROM customers ORDER BY created_at DESC";
$result = executeQuery($sql);
?>

<div class="admin-content-header">
    <h1>Customer Management</h1>
    <p>View and manage all customer information</p>
</div>

<div class="admin-controls">
    <div class="admin-search">
        <input type="text" placeholder="Search customers..." id="customer-search">
        <button><i class="fas fa-search"></i></button>
    </div>
    
    <div class="admin-actions">
        <button class="btn-primary" id="add-customer-btn">
            <i class="fas fa-plus"></i> Add New Customer
        </button>
    </div>
</div>

<div class="admin-table-container">
    <table class="admin-table customer-table">
        <thead>
            <tr>
                <th>
                    <input type="checkbox" id="select-all-customers">
                </th>
                <th>ID <i class="fas fa-sort"></i></th>
                <th>Customer <i class="fas fa-sort"></i></th>
                <th>Contact <i class="fas fa-sort"></i></th>
                <th>Country <i class="fas fa-sort"></i></th>
                <th>Consultations <i class="fas fa-sort"></i></th>
                <th>Created <i class="fas fa-sort"></i></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($customer = $result->fetch_assoc()): 
                    // Get consultation count
                    $customer_email = $customer['email'];
                    $consult_sql = "SELECT COUNT(*) as count FROM appointments WHERE email = '$customer_email'";
                    $consult_result = executeQuery($consult_sql);
                    $consult_count = 0;
                    if ($consult_result && $consult_result->num_rows > 0) {
                        $consult_data = $consult_result->fetch_assoc();
                        $consult_count = $consult_data['count'];
                    }
                ?>
                    <tr>
                        <td><input type="checkbox" class="customer-select"></td>
                        <td>#<?php echo str_pad($customer['id'], 4, '0', STR_PAD_LEFT); ?></td>
                        <td>
                            <div class="customer-info">
                                <span class="customer-name"><?php echo $customer['first_name'] . ' ' . $customer['last_name']; ?></span>
                                <span class="customer-email"><?php echo $customer['email']; ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="customer-contact">
                                <span class="customer-phone"><?php echo $customer['phone']; ?></span>
                            </div>
                        </td>
                        <td><?php echo $customer['country'] ?? 'N/A'; ?></td>
                        <td>
                            <span class="consultation-count"><?php echo $consult_count; ?></span>
                        </td>
                        <td>
                            <div class="created-date">
                                <span><?php echo date('M j, Y', strtotime($customer['created_at'])); ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="view_customer.php?id=<?php echo $customer['id']; ?>" class="action-btn view-btn" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit_customer.php?id=<?php echo $customer['id']; ?>" class="action-btn edit-btn" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="action-btn delete-btn" title="Delete" 
                                        onclick="confirmDelete(<?php echo $customer['id']; ?>, '<?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?>')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center;">No customers found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="admin-table-footer">
    <div class="table-info">
        Showing <span>1-10</span> of <span>24</span> customers
    </div>
    
    <div class="pagination">
        <button class="page-btn" disabled><i class="fas fa-chevron-left"></i></button>
        <button class="page-btn active">1</button>
        <button class="page-btn">2</button>
        <button class="page-btn">3</button>
        <button class="page-btn"><i class="fas fa-chevron-right"></i></button>
    </div>
</div>

<!-- Customer Details Modal -->
<div class="modal" id="customer-details-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Customer Details</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body" id="customer-details-content">
            <div class="customer-profile">
                <div class="customer-profile-header">
                    <div class="customer-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="customer-profile-name">
                        <h4>John Smith</h4>
                        <p>john.smith@example.com</p>
                        <p class="customer-id">#0001</p>
                    </div>
                </div>
                
                <div class="detail-section">
                    <h5>Personal Information</h5>
                    <div class="details-grid">
                        <div class="detail-label">Phone:</div>
                        <div class="detail-value">+1 (123) 456-7890</div>
                        
                        <div class="detail-label">Country:</div>
                        <div class="detail-value">United States</div>
                        
                        <div class="detail-label">Citizenship:</div>
                        <div class="detail-value">American</div>
                        
                        <div class="detail-label">Date of Birth:</div>
                        <div class="detail-value">Jan 15, 1985</div>
                    </div>
                </div>
                
                <div class="detail-section">
                    <h5>Immigration Information</h5>
                    <div class="details-grid">
                        <div class="detail-label">Current Status:</div>
                        <div class="detail-value">Work Permit Holder</div>
                        
                        <div class="detail-label">Passport Number:</div>
                        <div class="detail-value">A12345678</div>
                    </div>
                </div>
                
                <div class="detail-section">
                    <h5>Consultations History</h5>
                    <table class="details-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>May 15, 2023</td>
                                <td>Video Consultation</td>
                                <td><span class="status-badge status-completed">Completed</span></td>
                            </tr>
                            <tr>
                                <td>Mar 10, 2023</td>
                                <td>Phone Consultation</td>
                                <td><span class="status-badge status-completed">Completed</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="detail-section">
                    <h5>Notes</h5>
                    <div class="customer-notes">
                        <p>Client is interested in Express Entry. Provided initial assessment on May 15.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary modal-close">Close</button>
            <button class="btn-primary" id="edit-customer-btn">Edit Customer</button>
        </div>
    </div>
</div>

<!-- Add/Edit Customer Modal -->
<div class="modal" id="edit-customer-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add New Customer</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="customer-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first-name">First Name</label>
                        <input type="text" id="first-name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="last-name">Last Name</label>
                        <input type="text" id="last-name" name="last_name" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="country">Country</label>
                        <input type="text" id="country" name="country">
                    </div>
                    <div class="form-group">
                        <label for="citizenship">Citizenship</label>
                        <input type="text" id="citizenship" name="citizenship">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="date-of-birth">Date of Birth</label>
                        <input type="date" id="date-of-birth" name="date_of_birth">
                    </div>
                    <div class="form-group">
                        <label for="immigration-status">Immigration Status</label>
                        <select id="immigration-status" name="immigration_status">
                            <option value="">Select Status</option>
                            <option value="citizen">Citizen</option>
                            <option value="pr">Permanent Resident</option>
                            <option value="work_permit">Work Permit Holder</option>
                            <option value="study_permit">Study Permit Holder</option>
                            <option value="visitor">Visitor</option>
                            <option value="none">None of the above</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="customer-notes">Notes</label>
                    <textarea id="customer-notes" name="notes" rows="3"></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-secondary modal-close">Cancel</button>
                    <button type="submit" class="btn-primary">Save Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // View Customer Details
        const viewCustomerBtns = document.querySelectorAll('.view-customer');
        const customerDetailsModal = document.getElementById('customer-details-modal');
        
        viewCustomerBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const customerId = this.getAttribute('data-id');
                // In a real app, you would fetch customer details from server
                // and populate the modal
                
                // Show modal
                customerDetailsModal.style.display = 'flex';
            });
        });
        
        // Edit Customer
        const editCustomerBtns = document.querySelectorAll('.edit-customer');
        const editCustomerModal = document.getElementById('edit-customer-modal');
        
        editCustomerBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const customerId = this.getAttribute('data-id');
                // In a real app, you would fetch customer details from server
                // and populate the form
                
                // Change modal title
                document.querySelector('#edit-customer-modal .modal-header h3').textContent = 'Edit Customer';
                
                // Show modal
                editCustomerModal.style.display = 'flex';
            });
        });
        
        // Edit Customer from details modal
        const editCustomerBtn = document.getElementById('edit-customer-btn');
        if (editCustomerBtn) {
            editCustomerBtn.addEventListener('click', function() {
                customerDetailsModal.style.display = 'none';
                editCustomerModal.style.display = 'flex';
            });
        }
        
        // Add new customer
        const addCustomerBtn = document.getElementById('add-customer-btn');
        if (addCustomerBtn) {
            addCustomerBtn.addEventListener('click', function() {
                // Clear form fields for new customer
                document.getElementById('customer-form').reset();
                // Change modal title
                document.querySelector('#edit-customer-modal .modal-header h3').textContent = 'Add New Customer';
                // Show modal
                editCustomerModal.style.display = 'flex';
            });
        }
        
        // Close modals
        const modalCloseButtons = document.querySelectorAll('.modal-close');
        modalCloseButtons.forEach(button => {
            button.addEventListener('click', function() {
                customerDetailsModal.style.display = 'none';
                editCustomerModal.style.display = 'none';
            });
        });
        
        // Close modal if clicked outside
        window.addEventListener('click', function(event) {
            if (event.target === customerDetailsModal) {
                customerDetailsModal.style.display = 'none';
            }
            if (event.target === editCustomerModal) {
                editCustomerModal.style.display = 'none';
            }
        });
    });
</script>

<?php include('includes/footer.php'); ?> 