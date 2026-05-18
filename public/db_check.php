<?php
$pdo = new PDO('mysql:host=localhost;dbname=db_exadasa', 'root', '');
$tables = ['data_siswa', 'jawaban_siswa', 'ujian_soal', 'bank_soal', 'ujian'];
foreach($tables as $t) {
    echo "TABLE: $t\n";
    $stmt = $pdo->query("DESCRIBE $t");
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
    echo "------------------\n";
}
