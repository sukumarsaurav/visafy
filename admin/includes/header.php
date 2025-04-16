<?php
session_start();
// Check if user is logged in, if not redirect to login page
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

// Get current page for active menu highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | CANEXT Immigration</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Lora:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    
    <!-- Admin CSS -->
    <link rel="stylesheet" href="css/admin-styles.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <div class="admin-sidebar-header">
                <h2>CANEXT Admin</h2>
                <button id="sidebar-toggle" class="sidebar-toggle-btn">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <div class="admin-user-info">
                <div class="admin-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="admin-user-details">
                    <h4><?php echo $_SESSION['admin_username']; ?></h4>
                    <span>Administrator</span>
                </div>
            </div>
            
            <nav class="admin-nav">
                <ul>
                    <li class="<?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>">
                        <a href="dashboard.php">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="<?php echo $current_page === 'bookings.php' ? 'active' : ''; ?>">
                        <a href="bookings.php">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Bookings</span>
                        </a>
                    </li>
                    <li class="<?php echo $current_page === 'customers.php' ? 'active' : ''; ?>">
                        <a href="customers.php">
                            <i class="fas fa-users"></i>
                            <span>Customers</span>
                        </a>
                    </li>
                    <li class="<?php echo $current_page === 'news.php' ? 'active' : ''; ?>">
                        <a href="news.php">
                            <i class="fas fa-newspaper"></i>
                            <span>News Articles</span>
                        </a>
                    </li>
                    <li class="<?php echo $current_page === 'blog.php' || $current_page === 'blog_category_edit.php' || $current_page === 'blog_post_edit.php' ? 'active' : ''; ?>">
                        <a href="blog.php">
                            <i class="fas fa-blog"></i>
                            <span>Blog Manager</span>
                        </a>
                    </li>
                    <li class="<?php echo $current_page === 'guides.php' || $current_page === 'guide_category_edit.php' || $current_page === 'guide_edit.php' ? 'active' : ''; ?>">
                        <a href="guides.php">
                            <i class="fas fa-book"></i>
                            <span>Guides Manager</span>
                        </a>
                    </li>
                    <li class="<?php echo $current_page === 'video_tutorials.php' || $current_page === 'video_tutorial_edit.php' ? 'active' : ''; ?>">
                        <a href="video_tutorials.php">
                            <i class="fas fa-video"></i>
                            <span>Video Tutorials</span>
                        </a>
                    </li>
                    <li class="<?php echo $current_page === 'downloadable_resources.php' || $current_page === 'resource_edit.php' ? 'active' : ''; ?>">
                        <a href="downloadable_resources.php">
                            <i class="fas fa-file-download"></i>
                            <span>Downloadable Resources</span>
                        </a>
                    </li>
                    <li class="<?php echo $current_page === 'faq.php' ? 'active' : ''; ?>">
                        <a href="faq.php">
                            <i class="fas fa-question-circle"></i>
                            <span>FAQ Manager</span>
                        </a>
                    </li>
                    <li class="<?php echo $current_page === 'settings.php' ? 'active' : ''; ?>">
                        <a href="settings.php">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                    <li>
                        <a href="logout.php">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        
        <!-- Main Content -->
        <div class="admin-main">
            <!-- Top Header -->
            <div class="admin-header">
                <div class="admin-header-left">
                    <button id="mobile-menu-toggle" class="mobile-toggle-btn" aria-label="Toggle Navigation">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="page-title">
                        <h2><?php echo ucfirst(str_replace(array('.php', '_'), array('', ' '), $current_page)); ?></h2>
                    </div>
                </div>
                <div class="admin-header-right">
                   
                    <div class="admin-profile-dropdown">
                        <button class="profile-dropdown-btn">
                            <i class="fas fa-user-circle"></i>
                            <span><?php echo $_SESSION['admin_username']; ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="profile-dropdown-content">
                            <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                            <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
                            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Content Container -->
            <div class="admin-content"> 