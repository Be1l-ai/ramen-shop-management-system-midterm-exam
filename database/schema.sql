-- Ramen Shop Database Schema
-- Run this in phpMyAdmin SQL tab

CREATE DATABASE IF NOT EXISTS `cantiga_ramen_shop_db`;

USE `cantiga_ramen_shop_db`;

-- Parent Table: Ramen Shops
CREATE TABLE `ramen_shops` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Child Table: Menu Items
CREATE TABLE `menu_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `shop_id` INT NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `price` DECIMAL(10,2) NOT NULL,
    `is_available` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`shop_id`) REFERENCES `ramen_shops`(`id`) ON DELETE CASCADE
);