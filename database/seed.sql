-- Ramen Shop Seed Data
-- Run this AFTER schema.sql in phpMyAdmin

USE `cantiga_ramen_shop_db`;

-- Insert sample ramen shop
INSERT INTO `ramen_shops` (`name`, `description`) VALUES
('Ichiraku Ramen', 'Authentic Japanese ramen since 2020. Specializing in tonkotsu and shoyu broths made fresh daily.');

-- Get the shop ID (should be 1)
SET @shop_id = LAST_INSERT_ID();

-- Insert sample menu items
INSERT INTO `menu_items` (`shop_id`, `name`, `description`, `price`, `is_available`) VALUES
(@shop_id, 'Tonkotsu Ramen', 'Rich pork bone broth, chashu, soft-boiled egg, green onions, nori', 180.00, TRUE),
(@shop_id, 'Shoyu Ramen', 'Clear soy sauce broth, chashu, bamboo shoots, green onions, nori', 160.00, TRUE),
(@shop_id, 'Miso Ramen', 'Hearty miso broth, corn, butter, bean sprouts, green onions', 170.00, TRUE),
(@shop_id, 'Spicy Tan Tan Ramen', 'Sesame-spicy broth, ground pork, bok choy, chili oil', 190.00, TRUE),
(@shop_id, 'Gyoza (6 pcs)', 'Pan-fried pork dumplings with dipping sauce', 80.00, TRUE),
(@shop_id, 'Karaage', 'Japanese fried chicken with mayo', 120.00, TRUE),
(@shop_id, 'Japanese Iced Tea', 'Unsweetened cold green tea', 40.00, TRUE),
(@shop_id, 'Ramune Soda', 'Classic Japanese marble soda', 60.00, FALSE);