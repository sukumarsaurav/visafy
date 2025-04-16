<?php
// Set default page title if not set
$page_title = isset($page_title) ? $page_title : "CANEXT | Canadian Immigration Consultancy";

// Check if base_url is already set from the including file
if (!isset($base_url)) {
    // Determine base URL dynamically based on the current script's location
    $current_dir = dirname($_SERVER['PHP_SELF']);
    $base_url = '';

    // If we're in a subdirectory
    if (strpos($current_dir, '/visa-types') !== false || 
        strpos($current_dir, '/blog') !== false || 
        strpos($current_dir, '/resources') !== false ||
        strpos($current_dir, '/assessment-calculator') !== false) {
        $base_url = '..';
    } else if (strpos($current_dir, '/immigration-news') !== false) {
        $base_url = ''; // Root-relative for virtual directory
    } else {
        $base_url = '.';
    }
}

// Define base path - default to use base_url if not explicitly set
$base = isset($base_path) ? $base_path : $base_url;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'CANEXT Immigration Consultancy'; ?></title>
    <meta name="description" content="Expert Canadian immigration consultancy services for study permits, work permits, express entry, and more.">
    
    <!-- Favicon -->
    <link rel="icon" href="<?php echo $base; ?>/favicon.ico" type="image/x-icon">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Lora:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    
    <!-- Swiper CSS for Sliders -->
    <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css">
    
    <!-- AOS Animation CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <!-- Move JS libraries to the end of head to ensure they load before other scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $base; ?>/css/styles.css">
    <link rel="stylesheet" href="<?php echo $base; ?>/css/animations.css">
    <link rel="stylesheet" href="<?php echo $base; ?>/css/header.css">
    <link rel="stylesheet" href="<?php echo $base; ?>/css/resources.css">
    <link rel="stylesheet" href="<?php echo $base; ?>/css/assessment-drawer.css">
    <link rel="stylesheet" href="<?php echo $base; ?>/css/news.css">
    <link rel="stylesheet" href="<?php echo $base; ?>/css/faq.css">
        
    <!-- Libraries -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

    <!-- Load utils.js before other scripts -->
    <script src="<?php echo $base; ?>/js/utils.js"></script>

    <!-- Your custom scripts should come after utils.js -->
    <script src="<?php echo $base; ?>/js/main.js" defer></script>
    <script src="<?php echo $base; ?>/js/resources.js" defer></script>
