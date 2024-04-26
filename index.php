<?php 
session_start();

if (!isset($_SESSION['admin_id'])) {
    $_SESSION['error'] = 'You must be logged in to view this page.';
    header('Location: login.php');
    exit();
}

require 'header.php'; 
require '../connect.php';

// Capture sorting parameters
$sortField = $_GET['sort'] ?? 'title'; // Default sort field
$sortOrder = $_GET['order'] ?? 'asc'; // Default sort order

// Toggle the sort order
$nextOrder = $sortOrder === 'asc' ? 'desc' : 'asc';

// Handle book deletion
if (isset($_GET['book_id'])) {
    $book_id = $_GET['book_id'];
    $sql = "DELETE FROM Books WHERE book_id = ?";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([$book_id]);
        $_SESSION['message'] = 'Book deleted successfully.';
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Error deleting book: ' . $e->getMessage();
    }
}

// Fetch books with sorting
$sql = "SELECT Books.book_id, Books.title, Books.author, Books.price, Books.description, Books.image_url, Categories.category_name 
        FROM Books 
        JOIN Categories ON Books.category_id = Categories.category_id
        ORDER BY $sortField $sortOrder";
$stmt = $pdo->query($sql);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container mt-5">


    <h2>Manage Books</h2>
    <div class="btn-group" role="group">
                <a href="?sort=title&order=<?= $nextOrder ?>" class="btn btn-primary">Sort by Title</a>
                <a href="?sort=price&order=<?= $nextOrder ?>" class="btn btn-secondary">Sort by Price</a>
                <a href="?sort=category_name&order=<?= $nextOrder ?>" class="btn btn-success">Sort by Category</a>
            </div>
    <div class="row">
        <div class="col-md-6">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Price</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Image</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($books as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['book_id']) ?></td>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= htmlspecialchars($row['author']) ?></td>
                            <td>$<?= number_format($row['price'], 2) ?></td>
                            <td><?= htmlspecialchars($row['category_name']) ?></td>
                            <td><?= htmlspecialchars($row['description']) ?></td>
                            <td>
                                <?php if (!empty($row['image_url'])): ?>
                                <img src="<?= htmlspecialchars($row['image_url']) ?>" alt="Book Cover" style="width:100px; height:auto;">
                                <?php else: ?>
                                No image available
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="edit_book.php?book_id=<?= $row['book_id'] ?>" class='btn btn-primary'>Edit</a>
                                <a href="?book_id=<?= $row['book_id'] ?>&sort=<?= $sortField ?>&order=<?= $sortOrder ?>" onclick="return confirm('Are you sure you want to delete this book?')" class='btn btn-danger'>Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    
                </table>
                
            </div>
         
        </div>

        <div class="col-md-6">
                <h4>Add New Book</h4>
                <form action="add_book.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title:</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="author" class="form-label">Author:</label>
                        <input type="text" class="form-control" id="author" name="author" required>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price:</label>
                        <input type="text" class="form-control" id="price" name="price" required>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Category:</label>
                        <select class="form-select" id="category" name="category">
                            <?php
                            $sql = "SELECT category_id, category_name FROM Categories";
                            $stmt = $pdo->query($sql);
                            while ($category = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='" . $category['category_id'] . "'>" . htmlspecialchars($category['category_name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description:</label>
                        <textarea class="form-control" id="description" name="description" required></textarea>
                    </div>
                    <div class="mb-3">
    <label for="image" class="form-label">Book Cover Image:</label>
    <input type="file" class="form-control" id="image" name="image" >
</div>
                    <button type="submit" class="btn btn-primary">Add Book</button>
                </form>
            </div>
        
    </div>

</div>
<?php require 'footer.php'; ?>
