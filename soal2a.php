<?php
session_start();

// Initialize session data if not exists
if (!isset($_SESSION['form_data'])) {
    $_SESSION['form_data'] = array();
}

// Handle form submission
if ($_POST) {
    $step = isset($_POST['step']) ? (int)$_POST['step'] : 1;
    
    switch ($step) {
        case 1:
            $_SESSION['form_data']['nama'] = $_POST['nama'] ?? '';
            $_SESSION['current_step'] = 2;
            break;
        case 2:
            $_SESSION['form_data']['umur'] = $_POST['umur'] ?? '';
            $_SESSION['current_step'] = 3;
            break;
        case 3:
            $_SESSION['form_data']['hobi'] = $_POST['hobi'] ?? '';
            $_SESSION['current_step'] = 4;
            break;
    }
}

// Get current step
$current_step = isset($_SESSION['current_step']) ? $_SESSION['current_step'] : 1;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Form Multi-Step</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-box {
            border: 2px solid #333;
            padding: 20px;
            margin: 20px 0;
            background-color: #fff;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: inline-block;
            width: 100px;
            font-weight: bold;
        }
        input[type="text"], input[type="number"] {
            padding: 8px;
            border: 2px solid #333;
            width: 150px;
        }
        .submit-btn {
            background-color: #f0f0f0;
            border: 2px solid #333;
            padding: 10px 20px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 15px;
        }
        .submit-btn:hover {
            background-color: #e0e0e0;
        }
        .step-info {
            margin-bottom: 20px;
            font-weight: bold;
        }
        .final-result {
            border: 2px solid #333;
            padding: 15px;
            background-color: #f9f9f9;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($current_step == 1): ?>
            <div class="form-box">
                <form method="POST">
                    <div class="form-group">
                        <label>Nama Anda :</label>
                        <input type="text" name="nama" required>
                    </div>
                    <input type="hidden" name="step" value="1">
                    <input type="submit" value="SUBMIT" class="submit-btn">
                </form>
            </div>

        <?php elseif ($current_step == 2): ?>
            <div class="form-box">
                <form method="POST">
                    <div class="form-group">
                        <label>Umur Anda :</label>
                        <input type="number" name="umur" required>
                    </div>
                    <input type="hidden" name="step" value="2">
                    <input type="submit" value="SUBMIT" class="submit-btn">
                </form>
            </div>

        <?php elseif ($current_step == 3): ?>
            <div class="form-box">
                <form method="POST">
                    <div class="form-group">
                        <label>Hobi Anda :</label>
                        <input type="text" name="hobi" required>
                    </div>
                    <input type="hidden" name="step" value="3">
                    <input type="submit" value="SUBMIT" class="submit-btn">
                </form>
            </div>

        <?php elseif ($current_step == 4): ?>
            <div class="final-result">
                <div><strong>Nama:</strong> <?php echo htmlspecialchars($_SESSION['form_data']['nama']); ?></div>
                <div><strong>Umur:</strong> <?php echo htmlspecialchars($_SESSION['form_data']['umur']); ?></div>
                <div><strong>Hobi:</strong> <?php echo htmlspecialchars($_SESSION['form_data']['hobi']); ?></div>
            </div>
            <br>
            <a href="?reset=1" style="text-decoration: none;">
                <button class="submit-btn">Mulai Ulang</button>
            </a>
        <?php endif; ?>
    </div>

    <?php
    // Reset form if requested
    if (isset($_GET['reset'])) {
        session_destroy();
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
    ?>
</body>
</html>