</head>
<body>
    <!-- Drawer Overlay -->
    <div class="drawer-overlay"></div>
    
    <!-- Side Drawer -->
    <div class="side-drawer">
        <div class="drawer-header">
            <a href="<?php echo $base; ?>/index.php" class="drawer-logo">CANEXT</a>
            <button class="drawer-close"><i class="fas fa-times"></i></button>
        </div>
        <nav class="drawer-nav">
            <div class="drawer-item" data-target="visa-submenu">
                Visa Services <i class="fas fa-chevron-down"></i>
            </div>
            <div class="drawer-submenu" id="visa-submenu">
                <a href="<?php echo $base; ?>/visa-types/Study-Permit.php" class="drawer-submenu-item">
                    <div class="drawer-submenu-title">Study Permit</div>
                    <div class="drawer-submenu-description">Information for international students looking to study in Canada</div>
                </a>
                <a href="<?php echo $base; ?>/visa-types/Work-Permit.php" class="drawer-submenu-item">
                    <div class="drawer-submenu-title">Work Permit</div>
                    <div class="drawer-submenu-description">Guidance for those seeking employment opportunities in Canada</div>
                </a>
                <a href="<?php echo $base; ?>/visa-types/Express-Entry-visa.php" class="drawer-submenu-item">
                    <div class="drawer-submenu-title">Express Entry</div>
                    <div class="drawer-submenu-description">Fast-track immigration for skilled workers and professionals</div>
                </a>
                <a href="<?php echo $base; ?>/visa-types/Family-Sponsorship.php" class="drawer-submenu-item">
                    <div class="drawer-submenu-title">Family Sponsorship</div>
                    <div class="drawer-submenu-description">Reunite with your family members in Canada</div>
                </a>
                <a href="<?php echo $base; ?>/visa-types/Provincial-Nominee.php" class="drawer-submenu-item">
                    <div class="drawer-submenu-title">Provincial Nominee</div>
                    <div class="drawer-submenu-description">Immigration programs tailored to provincial needs</div>
                </a>
                <a href="<?php echo $base; ?>/visa-types/faq.php" class="drawer-submenu-item">
                    <div class="drawer-submenu-title">Visitor Visa</div>
                    <div class="drawer-submenu-description">Visit Canada for tourism, business, or family visits</div>
                </a>
            </div>
            
            <div class="drawer-item" data-target="assessment-submenu">
                Assessment Tools <i class="fas fa-chevron-down"></i>
            </div>
            <div class="drawer-submenu" id="assessment-submenu">
                <a href="<?php echo $base; ?>/assessment-calculator/eligibility-calculator.php" class="drawer-submenu-item">
                    <div class="drawer-submenu-title">Eligibility Calculator</div>
                    <div class="drawer-submenu-description">Check if you qualify for Canadian immigration programs</div>
                </a>
                <a href="<?php echo $base; ?>/assessment-calculator/crs-score-calculator.php" class="drawer-submenu-item">
                    <div class="drawer-submenu-title">CRS Calculator</div>
                    <div class="drawer-submenu-description">Calculate your Comprehensive Ranking System score</div>
                </a>
                <a href="<?php echo $base; ?>/assessment-calculator/study-permit-checker.php" class="drawer-submenu-item">
                    <div class="drawer-submenu-title">Study Permit Checker</div>
                    <div class="drawer-submenu-description">Verify your eligibility for a Canadian study permit</div>
                </a>
                <a href="<?php echo $base; ?>/assessment-calculator/immigration-pathway-calculator.php" class="drawer-submenu-item">
                    <div class="drawer-submenu-title">Immigration Pathway Calculator</div>
                    <div class="drawer-submenu-description">Find your ideal pathway to Canada based on your circumstances</div>
                </a>
            </div>
            
            <div class="drawer-item" data-target="resources-submenu">
                Resources <i class="fas fa-chevron-down"></i>
            </div>
            <div class="drawer-submenu" id="resources-submenu">
                <a href="<?php echo $base; ?>/resources/immigration-news.php" class="drawer-submenu-item">
                    <div class="drawer-submenu-title">Immigration News</div>
                    <div class="drawer-submenu-description">Latest updates on Canadian immigration policies</div>
                </a>
                <a href="<?php echo $base; ?>/resources/guides-tutorials.php" class="drawer-submenu-item">
                    <div class="drawer-submenu-title">Guides & Tutorials</div>
                    <div class="drawer-submenu-description">Step-by-step instructions for immigration processes</div>
                </a>
                <a href="<?php echo $base; ?>/resources/faq.php" class="drawer-submenu-item">
                    <div class="drawer-submenu-title">FAQ</div>
                    <div class="drawer-submenu-description">Answers to commonly asked immigration questions</div>
                </a>
                <a href="<?php echo $base; ?>/resources/blog.php" class="drawer-submenu-item">
                    <div class="drawer-submenu-title">Blog</div>
                    <div class="drawer-submenu-description">Articles and insights on Canadian immigration</div>
                </a>
            </div>
            
            <a href="<?php echo $base; ?>/contact.php" class="drawer-item">Contact</a>
            
            <div class="drawer-cta">
                <a href="<?php echo $base; ?>/contact.php" class="btn btn-primary">Book Consultation</a>
            </div>
        </nav>
    </div>

    <!-- Header Section -->
    <header class="header">
        <div class="container header-container">
        <button class="mobile-menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
            <div class="logo">
                <a href="<?php echo $base; ?>/index.php">
                    <!-- <img src="<?php echo $base_url; ?>/images/logo.png" alt="CANEXT Immigration Consultancy Logo"> -->
                    <span class="logo-text">CANEXT</span>
                </a>
            </div>
            
            <nav>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="<?php echo $base; ?>/visas.php">Visa Services <i class="fas fa-chevron-down"></i></a>
                        <div class="dropdown-menu mega-menu">
                            <a href="<?php echo $base; ?>/visa-types/Study-Permit.php" class="mega-menu-item">
                                <h4 class="mega-menu-title">Study Permit</h4>
                                <p class="mega-menu-description">Information for international students looking to study in Canada</p>
                            </a>
                            <a href="<?php echo $base; ?>/visa-types/Work-Permit.php" class="mega-menu-item">
                                <h4 class="mega-menu-title">Work Permit</h4>
                                <p class="mega-menu-description">Guidance for those seeking employment opportunities in Canada</p>
                            </a>
                            <a href="<?php echo $base; ?>/visa-types/Express-Entry-visa.php" class="mega-menu-item">
                                <h4 class="mega-menu-title">Express Entry</h4>
                                <p class="mega-menu-description">Fast-track immigration for skilled workers and professionals</p>
                            </a>
                            <a href="<?php echo $base; ?>/visa-types/Family-Sponsorship.php" class="mega-menu-item">
                                <h4 class="mega-menu-title">Family Sponsorship</h4>
                                <p class="mega-menu-description">Reunite with your family members in Canada</p>
                            </a>
                            <a href="<?php echo $base; ?>/visa-types/Provincial-Nominee.php" class="mega-menu-item">
                                <h4 class="mega-menu-title">Provincial Nominee</h4>
                                <p class="mega-menu-description">Immigration programs tailored to provincial needs</p>
                            </a>
                            <a href="<?php echo $base; ?>/visa-types/Visitor-Visa.php" class="mega-menu-item">
                                <h4 class="mega-menu-title">Visitor Visa</h4>
                                <p class="mega-menu-description">Visit Canada for tourism, business, or family visits</p>
                            </a>
                            <div class="mega-menu-consultation">
                                <h4>Need Immigration Assistance?</h4>
                                <p>Our experts are ready to help with your visa application process.</p>
                                <a href="<?php echo $base; ?>/Book-Consultation.php" class="btn btn-primary">Book a Consultation</a>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo $base; ?>/assessment-tools.php">Assessment Tools <i class="fas fa-chevron-down"></i></a>
                        <div class="dropdown-menu">
                            <a href="<?php echo $base; ?>/assessment-calculator/eligibility-calculator.php" class="dropdown-item">
                                <div class="mega-menu-title">Eligibility Calculator</div>
                                <div class="mega-menu-description">Check if you qualify for Canadian immigration programs</div>
                            </a>
                            <a href="<?php echo $base; ?>/assessment-calculator/crs-score-calculator.php" class="dropdown-item">
                                <div class="mega-menu-title">CRS Score Calculator</div>
                                <div class="mega-menu-description">Calculate your Comprehensive Ranking System score for Express Entry</div>
                            </a>
                            <a href="<?php echo $base; ?>/assessment-calculator/study-permit-checker.php" class="dropdown-item">
                                <div class="mega-menu-title">Study Permit Checker</div>
                                <div class="mega-menu-description">Verify your eligibility for a Canadian study permit</div>
                            </a>
                            <a href="<?php echo $base; ?>/assessment-calculator/immigration-pathway-calculator.php" class="dropdown-item">
                                <div class="mega-menu-title">Immigration Pathway Calculator</div>
                                <div class="mega-menu-description">Find your ideal pathway to Canada based on your circumstances</div>
                            </a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo $base; ?>/resources.php">Resources <i class="fas fa-chevron-down"></i></a>
                        <div class="dropdown-menu">
                            <a href="<?php echo $base; ?>/resources/immigration-news.php" class="dropdown-item">
                                <div class="mega-menu-title">Immigration News</div>
                                <div class="mega-menu-description">Latest updates on Canadian immigration policies and programs</div>
                            </a>
                            <a href="<?php echo $base; ?>/resources/guides-tutorials.php" class="dropdown-item">
                                <div class="mega-menu-title">Guides & Tutorials</div>
                                <div class="mega-menu-description">Step-by-step guides to help you through the immigration process</div>
                            </a>
                            <a href="<?php echo $base; ?>/resources/faq.php" class="dropdown-item">
                                <div class="mega-menu-title">FAQ</div>
                                <div class="mega-menu-description">Answers to commonly asked questions about Canadian immigration</div>
                            </a>
                            <a href="<?php echo $base; ?>/resources/blog.php" class="dropdown-item">
                                <div class="mega-menu-title">Blog</div>
                                <div class="mega-menu-description">Articles about life in Canada and immigration experiences</div>
                            </a>
                        </div>
                    </li>
                    <li class="nav-item"><a href="<?php echo $base; ?>/contact.php">Contact</a></li>
                </ul>
            </nav>
            
            <div class="header-actions book-button">
                <a href="<?php echo $base; ?>/consultant.php" class="btn btn-primary">Book Consultation</a>
               
            </div>
        </div>
    </header>

</body>
</html> 