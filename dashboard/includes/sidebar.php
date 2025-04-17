<?php
// Get current page to highlight active link
$current_page = basename($_SERVER['PHP_SELF']);
$user_type = $_SESSION['user_type'] ?? '';
?>

<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-primary sidebar collapse">
    <div class="position-sticky pt-3">
        <div class="px-3 mb-3 text-center">
            <h5 class="text-white">Visafy</h5>
            <div class="user-info mt-3">
                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 60px; height: 60px;">
                    <i class="bi bi-person-fill" style="font-size: 40px; color: var(--primary-color);"></i>
                </div>
                <h6 class="text-white mt-2 mb-0"><?php echo htmlspecialchars($_SESSION['user_name'] ?? $user_data['name'] ?? 'User'); ?></h6>
                <p class="text-white-50 small mb-2"><?php echo ucfirst($user_type); ?></p>
            </div>
        </div>
        
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-white-50">
            <span>Main</span>
        </h6>
        
        <ul class="nav flex-column">
            <?php if ($user_type === 'professional'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'index.php' ? 'active' : ''; ?>" href="index.php">
                        <i class="bi bi-speedometer2"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'profile.php' ? 'active' : ''; ?>" href="profile.php">
                        <i class="bi bi-person"></i>
                        Profile
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'clients.php' ? 'active' : ''; ?>" href="clients.php">
                        <i class="bi bi-people"></i>
                        Clients
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'cases.php' ? 'active' : ''; ?>" href="cases.php">
                        <i class="bi bi-folder"></i>
                        Cases
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'documents.php' ? 'active' : ''; ?>" href="documents.php">
                        <i class="bi bi-file-earmark"></i>
                        Documents
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'messages.php' ? 'active' : ''; ?>" href="messages.php">
                        <i class="bi bi-chat-dots"></i>
                        Messages
                    </a>
                </li>
            <?php elseif ($user_type === 'applicant'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'index.php' ? 'active' : ''; ?>" href="index.php">
                        <i class="bi bi-speedometer2"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'applications.php' ? 'active' : ''; ?>" href="applications.php">
                        <i class="bi bi-file-earmark-text"></i>
                        My Applications
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'eligibility.php' ? 'active' : ''; ?>" href="eligibility.php">
                        <i class="bi bi-clipboard-check"></i>
                        Eligibility Check
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'appointments.php' ? 'active' : ''; ?>" href="appointments.php">
                        <i class="bi bi-calendar"></i>
                        Appointments
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'documents.php' ? 'active' : ''; ?>" href="documents.php">
                        <i class="bi bi-file-earmark"></i>
                        Documents
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'messages.php' ? 'active' : ''; ?>" href="messages.php">
                        <i class="bi bi-chat-dots"></i>
                        Messages
                    </a>
                </li>
            <?php elseif ($user_type === 'employer'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'index.php' ? 'active' : ''; ?>" href="index.php">
                        <i class="bi bi-speedometer2"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'profile.php' ? 'active' : ''; ?>" href="profile.php">
                        <i class="bi bi-building"></i>
                        Company Profile
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'jobs.php' ? 'active' : ''; ?>" href="jobs.php">
                        <i class="bi bi-briefcase"></i>
                        Job Postings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'applications.php' ? 'active' : ''; ?>" href="applications.php">
                        <i class="bi bi-people"></i>
                        Applicants
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'lmia.php' ? 'active' : ''; ?>" href="lmia.php">
                        <i class="bi bi-file-earmark-check"></i>
                        LMIA Applications
                    </a>
                </li>
            <?php endif; ?>
        </ul>
        
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-white-50">
            <span>Account</span>
        </h6>
        
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'settings.php' ? 'active' : ''; ?>" href="settings.php">
                    <i class="bi bi-gear"></i>
                    Settings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../../logout.php">
                    <i class="bi bi-box-arrow-right"></i>
                    Sign Out
                </a>
            </li>
        </ul>
    </div>
</nav> 