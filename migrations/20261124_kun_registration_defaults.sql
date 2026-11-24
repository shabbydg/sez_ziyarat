-- Surgical migration: defaults for minimal registration INSERTs
-- Safe to re-run on MariaDB 11.x (MODIFY is idempotent for same definition).

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
