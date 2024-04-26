<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    $_SESSION['error'] = 'You must be logged in to view this page.';
    header('Location: login.php');
    exit();
}
require '../connect.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $price = $_POST['price'];
    $category_id = $_POST['category'];
    $description = $_POST['description'];
    $image_path = "";
    
    if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = $_FILES['image']['name'];
        $target_dir = "uploads/"; 
        $target_file = $target_dir . basename($image);
    
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = $target_file;
        } else {
            echo 'Error during file upload.';
            exit; 
        }
    } else {
        echo 'Upload error code: ' . $_FILES['image']['error'];
        exit; 
    }

    $sql = "INSERT INTO Books (title, author, price, description, category_id, image_url) VALUES (?, ?, ?, ?, ?, ?)";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(1, $title);
        $stmt->bindParam(2, $author);
        $stmt->bindParam(3, $price);
        $stmt->bindParam(4, $description);
        $stmt->bindParam(5, $category_id);
        $stmt->bindParam(6, $image_path);
        if ($stmt->execute()) {
            echo "Book added successfully.";
        } else {
            echo "Failed to add the book.";
        }
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        die("Error adding new book: " . $e->getMessage());
    }
} else {
    header("Location: index.php");
    exit();
}
?>
