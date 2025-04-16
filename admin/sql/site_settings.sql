-- Create site_settings table if not exists
CREATE TABLE IF NOT EXISTS `site_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_name` varchar(100) NOT NULL,
  `setting_value` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_name` (`setting_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default values if not exists
INSERT INTO `site_settings` (`setting_name`, `setting_value`) VALUES
('site_name', 'CANEXT Immigration Consultancy'),
('site_email', 'info@canext.com'),
('site_phone', '+1 (647) 226-7436'),
('site_address', '2233 Argentina Rd, Mississauga ON L5N 2X7, Canada'),
('business_hours', 'Mon-Fri: 9am-5pm'),
('timezone', 'America/Toronto')
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- Ensure admin_users table has the necessary fields
ALTER TABLE `admin_users` 
ADD COLUMN IF NOT EXISTS `first_name` varchar(100) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `last_name` varchar(100) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `email` varchar(100) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `phone` varchar(50) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `role` varchar(50) DEFAULT 'admin',
ADD COLUMN IF NOT EXISTS `status` varchar(20) DEFAULT 'active',
ADD COLUMN IF NOT EXISTS `last_login` timestamp NULL DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN IF NOT EXISTS `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP; 