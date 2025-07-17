<!DOCTYPE html>
<html>
<head>
    <title>Database Setup - Insidentia</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .button { background-color: #4CAF50; color: white; padding: 10px 20px; border: none; cursor: pointer; margin: 10px 0; }
        .button:hover { background-color: #45a049; }
    </style>
</head>
<body>
    <h1>Database Setup - Password Reset Table</h1>
    
    <?php
    $host = "localhost";
    $dbname = "inisidentia_db";
    $user = "root";
    $pass = "";
    
    try {
        // Create connection using PDO (more compatible)
        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";
        $pdo = new PDO($dsn, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<p class='success'>✓ Connected to database: $dbname</p>";
        
        // Check if table exists
        $stmt = $pdo->prepare("SHOW TABLES LIKE 'password_resets'");
        $stmt->execute();
        $tableExists = $stmt->rowCount() > 0;
        
        if ($tableExists) {
            echo "<p class='info'>ℹ Table 'password_resets' already exists</p>";
        } else {
            echo "<p class='info'>Table 'password_resets' does not exist. Creating...</p>";
            
            // Create table
            $sql = "CREATE TABLE password_resets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) NOT NULL,
                token VARCHAR(255) NOT NULL UNIQUE,
                expires_at TIMESTAMP NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_email (email)
            )";
            
            $pdo->exec($sql);
            echo "<p class='success'>✓ Table 'password_resets' created successfully</p>";
            
            // Add indexes
            $pdo->exec("CREATE INDEX idx_token ON password_resets(token)");
            $pdo->exec("CREATE INDEX idx_expires_at ON password_resets(expires_at)");
            echo "<p class='success'>✓ Indexes created successfully</p>";
        }
        
        // Show table structure
        $stmt = $pdo->prepare("DESCRIBE password_resets");
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h2>Table Structure:</h2>";
        echo "<table>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Default'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($column['Extra']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Test insert and delete
        echo "<h2>Testing Table:</h2>";
        $testToken = bin2hex(random_bytes(32));
        $testEmail = "test@example.com";
        $testExpires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Insert test data
        $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$testEmail, $testToken, $testExpires]);
        echo "<p class='success'>✓ Test data inserted successfully</p>";
        
        // Delete test data
        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
        $stmt->execute([$testEmail]);
        echo "<p class='success'>✓ Test data deleted successfully</p>";
        
        echo "<h2>🎉 Database Setup Complete!</h2>";
        echo "<p class='success'>Your forgot password functionality is now ready to use.</p>";
        echo "<p><a href='forgot-password.php' class='button'>Test Forgot Password</a></p>";
        echo "<p><a href='login.php' class='button'>Go to Login</a></p>";
        
    } catch (PDOException $e) {
        echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
        
        // If PDO fails, try alternative connection method
        echo "<h2>Alternative Setup Method:</h2>";
        echo "<p>If you see a connection error, please:</p>";
        echo "<ol>";
        echo "<li>Open phpMyAdmin: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a></li>";
        echo "<li>Select database: <strong>inisidentia_db</strong></li>";
        echo "<li>Click 'SQL' tab</li>";
        echo "<li>Copy and paste this SQL:</li>";
        echo "</ol>";
        
        echo "<textarea rows='10' cols='80' readonly>";
        echo "CREATE TABLE IF NOT EXISTS password_resets (\n";
        echo "    id INT AUTO_INCREMENT PRIMARY KEY,\n";
        echo "    email VARCHAR(255) NOT NULL,\n";
        echo "    token VARCHAR(255) NOT NULL UNIQUE,\n";
        echo "    expires_at TIMESTAMP NOT NULL,\n";
        echo "    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,\n";
        echo "    UNIQUE KEY unique_email (email)\n";
        echo ");\n\n";
        echo "CREATE INDEX idx_token ON password_resets(token);\n";
        echo "CREATE INDEX idx_expires_at ON password_resets(expires_at);";
        echo "</textarea>";
        
        echo "<p>5. Click 'Go' to execute</p>";
    }
    ?>
</body>
</html>
