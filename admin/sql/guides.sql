-- ======================================================
-- GUIDES TABLES
-- ======================================================

-- Guide Categories Table
CREATE TABLE IF NOT EXISTS `guide_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `icon` varchar(50) NOT NULL DEFAULT 'fas fa-book',
  `display_order` int(11) NOT NULL DEFAULT 0,
  `guide_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Guides Table
CREATE TABLE IF NOT EXISTS `guides` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text,
  `content` text NOT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `author` varchar(100) DEFAULT 'CANEXT Team',
  `status` enum('draft','published') NOT NULL DEFAULT 'draft',
  `publish_date` datetime DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `guides_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `guide_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Guide Downloads Table (for PDF downloads or attachments)
CREATE TABLE IF NOT EXISTS `guide_downloads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guide_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `download_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `guide_id` (`guide_id`),
  CONSTRAINT `guide_downloads_ibfk_1` FOREIGN KEY (`guide_id`) REFERENCES `guides` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert Sample Guide Categories
INSERT INTO `guide_categories` (`name`, `slug`, `icon`, `display_order`) VALUES
('Immigration Programs', 'immigration-programs', 'fas fa-passport', 1),
('Student Visas', 'student-visas', 'fas fa-graduation-cap', 2),
('Family Sponsorship', 'family-sponsorship', 'fas fa-users', 3);

-- Insert Sample Guides
INSERT INTO `guides` (`category_id`, `title`, `slug`, `excerpt`, `content`, `featured_image`, `status`, `publish_date`) VALUES
(1, 'Express Entry Application Guide', 'express-entry-guide', 'Complete guide to creating and submitting your Express Entry profile.', '<h2>Understanding Express Entry</h2><p>Express Entry is an online system that manages applications for permanent residence from skilled workers. This guide walks you through the process...</p>', 'images/resources/guide1.jpg', 'published', NOW()),
(2, 'Study Permit Application Guide', 'study-permit-guide', 'How to apply for a Canadian study permit successfully.', '<h2>Understanding Study Permits</h2><p>A study permit is a document issued by Immigration, Refugees and Citizenship Canada (IRCC) that allows foreign nationals to study at designated learning institutions (DLIs) in Canada...</p>', 'images/resources/guide2.jpg', 'published', NOW()),
(3, 'Family Sponsorship Process Guide', 'family-sponsorship-guide', 'Guide to sponsoring your family members to Canada.', '<h2>Understanding Family Sponsorship</h2><p>The Family Class Sponsorship Program allows Canadian citizens and permanent residents to sponsor eligible family members to come to Canada as permanent residents...</p>', 'images/resources/guide3.jpg', 'published', NOW()); 