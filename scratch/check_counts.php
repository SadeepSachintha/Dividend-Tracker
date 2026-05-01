<?php
$host = '127.0.0.1';
$db   = 'dividend_tracker';
$user = 'root';
$pass = ''; // Default XAMPP
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     $count = $pdo->query('SELECT COUNT(*) FROM holdings')->fetchColumn();
     echo "Holdings count: $count\n";
     
     $div_count = $pdo->query('SELECT COUNT(*) FROM dividends')->fetchColumn();
     echo "Dividends count: $div_count\n";
} catch (\Exception $e) {
     echo "Error: " . $e->getMessage() . "\n";
     
     // Try secret password
     $pass = 'secret';
     try {
         $pdo = new PDO($dsn, $user, $pass, $options);
         $count = $pdo->query('SELECT COUNT(*) FROM holdings')->fetchColumn();
         echo "Holdings count (with secret): $count\n";
         $div_count = $pdo->query('SELECT COUNT(*) FROM dividends')->fetchColumn();
         echo "Dividends count (with secret): $div_count\n";
     } catch (\Exception $e) {
         echo "Error with secret: " . $e->getMessage() . "\n";
     }
}
?>
