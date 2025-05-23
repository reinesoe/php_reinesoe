<?php
// Database configuration
$host = '127.0.0.1';  // Try 127.0.0.1 instead of localhost
$port = '3306';       // Default MySQL port
$dbname = 'testdb';
$username = 'root';
$password = '';

// Try multiple connection methods
$pdo = null;
$connection_methods = [
    "mysql:host=127.0.0.1;port=3306;dbname=$dbname",
    "mysql:host=localhost;port=3306;dbname=$dbname",
    "mysql:unix_socket=/tmp/mysql.sock;dbname=$dbname",
    "mysql:unix_socket=/var/run/mysqld/mysqld.sock;dbname=$dbname",
    "mysql:host=127.0.0.1;port=3306;dbname=$dbname;charset=utf8"
];

$connection_error = '';
foreach ($connection_methods as $dsn) {
    try {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        break; // Connection successful
    } catch (PDOException $e) {
        $connection_error = $e->getMessage();
        continue; // Try next method
    }
}

if (!$pdo) {
    echo "<div style='background-color: #ffebee; border: 1px solid #f44336; padding: 20px; margin: 20px; border-radius: 5px;'>";
    echo "<h3 style='color: #d32f2f;'>❌ Database Connection Error</h3>";
    echo "<p><strong>Last error:</strong> " . htmlspecialchars($connection_error) . "</p>";
    echo "<h4>🔧 Troubleshooting Steps:</h4>";
    echo "<ol>";
    echo "<li><strong>Check if MySQL is running:</strong><br><code>sudo service mysql start</code> (Linux)<br><code>brew services start mysql</code> (Mac)</li>";
    echo "<li><strong>Verify database exists:</strong><br>Login to MySQL and run: <code>SHOW DATABASES;</code></li>";
    echo "<li><strong>Import the database:</strong><br><code>mysql -u root -p < import.sql</code></li>";
    echo "<li><strong>Check MySQL socket location:</strong><br><code>mysql_config --socket</code></li>";
    echo "<li><strong>Try different host:</strong> localhost, 127.0.0.1, or socket path</li>";
    echo "</ol>";
    echo "<p><strong>💡 Quick Fix:</strong> Make sure MySQL is running and database 'testdb' exists!</p>";
    echo "</div>";
    exit;
}

