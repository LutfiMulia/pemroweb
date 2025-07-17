<?php
// Direct script to create password_resets table
$host = "localhost";
$dbname = "inisidentia_db";
$user = "root";
$pass = "";

try {
    // Create connection
    $conn = new mysqli($host, $user, $pass, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    echo "Connected successfully to database: $dbname<br><br>";
    
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
        echo "✓ Table 'password_resets' created successfully<br>";
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
            echo "✓ Index created successfully<br>";
        } else {
            echo "Error creating index: " . $conn->error . "<br>";
        }
    }
    
    // Verify table was created
    $result = $conn->query("SHOW TABLES LIKE 'password_resets'");
    if ($result->num_rows > 0) {
        echo "<br>✓ Table 'password_resets' exists in database<br>";
        
        // Show table structure
        $result = $conn->query("DESCRIBE password_resets");
        if ($result) {
            echo "<br><strong>Table Structure:</strong><br>";
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Default'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<br>✗ Table 'password_resets' was not created<br>";
    }
    
    $conn->close();
    echo "<br><br>✓ Database setup complete! You can now use the forgot password functionality.";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
