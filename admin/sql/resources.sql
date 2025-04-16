-- ======================================================
-- RESOURCES TABLES
-- ======================================================

-- Video Tutorials Table
CREATE TABLE IF NOT EXISTS `video_tutorials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `video_url` varchar(255) NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `status` enum('published','draft') NOT NULL DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `video_tutorials_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `guide_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Downloadable Resources Table
CREATE TABLE IF NOT EXISTS `downloadable_resources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `file_path` varchar(255) NOT NULL,
  `file_type` enum('pdf','doc','docx','xls','xlsx','ppt','pptx','zip','other') NOT NULL DEFAULT 'pdf',
  `file_size` int(11) DEFAULT NULL,
  `icon` varchar(50) DEFAULT 'fas fa-file-pdf',
  `category_id` int(11) DEFAULT NULL,
  `download_count` int(11) NOT NULL DEFAULT 0,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `status` enum('published','draft') NOT NULL DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `downloadable_resources_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `guide_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert Sample Video Tutorials
INSERT INTO `video_tutorials` (`title`, `description`, `video_url`, `thumbnail`, `duration`, `status`, `display_order`) VALUES
('CRS Score Calculator Tutorial', 'Learn how to calculate your Comprehensive Ranking System score.', 'https://www.youtube.com/embed/example1', 'images/resources/video-thumbnail1.jpg', '12:45', 'published', 1),
('Document Checklist Review', 'Detailed walkthrough of required documents for immigration.', 'https://www.youtube.com/embed/example2', 'images/resources/video-thumbnail2.jpg', '15:30', 'published', 2),
('Interview Preparation', 'Tips and strategies for immigration interviews.', 'https://www.youtube.com/embed/example3', 'images/resources/video-thumbnail3.jpg', '18:22', 'published', 3);

-- Insert Sample Downloadable Resources
INSERT INTO `downloadable_resources` (`title`, `description`, `file_path`, `file_type`, `icon`, `status`, `display_order`) VALUES
('Document Checklist', 'Comprehensive checklist of required documents for various visa applications.', 'files/resources/document-checklist.pdf', 'pdf', 'fas fa-file-pdf', 'published', 1),
('Cost Calculator', 'Excel sheet to calculate immigration costs and living expenses.', 'files/resources/cost-calculator.xlsx', 'xlsx', 'fas fa-file-excel', 'published', 2),
('Letter Templates', 'Templates for reference letters, statements of purpose, and more.', 'files/resources/letter-templates.docx', 'docx', 'fas fa-file-word', 'published', 3); 