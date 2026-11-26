-- Surgical migration: defaults for minimal foreign_census inserts
-- Used by m.colomboshabab.com/register_ziyarat_international.php which only
-- supplies ITS_ID, HOF_ID, name, email, mobile.
-- Defaults match existing stub rows already in production (0 / '' / Male).
-- Safe to re-run on MariaDB 11.x.

ALTER TABLE `foreign_census`
MODIFY COLUMN `father_its` INT(8) NOT NULL DEFAULT 0,
MODIFY COLUMN `mother_its` INT(8) NOT NULL DEFAULT 0,
MODIFY COLUMN `spouse_its` INT(8) NOT NULL DEFAULT 0,
MODIFY COLUMN `sabeel` VARCHAR(10) NOT NULL DEFAULT '',
MODIFY COLUMN `arabic_name` INT(255) NOT NULL DEFAULT 0,
MODIFY COLUMN `age` INT(3) NOT NULL DEFAULT 0,
MODIFY COLUMN `gender` ENUM('Male','Female') NOT NULL DEFAULT 'Male',
MODIFY COLUMN `misaq` VARCHAR(10) NOT NULL DEFAULT '',
MODIFY COLUMN `marital_status` VARCHAR(15) NOT NULL DEFAULT '',
MODIFY COLUMN `blood_group` VARCHAR(8) NOT NULL DEFAULT '',
MODIFY COLUMN `whatsapp` VARCHAR(20) NOT NULL DEFAULT '',
MODIFY COLUMN `address` TEXT NOT NULL DEFAULT '',
MODIFY COLUMN `city` VARCHAR(100) NOT NULL DEFAULT '',
MODIFY COLUMN `pincode` VARCHAR(10) NOT NULL DEFAULT '',
MODIFY COLUMN `mohalla` VARCHAR(10) NOT NULL DEFAULT '',
MODIFY COLUMN `area` VARCHAR(100) NOT NULL DEFAULT '';
