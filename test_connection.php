<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Connection Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .success { color: #4caf50; font-weight: bold; }
        .error { color: #f44336; font-weight: bold; }
        .info { background-color: #e3f2fd; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .command { background-color: #f5f5f5; padding: 10px; border-radius: 3px; font-family: monospace; }
        pre { background-color: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Database Connection Troubleshooter</h1>
        
        <?php
        // Include the database configuration
        include 'db_config.php';
        
        echo "<h2>📊 System Information</h2>";
        echo "<div class='info'>";
        echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
        echo "<p><strong>Operating System:</strong> " . PHP_OS . "</p>";
        echo "<p><strong>Server Software:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</p>";
        echo "<p><strong>PDO MySQL Available:</strong> " . (extension_loaded('pdo_mysql') ? '✅ Yes' : '❌ No') . "</p>";
        echo "</div>";
        
        if (!extension_loaded('pdo_mysql')) {
            echo "<div style='background-color: #ffebee; padding: 15px; border-radius: 5px; border-left: 4px solid #f44336;'>";
            echo "<h3>❌ PDO MySQL Extension Not Found</h3>";
            echo "<p>You need to install or enable the PDO MySQL extension:</p>";
            echo "<ul>";
            echo "<li><strong>Ubuntu/Debian:</strong> <code>sudo apt-get install php-mysql</code></li>";
            echo "<li><strong>CentOS/RHEL:</strong> <code>sudo yum install php-mysql</code></li>";
            echo "<li><strong>Mac (Homebrew):</strong> Usually included with PHP</li>";
            echo "<li><strong>Windows (XAMPP):</strong> Uncomment <code>extension=pdo_mysql</code> in php.ini</li>";
            echo "</ul>";
            echo "</div>";
            exit;
        }
        
        echo "<h2>🔍 Testing Database Connections</h2>";
        
        // Test all connection methods
        $working_config = testDatabaseConnection();
        
        if (!$working_config) {
            echo "<h2>🚀 Quick Setup Guide</h2>";
            echo "<div class='info'>";
            echo "<h3>Step 1: Start MySQL</h3>";
            echo "<div class='command'>";
            echo "# For Mac with Homebrew:<br>";
            echo "brew services start mysql<br><br>";
            echo "# For Linux:<br>";
            echo "sudo service mysql start<br>";
            echo "# or<br>";
            echo "sudo systemctl start mysql<br><br>";
            echo "# For Windows (XAMPP):<br>";
            echo "Start XAMPP Control Panel and start MySQL<br><br>";
            echo "# For Windows (Command):<br>";
            echo "net start mysql";
            echo "</div>";
            
            echo "<h3>Step 2: Create Database</h3>";
            echo "<div class='command'>";
            echo "mysql -u root -p<br>";
            echo "CREATE DATABASE testdb;<br>";
            echo "exit;";
            echo "</div>";
            
            echo "<h3>Step 3: Import Data</h3>";
            echo "<div class='command'>";
            echo "mysql -u root -p testdb < import.sql";
            echo "</div>";
            
            echo "<h3>Step 4: Test Connection</h3>";
            echo "<p>Refresh this page to test the connection again.</p>";
            echo "</div>";
        } else {
            echo "<h2>✅ Connection Successful!</h2>";
            echo "<div style='background-color: #e8f5e8; padding: 15px; border-radius: 5px;'>";
            echo "<p>Your database is working correctly. You can now use:</p>";
            echo "<ul>";
            echo "<li><a href='soal3a.php' target='_blank'>soal3a.php</a> - Standard version</li>";
            echo "<li><a href='soal3b.php' target='_blank'>soal3b.php</a> - Advanced version</li>";
            echo "</ul>";
            echo "</div>";
            
            // Test if tables exist and have data
            try {
                $pdo = getDatabaseConnection();
                if ($pdo) {
                    echo "<h3>📋 Database Content Check</h3>";
                    
                    // Check person table
                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM person");
                    $person_count = $stmt->fetch()['count'];
                    echo "<p><strong>Person table:</strong> {$person_count} records</p>";
                    
                    // Check hobi table
                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM hobi");
                    $hobi_count = $stmt->fetch()['count'];
                    echo "<p><strong>Hobi table:</strong> {$hobi_count} records</p>";
                    
                    if ($person_count == 0 || $hobi_count == 0) {
                        echo "<div style='background-color: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107;'>";
                        echo "<h4>⚠️ Tables are empty</h4>";
                        echo "<p>Run this command to import data:</p>";
                        echo "<div class='command'>mysql -u root -p testdb < import.sql</div>";
                        echo "</div>";
                    }
                    
                    // Show sample data
                    if ($person_count > 0) {
                        echo "<h4>Sample Data:</h4>";
                        $stmt = $pdo->query("SELECT p.nama, p.alamat, GROUP_CONCAT(h.hobi) as hobi_list FROM person p LEFT JOIN hobi h ON p.id = h.person_id GROUP BY p.id LIMIT 3");
                        $samples = $stmt->fetchAll();
                        
                        echo "<table style='border-collapse: collapse; width: 100%; margin-top: 10px;'>";
                        echo "<tr style='background-color: #f0f0f0;'>";
                        echo "<th style='border: 1px solid #ddd; padding: 8px;'>Nama</th>";
                        echo "<th style='border: 1px solid #ddd; padding: 8px;'>Alamat</th>";
                        echo "<th style='border: 1px solid #ddd; padding: 8px;'>Hobi</th>";
                        echo "</tr>";
                        
                        foreach ($samples as $row) {
                            echo "<tr>";
                            echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($row['nama']) . "</td>";
                            echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($row['alamat']) . "</td>";
                            echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($row['hobi_list'] ?? 'No hobi') . "</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                    }
                }
            } catch (Exception $e) {
                echo "<p class='error'>Error checking database content: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        }
        ?>
        
        <h2>📚 Additional Resources</h2>
        <div class="info">
            <h4>Common MySQL Socket Locations:</h4>
            <ul>
                <li><strong>Mac (Homebrew):</strong> /tmp/mysql.sock</li>
                <li><strong>Mac (MySQL.com):</strong> /var/mysql/mysql.sock</li>
                <li><strong>Linux (Ubuntu):</strong> /var/run/mysqld/mysqld.sock</li>
                <li><strong>Linux (CentOS):</strong> /var/lib/mysql/mysql.sock</li>
                <li><strong>XAMPP:</strong> Usually uses localhost:3306</li>
            </ul>
            
            <h4>Find MySQL Socket:</h4>
            <div class="command">
                mysql_config --socket<br>
                # or<br>
                mysql -u root -p -e "SHOW VARIABLES LIKE 'socket';"
            </div>
        </div>
        
        <p style="text-align: center; margin-top: 30px;">
            <a href="?" style="background-color: #2196f3; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">🔄 Test Again</a>
        </p>
    </div>
</body>
</html>
