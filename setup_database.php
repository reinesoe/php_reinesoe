<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - SOAL 3</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .success { 
            background-color: #d4edda; 
            color: #155724; 
            padding: 15px; 
            border-radius: 5px; 
            border-left: 4px solid #28a745;
            margin: 15px 0;
        }
        .error { 
            background-color: #f8d7da; 
            color: #721c24; 
            padding: 15px; 
            border-radius: 5px; 
            border-left: 4px solid #dc3545;
            margin: 15px 0;
        }
        .warning { 
            background-color: #fff3cd; 
            color: #856404; 
            padding: 15px; 
            border-radius: 5px; 
            border-left: 4px solid #ffc107;
            margin: 15px 0;
        }
        .info { 
            background-color: #d1ecf1; 
            color: #0c5460; 
            padding: 15px; 
            border-radius: 5px; 
            border-left: 4px solid #17a2b8;
            margin: 15px 0;
        }
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 10px 5px;
            font-weight: bold;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .btn-success { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); }
        .btn-danger { background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%); }
        pre {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            border: 1px solid #dee2e6;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table th, table td {
            border: 1px solid #dee2e6;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Database Setup - SOAL 3</h1>
        
        <?php
        // Database configuration
        $host = '127.0.0.1';
        $port = '3306';
        $dbname = 'testdb';
        $username = 'root';
        $password = '';

        // Connection methods to try
        $connection_methods = [
            "mysql:host=127.0.0.1;port=3306",
            "mysql:host=localhost;port=3306",
            "mysql:unix_socket=/tmp/mysql.sock",
            "mysql:unix_socket=/var/run/mysqld/mysqld.sock"
        ];

        $setup_action = $_GET['action'] ?? '';
        
        if ($setup_action == 'create_database') {
            echo "<h2>📋 Creating Database and Tables...</h2>";
            
            $pdo = null;
            $connection_error = '';
            
            // Try to connect without specifying database first
            foreach ($connection_methods as $dsn) {
                try {
                    $pdo = new PDO($dsn, $username, $password);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    break;
                } catch(PDOException $e) {
                    $connection_error = $e->getMessage();
                    continue;
                }
            }
            
            if (!$pdo) {
                echo "<div class='error'>";
                echo "<h3>❌ Cannot Connect to MySQL</h3>";
                echo "<p><strong>Error:</strong> " . htmlspecialchars($connection_error) . "</p>";
                echo "<p>Please make sure MySQL is running and try again.</p>";
                echo "</div>";
            } else {
                try {
                    // Create database if it doesn't exist
                    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci");
                    echo "<div class='success'>✅ Database '$dbname' created successfully!</div>";
                    
                    // Use the database
                    $pdo->exec("USE `$dbname`");
                    
                    // Create person table
                    $pdo->exec("
                        CREATE TABLE IF NOT EXISTS `person` (
                          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                          `nama` varchar(200) NOT NULL,
                          `alamat` varchar(200) DEFAULT NULL,
                          PRIMARY KEY (`id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1
                    ");
                    echo "<div class='success'>✅ Table 'person' created successfully!</div>";
                    
                    // Create hobi table
                    $pdo->exec("
                        CREATE TABLE IF NOT EXISTS `hobi` (
                          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                          `person_id` int(11) DEFAULT NULL,
                          `hobi` varchar(200) NOT NULL,
                          PRIMARY KEY (`id`),
                          KEY `idx_person_id` (`person_id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1
                    ");
                    echo "<div class='success'>✅ Table 'hobi' created successfully!</div>";
                    
                    // Insert sample data into person table
                    $pdo->exec("
                        INSERT IGNORE INTO `person` VALUES 
                        (1,'coba','cobafdsfd'),
                        (2,'ana 5','arab'),
                        (3,'Tari','Dakota'),
                        (4,'Cak Gembul x','Surabaya gg gg hhhhhh'),
                        (5,'Mc Greg x','Ujung Berung y'),
                        (6,'SENTOT xx','Bandung yhhh'),
                        (7,'Ni Made vv ff','Ujung Berung'),
                        (8,'Nama1 xx g','Alamat1 yy fg'),
                        (9,'Nama12','Alamat14'),
                        (10,'ANNAA','Jakarta')
                    ");
                    echo "<div class='success'>✅ Sample data inserted into 'person' table!</div>";
                    
                    // Insert sample data into hobi table
                    $pdo->exec("
                        INSERT IGNORE INTO `hobi` VALUES 
                        (1,1,'Futsal'),
                        (2,1,'Soccer'),
                        (3,1,'Tenis Meja'),
                        (4,2,'Basket'),
                        (5,2,'Renang'),
                        (6,3,'Futsal'),
                        (7,3,'Membaca'),
                        (8,3,'Renang'),
                        (9,3,'Game'),
                        (10,4,'Renang'),
                        (11,5,'Jalan-Jalan')
                    ");
                    echo "<div class='success'>✅ Sample data inserted into 'hobi' table!</div>";
                    
                    // Show sample data
                    echo "<h3>📊 Sample Data Preview:</h3>";
                    $stmt = $pdo->query("
                        SELECT p.nama, p.alamat, GROUP_CONCAT(h.hobi SEPARATOR ', ') as hobi_list
                        FROM person p 
                        LEFT JOIN hobi h ON p.id = h.person_id 
                        GROUP BY p.id, p.nama, p.alamat 
                        ORDER BY p.nama 
                        LIMIT 5
                    ");
                    $samples = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    echo "<table>";
                    echo "<tr><th>Nama</th><th>Alamat</th><th>Hobi</th></tr>";
                    foreach ($samples as $row) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['alamat']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['hobi_list'] ?? 'No hobi') . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                    
                    echo "<div class='success'>";
                    echo "<h3>🎉 Setup Complete!</h3>";
                    echo "<p>Database and tables have been created successfully. You can now use the applications:</p>";
                    echo "<a href='soal3a.php' class='btn btn-success'>Open soal3a.php</a>";
                    echo "<a href='soal3b.php' class='btn btn-success'>Open soal3b.php</a>";
                    echo "</div>";
                    
                } catch (PDOException $e) {
                    echo "<div class='error'>";
                    echo "<h3>❌ Error Creating Database/Tables</h3>";
                    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
                    echo "</div>";
                }
            }
            
        } else {
            // Show setup options
            echo "<div class='info'>";
            echo "<h2>🔧 Database Setup Options</h2>";
            echo "<p>This tool will help you set up the database for SOAL 3 automatically.</p>";
            echo "</div>";
            
            echo "<h3>📋 What will be created:</h3>";
            echo "<ul>";
            echo "<li>Database: <strong>testdb</strong></li>";
            echo "<li>Table: <strong>person</strong> (with sample data)</li>";
            echo "<li>Table: <strong>hobi</strong> (with sample data)</li>";
            echo "<li>Proper relationships between tables</li>";
            echo "</ul>";
            
            echo "<div class='warning'>";
            echo "<h4>⚠️ Prerequisites:</h4>";
            echo "<ul>";
            echo "<li>MySQL server must be running</li>";
            echo "<li>MySQL root access (or modify credentials in the script)</li>";
            echo "<li>PHP PDO MySQL extension enabled</li>";
            echo "</ul>";
            echo "</div>";
            
            echo "<h3>🚀 Start Setup:</h3>";
            echo "<a href='?action=create_database' class='btn'>Create Database & Tables</a>";
            
            echo "<h3>📁 Alternative: Manual Setup</h3>";
            echo "<p>If automatic setup doesn't work, you can manually run:</p>";
            echo "<pre>mysql -u root -p < import.sql</pre>";
            
            echo "<h3>🔍 Test Connection</h3>";
            echo "<a href='test_connection.php' class='btn'>Test Database Connection</a>";
        }
        ?>
    </div>
</body>
</html>
