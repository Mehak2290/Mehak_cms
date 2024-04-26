<?php
$host = 'localhost'; 
$dbname = 'cms'; 
$username = 'root'; 
$password = ''; 

try {
    // Create a PDO instance as db connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
