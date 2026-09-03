SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `colleges` (
  `code` VARCHAR(32) NOT NULL,
  `name` VARCHAR(512) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `staff` (
  `sno` INT NOT NULL,
  `college_code` VARCHAR(32) NOT NULL,
  `name` VARCHAR(256) NOT NULL,
  `designation` VARCHAR(256) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`sno`),
  KEY `idx_college` (`college_code`),
  CONSTRAINT `fk_staff_college` FOREIGN KEY (`college_code`) REFERENCES `colleges` (`code`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `verifications` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `staff_sno` INT NOT NULL,
  `college_code` VARCHAR(32) NOT NULL,
  `status` ENUM('yes', 'no') NOT NULL,
  `verifier_user_id` INT DEFAULT NULL,
  `verifier_phone` VARCHAR(32) DEFAULT NULL,
  `verifier_email` VARCHAR(256) DEFAULT NULL,
  `verified_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_staff_sno` (`staff_sno`),
  KEY `idx_college_code` (`college_code`),
  KEY `idx_status` (`status`),
  KEY `idx_verifier_user` (`verifier_user_id`),
  CONSTRAINT `fk_ver_staff` FOREIGN KEY (`staff_sno`) REFERENCES `staff` (`sno`) ON DELETE CASCADE,
  CONSTRAINT `fk_ver_college` FOREIGN KEY (`college_code`) REFERENCES `colleges` (`code`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
