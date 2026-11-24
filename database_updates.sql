-- Database updates for Kun Registration Form
-- Phase 1: Add photo type differentiation to zyt_kun_photos table

ALTER TABLE `zyt_kun_photos` 
ADD COLUMN `photo_type` ENUM('passport_info', 'passport_page', 'passport_photo', 'document') DEFAULT 'document' AFTER `photo_user_id`,
ADD INDEX `idx_photo_type` (`photo_type`);

-- Add helpful comments for future reference
-- photo_type: Differentiates between passport info page, additional pages, passport photo, and other documents

-- Phase 2: Make kun_registrations fields nullable for pre-registration flow
-- Allows minimal pre-registration (ITS, contact, email) with full details filled later

ALTER TABLE `zyt_kun_registrations` 
MODIFY COLUMN `kun_fname` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_lname` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_mname` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_ppno` VARCHAR(100) NULL DEFAULT NULL,
MODIFY COLUMN `kun_nationality` VARCHAR(100) NULL DEFAULT NULL,
MODIFY COLUMN `kun_dob` DATE NULL DEFAULT NULL,
MODIFY COLUMN `kun_gender` VARCHAR(10) NULL DEFAULT NULL,
MODIFY COLUMN `kun_birthplace` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_ppissue` DATE NULL DEFAULT NULL,
MODIFY COLUMN `kun_ppexpiry` DATE NULL DEFAULT NULL,
MODIFY COLUMN `kun_placeofissue` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_profession` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_passport_arabic_name` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_father_name` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_grandfather_name` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_mother_name` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_mother_father_name` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_father_name_arabic` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_grandfather_name_arabic` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_mother_name_arabic` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_mother_father_name_arabic` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_fname_arabic` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_mname_arabic` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_lname_arabic` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_passport_type` VARCHAR(50) NULL DEFAULT NULL,
MODIFY COLUMN `kun_age` INT NULL DEFAULT NULL,
MODIFY COLUMN `kun_category` VARCHAR(50) NULL DEFAULT NULL,
MODIFY COLUMN `kun_countryofissue` VARCHAR(100) NULL DEFAULT NULL,
MODIFY COLUMN `kun_orignationality` VARCHAR(100) NULL DEFAULT NULL,
MODIFY COLUMN `kun_birthcountry` VARCHAR(100) NULL DEFAULT NULL,
MODIFY COLUMN `kun_curroccupation` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_address` TEXT NULL DEFAULT NULL,
MODIFY COLUMN `kun_langspoken` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_spname` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_spoccupation` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_spnationality` VARCHAR(100) NULL DEFAULT NULL,
MODIFY COLUMN `kun_sppob` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_accomppersons` INT NULL DEFAULT NULL,
MODIFY COLUMN `kun_certs` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_issuecerts` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_numpreventry` INT NULL DEFAULT NULL,
MODIFY COLUMN `kun_child1name` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_child1occ` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_child2name` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_child2occ` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_child3name` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_child3occ` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_visit1country` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_visit1reason` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_visit2country` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_visit2reason` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_visit3country` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_visit3reason` VARCHAR(255) NULL DEFAULT NULL,
MODIFY COLUMN `kun_group` VARCHAR(50) NULL DEFAULT NULL,
MODIFY COLUMN `kun_declaration` TINYINT NULL DEFAULT 0,
MODIFY COLUMN `kun_lastprintdate` DATETIME NULL DEFAULT NULL,
MODIFY COLUMN `kun_printcount` INT NULL DEFAULT 0,
MODIFY COLUMN `kun_mowafeka` TINYINT NULL DEFAULT 0;

-- Phase 3: Defaults for columns required by minimal registration inserts
-- (e.g. m.colomboshabab.com/register_ziyarat_international.php only sets
-- safar_id, kun_its, kun_itsname, kun_email, kun_contact). Without these
-- defaults MariaDB strict mode raises: Field 'X' doesn't have a default value.
-- Remaining REQUIRED columns already have defaults (declaration/printcount/
-- priority/manifest/mowafeka) or are always supplied by that insert.

ALTER TABLE `zyt_kun_registrations`
MODIFY COLUMN `kun_lastupdate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
MODIFY COLUMN `kun_lastupdatememid` INT(8) NOT NULL DEFAULT 0,
MODIFY COLUMN `kun_status` ENUM(
  'registered',
  'raza',
  'approved',
  'submitted for visa',
  'visa passed',
  'visa & ticket issued',
  'cancelled'
) NOT NULL DEFAULT 'registered';

