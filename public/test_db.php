<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load the framework
require __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Database configuration
$host = 'localhost';
$dbname = 'lms_cagadas';
$username = 'root';
$password = '';

// Create connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Database Connection Successful!</h2>";
    
    // Check if enrollments table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'enrollments'");
    if ($stmt->rowCount() > 0) {
        echo "<p>✅ Enrollments table exists.</p>";
        
        // Show table structure
        $stmt = $pdo->query("DESCRIBE enrollments");
        echo "<h3>Enrollments Table Structure:</h3>";
        echo "<table border='1'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Show sample data
        $stmt = $pdo->query("SELECT * FROM enrollments LIMIT 5");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($rows) > 0) {
            echo "<h3>Sample Data (First 5 Rows):</h3>";
            echo "<table border='1'>";
            // Headers
            echo "<tr>";
            foreach (array_keys($rows[0]) as $col) {
                echo "<th>" . htmlspecialchars($col) . "</th>";
            }
            echo "</tr>";
            // Data
            foreach ($rows as $row) {
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No data found in the enrollments table.</p>";
        }
    } else {
        echo "<p>❌ Enrollments table does not exist.</p>";
    }
    
} catch (PDOException $e) {
    echo "<h2>Database Connection Failed</h2>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    
    // Additional troubleshooting
    echo "<h3>Troubleshooting:</h3>";
    echo "<ol>";
    echo "<li>Make sure MySQL server is running.</li>";
    echo "<li>Check if the database 'lms_cagadas' exists.</li>";
    echo "<li>Verify the username and password in app/Config/Database.php</li>";
    echo "<li>Check if the MySQL user has proper permissions on the database.</li>";
    echo "</ol>";
}

// Check if the database exists
$pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
$stmt = $pdo->query("SHOW DATABASES LIKE '$dbname'");
if ($stmt->rowCount() > 0) {
    echo "<p>✅ Database '$dbname' exists.</p>";
} else {
    echo "<p>❌ Database '$dbname' does not exist.</p>";
}

// Show all databases (for debugging)
echo "<h3>Available Databases:</h3>";
$stmt = $pdo->query("SHOW DATABASES");
echo "<ul>";
while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
    echo "<li>" . htmlspecialchars($row[0]) . "</li>";
}
echo "</ul>";
?>
