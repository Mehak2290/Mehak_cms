<?php
session_start();
require '../connect.php'; 


$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if ($username && $password) {
    $sql = "SELECT * FROM Admin WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['username'] = $admin['username'];
        header('Location: index.php');
        exit();
    } else {
        $_SESSION['error'] = 'Invalid login credentials.';
        header('Location: login.php');
        exit();
    }
} else {
    $_SESSION['error'] = 'Please enter username and password.';
    header('Location: login.php');
    exit();
}
