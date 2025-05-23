<?php
/**
 * Database Configuration File
 * Fix for: Connection failed: SQLSTATE[HY000] [2002] No such file or directory
 */

// Database configuration options
$db_configs = [
    // Configuration 1: Standard localhost
    [
        'name' => 'Standard Localhost',
        'host' => 'localhost',
        'port' => '3306',
        'dbname' => 'testdb',
        'username' => 'root',
        'password' => '',
        'dsn' => 'mysql:host=localhost;port=3306;dbname=testdb'
    ],
    // Configuration 2: IP Address
    [
        'name' => 'IP Address 127.0.0.1',
        'host' => '127.0.0.1',
        'port' => '3306',
        'dbname' => 'testdb',
        'username' => 'root',
        'password' => '',
        'dsn' => 'mysql:host=127.0.0.1;port=3306;dbname=testdb'
    ],
    // Configuration 3: Unix Socket (Mac/Linux)
    [
        'name' => 'Unix Socket /tmp/mysql.sock',
        'host' => '',
        'port' => '',
        'dbname' => 'testdb',
        'username' => 'root',
        'password' => '',
        'dsn' => 'mysql:unix_socket=/tmp/mysql.sock;dbname=testdb'
    ],
    // Configuration 4: Unix Socket Alternative
    [
        'name' => 'Unix Socket /var/run/mysqld/mysqld.sock',
        'host' => '',
        'port' => '',
        'dbname' => 'testdb',
        'username' => 'root',
        'password' => '',
        'dsn' => 'mysql:unix_socket=/var/run/mysqld/mysqld.sock;dbname=testdb'
    ],
    // Configuration 5: XAMPP/WAMP Default
    [
        'name' => 'XAMPP/WAMP Default',
        'host' => 'localhost',
        'port' => '3306',
        'dbname' => 'testdb',
        'username' => 'root',
        'password' => '',
        'dsn' => 'mysql:host=localhost;port=3306;dbname=testdb;charset=utf8'
    ]
];

/**
 * Function to test database connection
 */
function testDatabaseConnection() {
    global $db_configs;
    
    echo "<div style='font-family: Arial, sans-serif; margin: 20px; padding: 20px; background-color: #f5f5f5; border-radius: 8px;'>";
    echo "<h2>🔧 Database Connection Tester</h2>";
    
    $connection_successful = false;
    $successful_config = null;
    
    foreach ($db_configs as $index => $config) {
        echo "<div style='margin: 10px 0; padding: 15px; background-color: white; border-radius: 5px; border-left: 4px solid #2196f3;'>";
        echo "<h4>Testing: {$config['name']}</h4>";
        echo "<p><strong>DSN:</strong> {$config['dsn']}</p>";
        
        try {
            $pdo = new PDO($config['dsn'], $config['username'], $config['password']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Test if database and tables exist
            $stmt = $pdo->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            echo "<p style='color: #4caf50; font-weight: bold;'>✅ CONNECTION SUCCESSFUL!</p>";
            echo "<p><strong>Tables found:</strong> " . implode(', ', $tables) . "</p>";
            
            if (!$connection_successful) {
                $connection_successful = true;
                $successful_config = $config;
            }
            
        } catch (PDOException $e) {
            echo "<p style='color: #f44336; font-weight: bold;'>❌ Connection Failed</p>";
            echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        echo "</div>";
    }
    
    if ($connection_successful) {
        echo "<div style='margin: 20px 0; padding: 15px; background-color: #e8f5e8; border-radius: 5px; border-left: 4px solid #4caf50;'>";
        echo "<h3>🎉 Recommended Configuration</h3>";
        echo "<p>Use this configuration in your PHP files:</p>";
        echo "<pre style='background-color: #f5f5f5; padding: 10px; border-radius: 3px; overflow-x: auto;'>";
        echo htmlspecialchars("<?php\n");
        echo htmlspecialchars("\$host = '{$successful_config['host']}';\n");
        echo htmlspecialchars("\$port = '{$successful_config['port']}';\n");
        echo htmlspecialchars("\$dbname = '{$successful_config['dbname']}';\n");
        echo htmlspecialchars("\$username = '{$successful_config['username']}';\n");
        echo htmlspecialchars("\$password = '{$successful_config['password']}';\n");
        echo htmlspecialchars("\$dsn = '{$successful_config['dsn']}';\n");
        echo htmlspecialchars("\n\$pdo = new PDO(\$dsn, \$username, \$password);\n");
        echo htmlspecialchars("\$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);\n");
        echo htmlspecialchars("?>");
        echo "</pre>";
        echo "</div>";
        
        return $successful_config;
    } else {
        echo "<div style='margin: 20px 0; padding: 15px; background-color: #ffebee; border-radius: 5px; border-left: 4px solid #f44336;'>";
        echo "<h3>❌ No Successful Connections</h3>";
        echo "<h4>Troubleshooting Steps:</h4>";
        echo "<ol>";
        echo "<li><strong>Start MySQL Service:</strong>";
        echo "<ul>";
        echo "<li>Linux: <code>sudo service mysql start</code> or <code>sudo systemctl start mysql</code></li>";
        echo "<li>Mac: <code>brew services start mysql</code> or <code>sudo /usr/local/mysql/support-files/mysql.server start</code></li>";
        echo "<li>Windows: <code>net start mysql</code> or start via XAMPP/WAMP control panel</li>";
        echo "</ul></li>";
        echo "<li><strong>Check if MySQL is running:</strong> <code>ps aux | grep mysql</code> (Mac/Linux)</li>";
        echo "<li><strong>Verify MySQL socket location:</strong> <code>mysql_config --socket</code></li>";
        echo "<li><strong>Create database:</strong> Login to MySQL and run <code>CREATE DATABASE testdb;</code></li>";
        echo "<li><strong>Import data:</strong> <code>mysql -u root -p testdb < import.sql</code></li>";
        echo "</ol>";
        echo "</div>";
        
        return null;
    }
    
    echo "</div>";
}

/**
 * Get working database connection
 */
function getDatabaseConnection() {
    global $db_configs;
    
    foreach ($db_configs as $config) {
        try {
            $pdo = new PDO($config['dsn'], $config['username'], $config['password']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $pdo;
        } catch (PDOException $e) {
            continue;
        }
    }
    
    return null;
}

// If this file is accessed directly, run the connection test
if (basename($_SERVER['PHP_SELF']) == 'db_config.php') {
    testDatabaseConnection();
}
?>
