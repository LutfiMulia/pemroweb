-- SQL query to create password_resets table
-- Copy and paste this into phpMyAdmin SQL tab

CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_email (email)
);

-- Add indexes for better performance
CREATE INDEX IF NOT EXISTS idx_token ON password_resets(token);
CREATE INDEX IF NOT EXISTS idx_expires_at ON password_resets(expires_at);

-- Verify table was created
SHOW TABLES LIKE 'password_resets';
DESCRIBE password_resets;
