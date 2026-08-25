-- ============================================================
-- Kamadenu Goushala – Database Schema
-- MySQL 5.7+ / MariaDB 10.3+
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Create database
CREATE DATABASE IF NOT EXISTS `kamadhenu_goushala` 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

USE `kamadhenu_goushala`;

-- ============================================================
-- 1. ADMINS
-- ============================================================
DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `avatar` VARCHAR(255) DEFAULT NULL,
    `last_login` DATETIME DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_admins_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 2. SETTINGS
-- ============================================================
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` TEXT DEFAULT NULL,
    `setting_group` VARCHAR(50) NOT NULL DEFAULT 'general',
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_settings_group` (`setting_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 3. BREEDS
-- ============================================================
DROP TABLE IF EXISTS `breeds`;
CREATE TABLE `breeds` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(120) NOT NULL UNIQUE,
    `origin` VARCHAR(150) DEFAULT NULL,
    `description` TEXT DEFAULT NULL,
    `milk_quality` TEXT DEFAULT NULL,
    `characteristics` TEXT DEFAULT NULL,
    `image` VARCHAR(255) DEFAULT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_breeds_active` (`is_active`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 4. COWS
-- ============================================================
DROP TABLE IF EXISTS `cows`;
CREATE TABLE `cows` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `breed_id` INT UNSIGNED DEFAULT NULL,
    `gender` ENUM('Female', 'Male', 'Calf') NOT NULL DEFAULT 'Female',
    `date_of_birth` DATE DEFAULT NULL,
    `description` TEXT DEFAULT NULL,
    `rescue_story` TEXT DEFAULT NULL,
    `health_status` VARCHAR(100) DEFAULT 'Healthy',
    `photo` VARCHAR(255) DEFAULT NULL,
    `is_adoptable` TINYINT(1) NOT NULL DEFAULT 1,
    `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
    `status` ENUM('Available', 'Adopted', 'Permanent Resident', 'Medical Care', 'Rescued') NOT NULL DEFAULT 'Available',
    `monthly_adoption_amount` DECIMAL(10,2) NOT NULL DEFAULT 2500.00,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_cows_breed` (`breed_id`),
    INDEX `idx_cows_status` (`status`),
    INDEX `idx_cows_featured` (`is_featured`),
    INDEX `idx_cows_adoptable` (`is_adoptable`),
    CONSTRAINT `fk_cows_breed` FOREIGN KEY (`breed_id`) REFERENCES `breeds`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 5. COW GALLERY
-- ============================================================
DROP TABLE IF EXISTS `cow_gallery`;
CREATE TABLE `cow_gallery` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `cow_id` INT UNSIGNED NOT NULL,
    `image_path` VARCHAR(255) NOT NULL,
    `caption` VARCHAR(255) DEFAULT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_cow_gallery_cow` (`cow_id`),
    CONSTRAINT `fk_cow_gallery_cow` FOREIGN KEY (`cow_id`) REFERENCES `cows`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 6. SEVA CATEGORIES
-- ============================================================
DROP TABLE IF EXISTS `seva_categories`;
CREATE TABLE `seva_categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(150) NOT NULL,
    `slug` VARCHAR(170) NOT NULL UNIQUE,
    `icon` VARCHAR(100) DEFAULT NULL,
    `image` VARCHAR(255) DEFAULT NULL,
    `short_description` VARCHAR(500) DEFAULT NULL,
    `description` TEXT DEFAULT NULL,
    `benefits` TEXT DEFAULT NULL,
    `suggested_amounts` VARCHAR(255) DEFAULT '101,501,1001,2501,5001',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_seva_active` (`is_active`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 7. DONATIONS
-- ============================================================
DROP TABLE IF EXISTS `donations`;
CREATE TABLE `donations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `reference_number` VARCHAR(30) NOT NULL UNIQUE,
    `donor_name` VARCHAR(100) NOT NULL,
    `donor_phone` VARCHAR(20) NOT NULL,
    `donor_email` VARCHAR(100) DEFAULT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `seva_category_id` INT UNSIGNED DEFAULT NULL,
    `purpose` VARCHAR(255) DEFAULT NULL,
    `message` TEXT DEFAULT NULL,
    `payment_method` VARCHAR(50) DEFAULT 'UPI',
    `transaction_id` VARCHAR(100) DEFAULT NULL,
    `payment_status` ENUM('Pending', 'Success', 'Failed', 'Refunded') NOT NULL DEFAULT 'Pending',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_donations_status` (`payment_status`),
    INDEX `idx_donations_seva` (`seva_category_id`),
    INDEX `idx_donations_date` (`created_at`),
    CONSTRAINT `fk_donations_seva` FOREIGN KEY (`seva_category_id`) REFERENCES `seva_categories`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 8. ADOPTIONS
-- ============================================================
DROP TABLE IF EXISTS `adoptions`;
CREATE TABLE `adoptions` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `adoption_id` VARCHAR(30) NOT NULL UNIQUE,
    `cow_id` INT UNSIGNED NOT NULL,
    `adopter_name` VARCHAR(100) NOT NULL,
    `adopter_phone` VARCHAR(20) NOT NULL,
    `adopter_email` VARCHAR(100) DEFAULT NULL,
    `adopter_address` TEXT DEFAULT NULL,
    `duration_months` INT NOT NULL DEFAULT 1,
    `monthly_amount` DECIMAL(10,2) NOT NULL,
    `total_amount` DECIMAL(10,2) NOT NULL,
    `start_date` DATE DEFAULT NULL,
    `end_date` DATE DEFAULT NULL,
    `payment_status` ENUM('Pending', 'Success', 'Failed', 'Refunded') NOT NULL DEFAULT 'Pending',
    `transaction_id` VARCHAR(100) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_adoptions_cow` (`cow_id`),
    INDEX `idx_adoptions_status` (`payment_status`),
    INDEX `idx_adoptions_date` (`created_at`),
    CONSTRAINT `fk_adoptions_cow` FOREIGN KEY (`cow_id`) REFERENCES `cows`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 9. EVENTS
-- ============================================================
DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(200) NOT NULL,
    `slug` VARCHAR(220) NOT NULL UNIQUE,
    `event_date` DATE NOT NULL,
    `event_time` TIME DEFAULT NULL,
    `location` VARCHAR(255) DEFAULT NULL,
    `short_description` VARCHAR(500) DEFAULT NULL,
    `description` TEXT DEFAULT NULL,
    `image` VARCHAR(255) DEFAULT NULL,
    `registration_url` VARCHAR(500) DEFAULT NULL,
    `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
    `status` ENUM('Upcoming', 'Ongoing', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Upcoming',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_events_date` (`event_date`),
    INDEX `idx_events_status` (`status`),
    INDEX `idx_events_featured` (`is_featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 10. NEWS CATEGORIES
-- ============================================================
DROP TABLE IF EXISTS `news_categories`;
CREATE TABLE `news_categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(120) NOT NULL UNIQUE,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 11. NEWS
-- ============================================================
DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(250) NOT NULL,
    `slug` VARCHAR(270) NOT NULL UNIQUE,
    `short_description` VARCHAR(500) DEFAULT NULL,
    `content` TEXT DEFAULT NULL,
    `featured_image` VARCHAR(255) DEFAULT NULL,
    `author` VARCHAR(100) DEFAULT 'Kamadenu Goushala',
    `category_id` INT UNSIGNED DEFAULT NULL,
    `tags` VARCHAR(500) DEFAULT NULL,
    `published_date` DATE DEFAULT NULL,
    `status` ENUM('Draft', 'Published', 'Archived') NOT NULL DEFAULT 'Draft',
    `seo_title` VARCHAR(200) DEFAULT NULL,
    `seo_description` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_news_status` (`status`),
    INDEX `idx_news_date` (`published_date`),
    INDEX `idx_news_category` (`category_id`),
    CONSTRAINT `fk_news_category` FOREIGN KEY (`category_id`) REFERENCES `news_categories`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 12. GALLERY CATEGORIES
-- ============================================================
DROP TABLE IF EXISTS `gallery_categories`;
CREATE TABLE `gallery_categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(120) NOT NULL UNIQUE,
    `sort_order` INT NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 13. GALLERY
-- ============================================================
DROP TABLE IF EXISTS `gallery`;
CREATE TABLE `gallery` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT UNSIGNED DEFAULT NULL,
    `image_path` VARCHAR(255) NOT NULL,
    `caption` VARCHAR(255) DEFAULT NULL,
    `alt_text` VARCHAR(255) DEFAULT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_gallery_category` (`category_id`),
    INDEX `idx_gallery_active` (`is_active`, `sort_order`),
    CONSTRAINT `fk_gallery_category` FOREIGN KEY (`category_id`) REFERENCES `gallery_categories`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 14. PRODUCT CATEGORIES
-- ============================================================
DROP TABLE IF EXISTS `product_categories`;
CREATE TABLE `product_categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(120) NOT NULL UNIQUE,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 15. PRODUCTS
-- ============================================================
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(200) NOT NULL,
    `slug` VARCHAR(220) NOT NULL UNIQUE,
    `description` TEXT DEFAULT NULL,
    `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `image` VARCHAR(255) DEFAULT NULL,
    `whatsapp_number` VARCHAR(30) DEFAULT NULL,
    `whatsapp_message` TEXT DEFAULT NULL,
    `category_id` INT UNSIGNED DEFAULT NULL,
    `stock_status` ENUM('In Stock', 'Out of Stock', 'Pre-Order') NOT NULL DEFAULT 'In Stock',
    `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_products_category` (`category_id`),
    INDEX `idx_products_featured` (`is_featured`),
    INDEX `idx_products_active` (`is_active`),
    CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `product_categories`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 16. TESTIMONIALS
-- ============================================================
DROP TABLE IF EXISTS `testimonials`;
CREATE TABLE `testimonials` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `location` VARCHAR(150) DEFAULT NULL,
    `photo` VARCHAR(255) DEFAULT NULL,
    `message` TEXT NOT NULL,
    `rating` INT NOT NULL DEFAULT 5,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_testimonials_active` (`is_active`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 17. CONTACT MESSAGES
-- ============================================================
DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE `contact_messages` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(20) DEFAULT NULL,
    `subject` VARCHAR(200) NOT NULL,
    `message` TEXT NOT NULL,
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_messages_read` (`is_read`),
    INDEX `idx_messages_date` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
