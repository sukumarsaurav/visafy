-- Add profile_image column to professionals table
ALTER TABLE professionals ADD COLUMN profile_image VARCHAR(255) DEFAULT NULL AFTER user_id; 