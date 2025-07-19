<?php
require_once 'src/includes/config.php';

echo "<h2>Test User Creation</h2>";

// Data test user
$test_users = [
    [
        'name' => 'Test User',
        'email' => 'user@test.com', 
        'password' => 'user123',
        'role_id' => 2, // user role
        'status' => 'aktif'
    ],
    [
        'name' => 'Test Admin', 
        'email' => 'admin@test.com',
        'password' => 'admin123', 
        'role_id' => 1, // admin role
        'status' => 'aktif'
    ]
];

foreach ($test_users as $user) {
    // Check if user already exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $user['email']);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows == 0) {
        // Hash password
        $hashed_password = password_hash($user['password'], PASSWORD_DEFAULT);
        
        // Insert new user
        $insert = $conn->prepare("INSERT INTO users (name, email, password, role_id, status) VALUES (?, ?, ?, ?, ?)");
        $insert->bind_param("sssis", $user['name'], $user['email'], $hashed_password, $user['role_id'], $user['status']);
        
        if ($insert->execute()) {
            echo "✅ User created: {$user['email']} (password: {$user['password']})<br>";
        } else {
            echo "❌ Failed to create user: {$user['email']}<br>";
        }
    } else {
        echo "ℹ️ User already exists: {$user['email']}<br>";
    }
}

echo "<h3>Current Users:</h3>";
$query = $conn->query("SELECT u.name, u.email, r.name as role_name, u.status FROM users u JOIN roles r ON u.role_id = r.id ORDER BY u.id");
if ($query) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th></tr>";
    while ($row = $query->fetch_assoc()) {
        echo "<tr><td>{$row['name']}</td><td>{$row['email']}</td><td>{$row['role_name']}</td><td>{$row['status']}</td></tr>";
    }
    echo "</table>";
}

echo "<br><h3>Test Login Credentials:</h3>";
echo "<strong>Admin:</strong> admin@test.com / admin123<br>";
echo "<strong>User:</strong> user@test.com / user123<br>";

echo "<br><a href='src/auth/login.php'>🔐 Go to Login Page</a>";
?>
