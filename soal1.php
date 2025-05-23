<!DOCTYPE html>
<html>
<head>
    <style>
        table {
            border-collapse: collapse;
            margin: 20px;
            border: 3px solid black;
        }
        td {
            border: 2px solid black;
            padding: 10px 15px;
            text-align: center;
            font-family: Arial, sans-serif;
            font-size: 16px;
            font-weight: bold;
            width: 50px;
            height: 35px;
            vertical-align: middle;
        }
        .total-row {
            background-color: white;
            font-weight: bold;
            text-align: left;
            padding-left: 15px;
        }
    </style>
</head>
<body>

<?php

$jml = isset($_GET['jml']) ? (int)$_GET['jml'] : 0;

if ($jml > 0) {
    echo "<table>\n";
    for ($a = $jml; $a > 0; $a--)
    {
      // Hitung total untuk row ini
      $total = 0;
      for ($i = $a; $i > 0; $i--) {
        $total += $i;
      }

      // Tampilkan row TOTAL - selalu menggunakan colspan untuk lebar penuh
      echo "<tr>\n";
      echo "<td colspan='$jml' class='total-row'>TOTAL: $total</td>";
      echo "</tr>\n";

      // Tampilkan row angka
      echo "<tr>\n";
      for ($b = $a; $b > 0; $b--)
      {
        echo "<td>$b</td>";
      }
      // Tambahkan cell kosong jika diperlukan untuk membuat semua row sama lebar
      $emptyCells = $jml - $a;
      for ($c = 0; $c < $emptyCells; $c++) {
        echo "<td style='border: none;'></td>";
      }
      echo "</tr>\n";
    }
    echo "</table>";
} else {
    echo "<p>Parameter 'jml' harus berupa angka positif. Contoh: soal1.php?jml=4</p>";
}

?>

</body>
</html>