USE `cantiga_ramen_shop_db`;

-- Clear existing data (order matters due to FKs)
DELETE FROM `audit_logs`;
DELETE FROM `menu_items`;
DELETE FROM `ramen_shops`;
DELETE FROM `users`;

-- Insert default admin user (password: admin123)
INSERT INTO `users` (`username`, `email`, `password_hash`, `role`, `shop_id`, `is_active`) VALUES
('admin', 'admin@ramenshop.com', '$2y$12$GZ5Ud56xvBH1DTFz2vfuEOT1OeceK8/i1AZbThmWJ/1eSTDf.0rG6', 'admin', NULL, TRUE);

SET @admin_id = LAST_INSERT_ID();

-- Insert sample ramen shop
INSERT INTO `ramen_shops` (`name`, `description`, `created_by`, `updated_by`) VALUES
('Ichiraku Ramen', 'Authentic Japanese ramen since 2020. Specializing in tonkotsu and shoyu broths made fresh daily.', @admin_id, @admin_id);

-- Get the shop ID (should be 1)
SET @shop_id = LAST_INSERT_ID();

-- Insert shop owner user (password: owner123)
INSERT INTO `users` (`username`, `email`, `password_hash`, `role`, `shop_id`, `is_active`) VALUES
('owner', 'owner@ichiraku.com', '$2y$12$j2aru6NF/8X4hyBmozbqkenP7Lew7NiiJYJue6grL.yvNBTAFiUCm', 'shop_owner', @shop_id, TRUE);

-- Insert staff user (password: staff123)
INSERT INTO `users` (`username`, `email`, `password_hash`, `role`, `shop_id`, `is_active`) VALUES
('staff', 'staff@ichiraku.com', '$2y$12$LG/dSRIAvWGafcsd1RNhuuf79J6vTUh.YcsL6UqEGotzm0RMMxV/2', 'staff', @shop_id, TRUE);

-- Insert sample menu items
INSERT INTO `menu_items` (`shop_id`, `name`, `description`, `price`, `is_available`, `created_by`, `updated_by`) VALUES
(@shop_id, 'Tonkotsu Ramen', 'Rich pork bone broth, chashu, soft-boiled egg, green onions, nori', 180.00, TRUE, @admin_id, @admin_id),
(@shop_id, 'Shoyu Ramen', 'Clear soy sauce broth, chashu, bamboo shoots, green onions, nori', 160.00, TRUE, @admin_id, @admin_id),
(@shop_id, 'Miso Ramen', 'Hearty miso broth, corn, butter, bean sprouts, green onions', 170.00, TRUE, @admin_id, @admin_id),
(@shop_id, 'Spicy Tan Tan Ramen', 'Sesame-spicy broth, ground pork, bok choy, chili oil', 190.00, TRUE, @admin_id, @admin_id),
(@shop_id, 'Gyoza (6 pcs)', 'Pan-fried pork dumplings with dipping sauce', 80.00, TRUE, @admin_id, @admin_id),
(@shop_id, 'Karaage', 'Japanese fried chicken with mayo', 120.00, TRUE, @admin_id, @admin_id),
(@shop_id, 'Japanese Iced Tea', 'Unsweetened cold green tea', 40.00, TRUE, @admin_id, @admin_id),
(@shop_id, 'Ramune Soda', 'Classic Japanese marble soda', 60.00, FALSE, @admin_id, @admin_id);