-- MySQL initialization script
-- This file is executed when the MySQL container starts for the first time

-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS online_survey CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Grant privileges to the user
GRANT ALL PRIVILEGES ON library.* TO 'survey_user'@'%';
FLUSH PRIVILEGES;
