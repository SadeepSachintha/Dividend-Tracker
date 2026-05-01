<?php
$host = '127.0.0.1';
$db   = 'dividend_tracker';
$user = 'root';
$pass = ''; // Try XAMPP default first
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     echo "Connected successfully to local MySQL\n";
     $stmt = $pdo->query('SHOW TABLES');
     while ($row = $stmt->fetch()) {
         print_r($row);
     }
} catch (\PDOException $e) {
     echo "Failed to connect to local MySQL: " . $e->getMessage() . "\n";
     
     // Try Docker config
     $host = '127.0.0.1';
     $user = 'root';
     $pass = 'secret';
     $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
     try {
         $pdo = new PDO($dsn, $user, $pass, $options);
         echo "Connected successfully to Docker MySQL (localhost)\n";
         $stmt = $pdo->query('SHOW TABLES');
         while ($row = $stmt->fetch()) {
             print_r($row);
         }
     } catch (\PDOException $e) {
         echo "Failed to connect to Docker MySQL: " . $e->getMessage() . "\n";
     }
}
?>
