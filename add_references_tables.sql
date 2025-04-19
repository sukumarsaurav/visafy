-- Create languages reference table
CREATE TABLE IF NOT EXISTS `languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `code` varchar(10) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert common languages
INSERT INTO `languages` (`name`, `code`) VALUES
('English', 'en'),
('French', 'fr'),
('Spanish', 'es'),
('Mandarin', 'zh'),
('Hindi', 'hi'),
('Arabic', 'ar'),
('Portuguese', 'pt'),
('Bengali', 'bn'),
('Russian', 'ru'),
('Japanese', 'ja'),
('Punjabi', 'pa'),
('German', 'de'),
('Korean', 'ko'),
('Turkish', 'tr'),
('Tamil', 'ta'),
('Italian', 'it'),
('Urdu', 'ur');

-- Create specializations reference table
CREATE TABLE IF NOT EXISTS `specializations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert common immigration specializations
INSERT INTO `specializations` (`name`, `description`) VALUES
('Express Entry', 'Federal Skilled Worker, Federal Skilled Trades, and Canadian Experience Class programs'),
('Family Sponsorship', 'Sponsor a spouse, partner, child, or other family member'),
('Student Visa', 'Study permit for international students'),
('Work Permit', 'Temporary work authorization in Canada'),
('Business Immigration', 'Start-up Visa and Self-employed programs'),
('Provincial Nominee', 'Immigration through provincial nomination'),
('Skilled Worker', 'Immigration programs for skilled workers'),
('Startup Visa', 'Immigration program for entrepreneurs'),
('Refugee Claims', 'Assistance with refugee protection claims'),
('Citizenship Applications', 'Support for citizenship and naturalization'),
('Humanitarian Cases', 'Applications based on humanitarian and compassionate grounds'),
('Appeals & Tribunals', 'Representation at immigration appeals and tribunals'),
('LMIA Applications', 'Labour Market Impact Assessment applications');

-- Create a junction table for professionals and languages
CREATE TABLE IF NOT EXISTS `professional_languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `professional_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `proficiency_level` enum('basic','intermediate','fluent','native') DEFAULT 'intermediate',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `professional_language` (`professional_id`,`language_id`),
  KEY `language_id` (`language_id`),
  CONSTRAINT `professional_languages_professional_fk` FOREIGN KEY (`professional_id`) REFERENCES `professionals` (`id`) ON DELETE CASCADE,
  CONSTRAINT `professional_languages_language_fk` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create a junction table for professionals and specializations
CREATE TABLE IF NOT EXISTS `professional_specializations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `professional_id` int(11) NOT NULL,
  `specialization_id` int(11) NOT NULL,
  `years_experience` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `professional_specialization` (`professional_id`,`specialization_id`),
  KEY `specialization_id` (`specialization_id`),
  CONSTRAINT `professional_specializations_professional_fk` FOREIGN KEY (`professional_id`) REFERENCES `professionals` (`id`) ON DELETE CASCADE,
  CONSTRAINT `professional_specializations_specialization_fk` FOREIGN KEY (`specialization_id`) REFERENCES `specializations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci; 