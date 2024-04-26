<?php
session_start();
require 'connect.php';


$bookId = $_POST['bookId'] ?? '';
$userName = $_POST['userName'] ?? '';
$comment = $_POST['comment'] ?? '';

if (!$bookId || !$userName || !$comment) {
    $_SESSION['message'] = 'All fields are required.';
    header("Location: view.php?book_id=$bookId");
    exit;
}

try {
    $sql = "INSERT INTO Comments (comment, bookId, userName) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$comment, $bookId, $userName]);
    $_SESSION['message'] = 'Comment added successfully.';
} catch (PDOException $e) {
    $_SESSION['message'] = "Database error: " . $e->getMessage();
}
header("Location: view.php?book_id=$bookId");
exit;
?>
