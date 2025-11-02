-- Database updates for Kun Registration Form
-- Phase 1: Add photo type differentiation to zyt_kun_photos table

ALTER TABLE `zyt_kun_photos` 
ADD COLUMN `photo_type` ENUM('passport_info', 'passport_page', 'passport_photo', 'document') DEFAULT 'document' AFTER `photo_user_id`,
ADD INDEX `idx_photo_type` (`photo_type`);

-- Add helpful comments for future reference
-- photo_type: Differentiates between passport info page, additional pages, passport photo, and other documents
