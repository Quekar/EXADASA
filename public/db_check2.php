<?php
$pdo = new PDO('mysql:host=localhost;dbname=db_exadasa', 'root', '');
$tables = ['siswa', 'users'];
foreach($tables as $t) {
    echo "TABLE: $t\n";
    $stmt = $pdo->query("DESCRIBE $t");
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
    echo "------------------\n";
}
