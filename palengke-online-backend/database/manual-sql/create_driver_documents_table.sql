-- Manual SQL for creating the driver_documents table
-- Run this if artisan migrate is not available

CREATE TABLE IF NOT EXISTS `driver_documents` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `driver_email` VARCHAR(255) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `type` VARCHAR(255) NOT NULL,
  `file_path` TEXT NULL,
  `expiry_date` VARCHAR(255) NULL,
  `status` VARCHAR(255) NOT NULL DEFAULT 'missing',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  UNIQUE KEY `driver_documents_email_type_unique` (`driver_email`, `type`),
  CONSTRAINT `driver_documents_driver_email_foreign` 
    FOREIGN KEY (`driver_email`) REFERENCES `driver_registrations` (`email`) 
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
