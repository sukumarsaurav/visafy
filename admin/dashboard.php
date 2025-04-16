<?php 
include('includes/header.php');
include('includes/db_connection.php');

// Get dashboard statistics
$total_appointments = getRecordCount('appointments');
$total_customers = getRecordCount('customers');
$upcoming_appointments = getRecordCount('appointments', "appointment_datetime >= NOW() AND status IN ('pending', 'confirmed')");
$today_appointments = getRecordCount('appointments', "DATE(appointment_datetime) = CURDATE()");

// Get recent appointments
$recent_sql = "SELECT * FROM appointments ORDER BY created_at DESC LIMIT 5";
$recent_result = executeQuery($recent_sql);

// Get upcoming appointments for today and tomorrow
$upcoming_sql = "SELECT * FROM appointments 
                WHERE appointment_datetime >= NOW() 
                AND appointment_datetime <= DATE_ADD(NOW(), INTERVAL 2 DAY)
                AND status IN ('pending', 'confirmed')
                ORDER BY appointment_datetime ASC 
                LIMIT 5";
$upcoming_result = executeQuery($upcoming_sql);
?>

<div class="admin-content-header">
    <h1>Dashboard</h1>
    <p>Welcome back! Here's an overview of your consultation bookings.</p>
</div>

<!-- Dashboard Stats -->
<div class="dashboard-stats">
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo $total_appointments; ?></h3>
            <p>Total Bookings</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo $total_customers; ?></h3>
            <p>Total Customers</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-calendar-day"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo $today_appointments; ?></h3>
            <p>Today's Bookings</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo $upcoming_appointments; ?></h3>
            <p>Upcoming Bookings</p>
        </div>
    </div>
</div>

<!-- Recent Bookings Section -->
<div class="dashboard-main">
    <!-- Left Column -->
    <div class="dashboard-column">
        <div class="dashboard-card">
            <div class="card-header">
                <h3>Recent Bookings</h3>
                <a href="bookings.php" class="view-all">View All</a>
            </div>
            <div class="card-content">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($recent_result && $recent_result->num_rows > 0): ?>
                            <?php while($booking = $recent_result->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <div class="table-user">
                                            <span class="user-name"><?php echo $booking['first_name'] . ' ' . $booking['last_name']; ?></span>
                                            <span class="user-email"><?php echo $booking['email']; ?></span>
                                        </div>
                                    </td>
                                    <td><?php echo $booking['consultation_type']; ?></td>
                                    <td><?php echo date('M j, Y g:i A', strtotime($booking['appointment_datetime'])); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($booking['status']); ?>">
                                            <?php echo ucfirst($booking['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align: center;">No recent bookings</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Right Column -->
    <div class="dashboard-column">
        <div class="dashboard-card">
            <div class="card-header">
                <h3>Upcoming Consultations</h3>
                <a href="bookings.php?filter=upcoming" class="view-all">View All</a>
            </div>
            <div class="card-content">
                <div class="upcoming-list">
                    <?php if($upcoming_result && $upcoming_result->num_rows > 0): ?>
                        <?php while($upcoming = $upcoming_result->fetch_assoc()): 
                            $is_today = date('Y-m-d', strtotime($upcoming['appointment_datetime'])) === date('Y-m-d');
                            $date_label = $is_today ? 'Today' : 'Tomorrow';
                        ?>
                            <div class="upcoming-item">
                                <div class="upcoming-time">
                                    <span class="day-label"><?php echo $date_label; ?></span>
                                    <span class="time"><?php echo date('g:i A', strtotime($upcoming['appointment_datetime'])); ?></span>
                                </div>
                                <div class="upcoming-details">
                                    <span class="name"><?php echo $upcoming['first_name'] . ' ' . $upcoming['last_name']; ?></span>
                                    <span class="consultation-type"><?php echo $upcoming['consultation_type']; ?></span>
                                </div>
                                <div class="upcoming-status">
                                    <span class="status-badge status-<?php echo strtolower($upcoming['status']); ?>">
                                        <?php echo ucfirst($upcoming['status']); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="no-appointments">
                            <p>No upcoming appointments for today or tomorrow.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Mini Calendar -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3>Calendar</h3>
                <div class="calendar-nav">
                    <button class="calendar-nav-btn" id="prev-month"><i class="fas fa-chevron-left"></i></button>
                    <span id="current-month"><?php echo date('F Y'); ?></span>
                    <button class="calendar-nav-btn" id="next-month"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
            <div class="card-content">
                <div id="mini-calendar" class="mini-calendar">
                    <!-- Calendar will be rendered here by JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add JavaScript for the calendar -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calendar code would go here
});
</script>

<!-- Add this before the closing </body> tag -->
<script src="js/calendar.js"></script>

<?php include('includes/footer.php'); ?> 