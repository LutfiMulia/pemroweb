<?php
require_once '../includes/config.php';

// SQL to create password_resets table
$sql = "CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_email (email)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'password_resets' created successfully<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Add indexes
$indexes = [
    "CREATE INDEX IF NOT EXISTS idx_token ON password_resets(token)",
    "CREATE INDEX IF NOT EXISTS idx_expires_at ON password_resets(expires_at)"
];

foreach ($indexes as $index_sql) {
    if ($conn->query($index_sql) === TRUE) {
        echo "Index created successfully<br>";
    } else {
        echo "Error creating index: " . $conn->error . "<br>";
    }
}

$conn->close();
echo "Database initialization complete!";
?>