// Check if tables exist and create them if they don't
function checkAndCreateTables($pdo)
{
    try {
        // Check if person table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'person'");
        if ($stmt->rowCount() == 0) {
            // Create person table
            $pdo->exec("
                CREATE TABLE `person` (
                  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `nama` varchar(200) NOT NULL,
                  `alamat` varchar(200) DEFAULT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1
            ");

            // Insert sample data
            $pdo->exec("
                INSERT INTO `person` VALUES
                (1,'coba','cobafdsfd'),
                (2,'ana 5','arab'),
                (3,'Tari','Dakota'),
                (4,'Cak Gembul x','Surabaya gg gg hhhhhh'),
                (5,'Mc Greg x','Ujung Berung y'),
                (6,'SENTOT xx','Bandung yhhh')
            ");
        }

        // Check if hobi table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'hobi'");
        if ($stmt->rowCount() == 0) {
            // Create hobi table
            $pdo->exec("
                CREATE TABLE `hobi` (
                  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `person_id` int(11) DEFAULT NULL,
                  `hobi` varchar(200) NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `idx_person_id` (`person_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1
            ");

            // Insert sample data
            $pdo->exec("
                INSERT INTO `hobi` VALUES
                (1,1,'Futsal'),
                (2,1,'Soccer'),
                (3,1,'Tenis Meja'),
                (4,2,'Basket'),
                (5,2,'Renang'),
                (6,3,'Futsal'),
                (7,3,'Membaca'),
                (8,3,'Renang'),
                (9,3,'Game')
            ");
        }

        return true;
    } catch (PDOException $e) {
        return "Error creating tables: " . $e->getMessage();
    }
}

// Check and create tables
$table_check = checkAndCreateTables($pdo);
if ($table_check !== true) {
    echo "<div style='background-color: #fff3cd; border: 1px solid #ffc107; padding: 20px; margin: 20px; border-radius: 5px;'>";
    echo "<h3 style='color: #856404;'>⚠️ Table Creation Error</h3>";
    echo "<p>" . htmlspecialchars($table_check) . "</p>";
    echo "<p><strong>Manual Fix:</strong> Run the import.sql file:</p>";
    echo "<code>mysql -u root -p testdb < import.sql</code>";
    echo "</div>";
    exit;
}

// Handle search
$search_results = [];
$search_performed = false;

if ($_POST) {
    $search_performed = true;
    $nama = trim($_POST['nama'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $hobi = trim($_POST['hobi'] ?? '');

    // Build query with JOIN to get hobi data
    $sql = "SELECT DISTINCT p.id, p.nama, p.alamat,
                   GROUP_CONCAT(h.hobi SEPARATOR ', ') as hobi_list
            FROM person p
            LEFT JOIN hobi h ON p.id = h.person_id
            WHERE 1=1";

    $params = [];

    if (!empty($nama)) {
        $sql .= " AND p.nama LIKE :nama";
        $params[':nama'] = '%' . $nama . '%';
    }

    if (!empty($alamat)) {
        $sql .= " AND p.alamat LIKE :alamat";
        $params[':alamat'] = '%' . $alamat . '%';
    }

    if (!empty($hobi)) {
        $sql .= " AND p.id IN (SELECT DISTINCT person_id FROM hobi WHERE hobi LIKE :hobi)";
        $params[':hobi'] = '%' . $hobi . '%';
    }

    $sql .= " GROUP BY p.id, p.nama, p.alamat ORDER BY p.nama";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencarian Data Person & Hobi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .search-form {
            border: 2px solid #333;
            padding: 20px;
            margin: 20px 0;
            background-color: #fff;
        }

        .form-row {
            margin-bottom: 15px;
        }

        .form-row label {
            display: inline-block;
            width: 80px;
            font-weight: bold;
        }

        .form-row input[type="text"] {
            padding: 8px;
            border: 2px solid #333;
            width: 200px;
            margin-left: 10px;
        }

        .search-btn {
            background-color: #f0f0f0;
            border: 2px solid #333;
            padding: 10px 20px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }

        .search-btn:hover {
            background-color: #e0e0e0;
        }

        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border: 2px solid #333;
        }

        .results-table th,
        .results-table td {
            border: 1px solid #333;
            padding: 10px;
            text-align: left;
        }

        .results-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .no-results {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }

        .info-text {
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .sample-data {
            background-color: #f9f9f9;
            padding: 15px;
            border: 1px solid #ddd;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>SOAL 3: Pencarian Data Person & Hobi</h1>

        <div class="info-text">
            <strong>Database:</strong> testdb<br>
            <strong>Struktur:</strong> Tabel hobi memiliki relasi dengan tabel person dimana field "id" pada tabel person sama dengan field "person_id" pada tabel "hobi".<br>
            Jadi satu orang pada tabel person bisa memiliki beberapa hobi.<br>
            <strong>Tugas:</strong> Buat file import.sql yang berisi sql struktur database testdb tersebut
        </div>

        <div class="sample-data">
            <strong>TUGAS:</strong><br>
            Dengan data yang sudah ada:<br>
            Buat 1 page untuk listing data2 di table "person" berikut hobinya masing-masing yg barada di tabel "hobi", misal:
            <table style="border-collapse: collapse; margin-top: 10px;">
                <tr style="border: 1px solid #333;">
                    <th style="border: 1px solid #333; padding: 8px; background-color: #f0f0f0;">Nama</th>
                    <th style="border: 1px solid #333; padding: 8px; background-color: #f0f0f0;">Alamat</th>
                    <th style="border: 1px solid #333; padding: 8px; background-color: #f0f0f0;">Hobi</th>
                </tr>
                <tr style="border: 1px solid #333;">
                    <td style="border: 1px solid #333; padding: 8px;">Sentot</td>
                    <td style="border: 1px solid #333; padding: 8px;">Aaa 11</td>
                    <td style="border: 1px solid #333; padding: 8px;">Futsal</td>
                </tr>
                <tr style="border: 1px solid #333;">
                    <td style="border: 1px solid #333; padding: 8px;">Anna</td>
                    <td style="border: 1px solid #333; padding: 8px;">Bbb 21</td>
                    <td style="border: 1px solid #333; padding: 8px;">Basket</td>
                </tr>
            </table>
        </div>

        <p><strong>Dibawah tampilan tsb buat fasilitas form "search" nama/alamat/hobi sbb:</strong></p>
        <p><em>Catatan: Pencarian hobi akan mencari berdasarkan hobi yang dimiliki oleh person tersebut.</em></p>

        <div class="search-form">
            <form method="POST" action="">
                <div class="form-row">
                    <label for="nama">Nama :</label>
                    <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($_POST['nama'] ?? ''); ?>">
                </div>
                <div class="form-row">
                    <label for="alamat">Alamat :</label>
                    <input type="text" id="alamat" name="alamat" value="<?php echo htmlspecialchars($_POST['alamat'] ?? ''); ?>">
                </div>
                <div class="form-row">
                    <label for="hobi">Hobi :</label>
                    <input type="text" id="hobi" name="hobi" value="<?php echo htmlspecialchars($_POST['hobi'] ?? ''); ?>">
                </div>
                <input type="submit" value="SEARCH" class="search-btn">
            </form>
        </div>

        <p><strong>Ditekan tombol SEARCH, maka list tabel diatas akan berisi hasil pencarian tsb.</strong></p>

        <?php if ($search_performed): ?>
            <h3>Hasil Pencarian:</h3>
            <?php if (count($search_results) > 0): ?>
                <table class="results-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Alamat</th>
                            <th>Hobi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($search_results as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                <td><?php echo htmlspecialchars($row['alamat']); ?></td>
                                <td><?php echo htmlspecialchars($row['hobi_list'] ?? 'Tidak ada hobi'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-results">
                    Tidak ada data yang ditemukan dengan kriteria pencarian tersebut.
                </div>
            <?php endif; ?>
        <?php else: ?>
            <h3>Semua Data Person & Hobi:</h3>
            <?php
            // Show all data when no search is performed
            $sql = "SELECT DISTINCT p.id, p.nama, p.alamat,
                           GROUP_CONCAT(h.hobi SEPARATOR ', ') as hobi_list
                    FROM person p
                    LEFT JOIN hobi h ON p.id = h.person_id
                    GROUP BY p.id, p.nama, p.alamat
                    ORDER BY p.nama
                    LIMIT 10";

            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $all_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <table class="results-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>Hobi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_results as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['nama']); ?></td>
                            <td><?php echo htmlspecialchars($row['alamat']); ?></td>
                            <td><?php echo htmlspecialchars($row['hobi_list'] ?? 'Tidak ada hobi'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p><em>Menampilkan 10 data pertama. Gunakan form pencarian untuk mencari data spesifik.</em></p>
        <?php endif; ?>
    </div>
</body>
</html>