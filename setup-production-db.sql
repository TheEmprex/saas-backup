-- Production Database Setup Script
-- Run this on your production MySQL server

-- Create database
CREATE DATABASE your_production_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user
CREATE USER 'your_db_user'@'%' IDENTIFIED BY 'your_secure_password';

-- Grant privileges
GRANT ALL PRIVILEGES ON your_production_db.* TO 'your_db_user'@'%';
FLUSH PRIVILEGES;

-- Verify
SELECT User, Host FROM mysql.user WHERE User = 'your_db_user';
SHOW DATABASES;
