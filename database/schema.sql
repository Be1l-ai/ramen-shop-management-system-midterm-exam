CREATE DATABASE IF NOT EXISTS `cantiga_ramen_shop_db`;

USE `cantiga_ramen_shop_db`;

-- Parent Table: Ramen Shops (created first, no FK to users yet)
CREATE TABLE `ramen_shops` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `created_by` INT NULL,
    `updated_by` INT NULL,
    `deleted_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Users Table
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'shop_owner', 'staff') NOT NULL DEFAULT 'staff',
    `shop_id` INT NULL,
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    FOREIGN KEY (`shop_id`) REFERENCES `ramen_shops`(`id`) ON DELETE SET NULL
);

-- Add FKs to ramen_shops after users table exists
ALTER TABLE `ramen_shops` 
    ADD FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    ADD FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`) ON DELETE SET NULL;

-- Child Table: Menu Items
CREATE TABLE `menu_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `shop_id` INT NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `price` DECIMAL(10,2) NOT NULL,
    `is_available` BOOLEAN DEFAULT TRUE,
    `created_by` INT NULL,
    `updated_by` INT NULL,
    `deleted_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`shop_id`) REFERENCES `ramen_shops`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
);

-- Audit Logs Table
CREATE TABLE `audit_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `action` ENUM('create', 'update', 'delete', 'restore') NOT NULL,
    `table_name` VARCHAR(50) NOT NULL,
    `record_id` INT NOT NULL,
    `old_values` JSON NULL,
    `new_values` JSON NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
);

-- Indexes for audit_logs
CREATE INDEX `idx_audit_logs_table_record` ON `audit_logs` (`table_name`, `record_id`);
CREATE INDEX `idx_audit_logs_user` ON `audit_logs` (`user_id`);
CREATE INDEX `idx_audit_logs_created` ON `audit_logs` (`created_at`);