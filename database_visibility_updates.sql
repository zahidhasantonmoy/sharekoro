-- Database schema updates for content visibility features

-- Add visibility column to shares table
ALTER TABLE shares 
ADD COLUMN visibility ENUM('public', 'private', 'protected') DEFAULT 'public' AFTER is_public;

-- Add password column for private content
ALTER TABLE shares 
ADD COLUMN access_password VARCHAR(255) NULL AFTER password_protect;

-- Add access_code column for protected content
ALTER TABLE shares 
ADD COLUMN access_code VARCHAR(10) NULL AFTER access_password;

-- Update existing shares to have visibility set
UPDATE shares SET visibility = 'public' WHERE is_public = 1;
UPDATE shares SET visibility = 'private' WHERE is_public = 0 AND password_protect IS NOT NULL;
UPDATE shares SET visibility = 'protected' WHERE is_public = 0 AND password_protect IS NULL;

-- Add indexes for better performance
CREATE INDEX idx_shares_visibility ON shares(visibility);
CREATE INDEX idx_shares_access_code ON shares(access_code);