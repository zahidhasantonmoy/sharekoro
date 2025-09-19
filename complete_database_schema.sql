-- ShareKoro Complete Database Schema
-- Database: if0_39860069_sharekoro

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- Create categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT
);

-- Create shares table with original schema
CREATE TABLE IF NOT EXISTS shares (
    id INT AUTO_INCREMENT PRIMARY KEY,
    share_key VARCHAR(50) UNIQUE NOT NULL,
    title VARCHAR(255),
    content LONGTEXT,
    file_path VARCHAR(255),
    file_name VARCHAR(255),
    file_size INT,
    share_type ENUM('text', 'code', 'file') NOT NULL,
    expiration_date TIMESTAMP NULL,
    created_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    view_count INT DEFAULT 0,
    is_public BOOLEAN DEFAULT TRUE,
    password_protect VARCHAR(255) NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Add visibility features to shares table (if not already added)
-- Add visibility column to shares table
ALTER TABLE shares 
ADD COLUMN IF NOT EXISTS visibility ENUM('public', 'private', 'protected') DEFAULT 'public' AFTER is_public;

-- Add password column for private content
ALTER TABLE shares 
ADD COLUMN IF NOT EXISTS access_password VARCHAR(255) NULL AFTER password_protect;

-- Add access_code column for protected content
ALTER TABLE shares 
ADD COLUMN IF NOT EXISTS access_code VARCHAR(10) NULL AFTER access_password;

-- Update existing shares to have visibility set (only if needed)
UPDATE shares SET visibility = 'public' WHERE is_public = 1 AND visibility IS NULL;
UPDATE shares SET visibility = 'private' WHERE is_public = 0 AND password_protect IS NOT NULL AND visibility IS NULL;
UPDATE shares SET visibility = 'protected' WHERE is_public = 0 AND password_protect IS NULL AND visibility IS NULL;

-- Create share_categories table
CREATE TABLE IF NOT EXISTS share_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    share_id INT,
    category_id INT,
    FOREIGN KEY (share_id) REFERENCES shares(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Create reports table
CREATE TABLE IF NOT EXISTS reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    share_id INT,
    reporter_ip VARCHAR(45),
    reason TEXT,
    status ENUM('pending', 'resolved') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (share_id) REFERENCES shares(id) ON DELETE CASCADE
);

-- Create collections table
CREATE TABLE IF NOT EXISTS collections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_public BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- Create collection_shares table
CREATE TABLE IF NOT EXISTS collection_shares (
    id INT AUTO_INCREMENT PRIMARY KEY,
    collection_id INT NOT NULL,
    share_id INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (collection_id) REFERENCES collections(id) ON DELETE CASCADE,
    FOREIGN KEY (share_id) REFERENCES shares(id) ON DELETE CASCADE,
    UNIQUE KEY unique_collection_share (collection_id, share_id)
);

-- Insert default categories (only if they don't exist)
INSERT IGNORE INTO categories (name, description) VALUES 
('Text', 'Plain text shares'),
('Code', 'Programming code snippets'),
('Document', 'Document files'),
('Image', 'Image files'),
('Other', 'Other file types');

-- Add indexes for better performance (if not already added)
CREATE INDEX IF NOT EXISTS idx_shares_visibility ON shares(visibility);
CREATE INDEX IF NOT EXISTS idx_shares_access_code ON shares(access_code);
CREATE INDEX IF NOT EXISTS idx_shares_created_at ON shares(created_at);
CREATE INDEX IF NOT EXISTS idx_shares_expiration ON shares(expiration_date);
CREATE INDEX IF NOT EXISTS idx_shares_is_public ON shares(is_public);