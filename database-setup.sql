-- Create professional_clients table
CREATE TABLE `professional_clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `professional_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `status` enum('pending','active','completed','archived','rejected') NOT NULL DEFAULT 'pending',
  `initial_message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `professional_id` (`professional_id`),
  KEY `client_id` (`client_id`),
  CONSTRAINT `professional_clients_professional_fk` FOREIGN KEY (`professional_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `professional_clients_client_fk` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create visa_types table
CREATE TABLE `visa_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert some default visa types
INSERT INTO `visa_types` (`name`, `description`) VALUES
('Express Entry', 'Federal Skilled Worker, Federal Skilled Trades, and Canadian Experience Class programs'),
('Family Sponsorship', 'Sponsor a spouse, partner, child, or other family member'),
('Student Visa', 'Study permit for international students'),
('Work Permit', 'Temporary work authorization in Canada'),
('Business Immigration', 'Start-up Visa and Self-employed programs'),
('Provincial Nominee', 'Immigration through provincial nomination');

-- Create case_applications table
CREATE TABLE `case_applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `professional_id` int(11) DEFAULT NULL,
  `visa_type_id` int(11) NOT NULL,
  `reference_number` varchar(50) NOT NULL,
  `status` enum('new','in_progress','pending_documents','review','approved','rejected') NOT NULL DEFAULT 'new',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference_number` (`reference_number`),
  KEY `client_id` (`client_id`),
  KEY `professional_id` (`professional_id`),
  KEY `visa_type_id` (`visa_type_id`),
  CONSTRAINT `case_applications_client_fk` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `case_applications_professional_fk` FOREIGN KEY (`professional_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `case_applications_visa_type_fk` FOREIGN KEY (`visa_type_id`) REFERENCES `visa_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create document_types table
CREATE TABLE `document_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert some default document types
INSERT INTO `document_types` (`name`, `description`) VALUES
('Passport', 'Valid passport or travel document'),
('Education Credential', 'Educational certificates and transcripts'),
('Employment Record', 'Employment letters, contracts, and pay stubs'),
('Language Test', 'Language proficiency test results'),
('Financial Document', 'Bank statements and proof of funds'),
('Identity Document', 'Birth certificate, national ID card'),
('Medical Examination', 'Medical examination results');

-- Create documents table
CREATE TABLE `documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_id` int(11) NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `professional_id` int(11) DEFAULT NULL,
  `document_type_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `case_id` (`case_id`),
  KEY `client_id` (`client_id`),
  KEY `professional_id` (`professional_id`),
  KEY `document_type_id` (`document_type_id`),
  CONSTRAINT `documents_case_fk` FOREIGN KEY (`case_id`) REFERENCES `case_applications` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documents_client_fk` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `documents_professional_fk` FOREIGN KEY (`professional_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `documents_document_type_fk` FOREIGN KEY (`document_type_id`) REFERENCES `document_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create case_notes table
CREATE TABLE `case_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_type` enum('client','professional','admin') NOT NULL,
  `content` text NOT NULL,
  `is_private` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `case_id` (`case_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `case_notes_case_fk` FOREIGN KEY (`case_id`) REFERENCES `case_applications` (`id`) ON DELETE CASCADE,
  CONSTRAINT `case_notes_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create notifications table
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `notifications_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci; 