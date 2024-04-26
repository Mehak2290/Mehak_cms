<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    $_SESSION['error'] = 'You must be logged in to view this page.';
    header('Location: login.php');
    exit();
}
require '../connect.php';
require 'header.php'; 



if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_id'])) {
    $book_id = $_POST['book_id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $remove_image = isset($_POST['remove_image']);  // Check if the remove image checkbox was checked

    $image_path = '';

    // Handle file upload
    if (!$remove_image && isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        $image_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;

        if (getimagesize($_FILES["image"]["tmp_name"]) !== false) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_path = $target_file;
            } else {
                $_SESSION['error'] = 'Error uploading file.';
            }
        } else {
            $_SESSION['error'] = 'File is not an image.';
        }
    }

    // Prepare SQL query
    $sql = "UPDATE Books SET title = ?, author = ?, price = ?, description = ?";
    if (!empty($image_path)) {
        $sql .= ", image_url = ?";
    } elseif ($remove_image) {
        $sql .= ", image_url = NULL";
    }
    $sql .= " WHERE book_id = ?";

    $stmt = $pdo->prepare($sql);
    $params = [$title, $author, $price, $description];
    if (!empty($image_path)) {
        $params[] = $image_path;
    }
    $params[] = $book_id;

    try {
        $stmt->execute($params);
        if ($remove_image && !empty($book['image_url'])) {
            // Delete the image file from the server
            unlink($book['image_url']);
        }
        $_SESSION['message'] = 'Book updated successfully.';
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Error updating book: ' . $e->getMessage();
    }
    header("Location: index.php");
    exit();
}

if (isset($_GET['book_id'])) {
    $book_id = $_GET['book_id'];
    $sql = "SELECT * FROM Books WHERE book_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$book_id]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$book) {
        $_SESSION['error'] = "No book found with ID $book_id";
        header("Location: index.php");
        exit;
    }
} else {
    $_SESSION['error'] = 'No book ID provided.';
    header("Location: index.php");
    exit;
}
?>

<div class="container mt-5">
    <h2>Edit Book</h2>
    <form action="edit_book.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['book_id']); ?>">

        <div class="mb-3">
            <label for="title" class="form-label">Title:</label>
            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($book['title']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="author" class="form-label">Author:</label>
            <input type="text" class="form-control" id="author" name="author" value="<?php echo htmlspecialchars($book['author']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Price:</label>
            <input type="text" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($book['price']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description:</label>
            <textarea class="form-control" id="description" name="description" required><?php echo htmlspecialchars($book['description']); ?></textarea>
        </div>
        <div class="mb-3">
    <label for="image" class="form-label">Book Cover Image:</label>
    <input type="file" class="form-control" id="image" name="image">
    <?php if (!empty($book['image_url'])): ?>
        <img src="<?php echo htmlspecialchars($book['image_url']); ?>" alt="Current Cover" style="width:100px; height:auto;">
        <p>Current Cover</p>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="remove_image" name="remove_image">
            <label class="form-check-label" for="remove_image">
                Remove current cover image
            </label>
        </div>
    <?php else: ?>
        <p>No cover available.</p>
    <?php endif; ?>

</div>

        <button type="submit" class="btn btn-primary">Update Book</button>
    </form>
</div>

<?
require './footer.php'
?>
