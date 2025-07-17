<?php
// Simple test script to verify forgot password functionality
echo "<h2>Forgot Password System Test</h2>";
echo "<p>Testing database connection and table creation...</p>";

try {
    require_once '../includes/config.php';
    echo "✓ Database connection successful<br>";
    
    // Test if password_resets table exists
    $result = $conn->query("DESCRIBE password_resets");
    if ($result) {
        echo "✓ password_resets table exists<br>";
        echo "<h3>Table Structure:</h3>";
        echo "<table border='1'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "✗ password_resets table does not exist<br>";
        echo "Please run the SQL commands in SETUP_INSTRUCTIONS.md<br>";
    }
    
    // Test token generation
    $token = bin2hex(random_bytes(32));
    echo "<h3>Token Generation Test:</h3>";
    echo "Sample token: " . $token . "<br>";
    echo "Token length: " . strlen($token) . " characters<br>";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Create the password_resets table using phpMyAdmin or MySQL command line</li>";
echo "<li>Test the forgot password form at: <a href='forgot-password.php'>forgot-password.php</a></li>";
echo "<li>Test the login page at: <a href='login.php'>login.php</a></li>";
echo "</ol>";
?>
