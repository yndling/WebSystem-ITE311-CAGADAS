<?php
// Test database connection
$db = \Config\Database::connect();
try {
    $db->initialize();
    $tables = $db->listTables();
    echo "<h3>Database Connection Successful!</h3>";
    echo "<h4>Tables in database:</h4>";
    echo "<pre>";
    print_r($tables);
    echo "</pre>";
} catch (\Exception $e) {
    echo "<h3>Database Connection Error:</h3>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    echo "<h4>Database Config:</h4>";
    echo "<pre>";
    print_r([
        'hostname' => $db->hostname,
        'database' => $db->database,
        'username' => $db->username,
        'password' => $db->password ? '***' : '(empty)',
        'DBDriver' => $db->DBDriver,
        'port' => $db->port
    ]);
    echo "</pre>";
}

// Check if user is logged in
echo "<h3>Session Data:</h3>";
session_start();
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
