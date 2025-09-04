-- ShareKoro Database Schema
-- Database: if0_39860069_sharekoro

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT
);

CREATE TABLE shares (
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

CREATE TABLE share_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    share_id INT,
    category_id INT,
    FOREIGN KEY (share_id) REFERENCES shares(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

CREATE TABLE reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    share_id INT,
    reporter_ip VARCHAR(45),
    reason TEXT,
    status ENUM('pending', 'resolved') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (share_id) REFERENCES shares(id) ON DELETE CASCADE
);

-- Insert default categories
INSERT INTO categories (name, description) VALUES 
('Text', 'Plain text shares'),
('Code', 'Programming code snippets'),
('Document', 'Document files'),
('Image', 'Image files'),
('Other', 'Other file types');