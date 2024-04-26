<?php
require 'header.php';
require 'connect.php'; 

$book_id = $_GET['book_id'] ?? ''; 

if (!$book_id) {
    echo "<p>No book selected.</p>";
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM Books WHERE book_id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$book) {
        echo "<p>Book not found.</p>";
        exit;
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    exit;
}
?>

<div class="container mt-5">
    <h1><?php echo htmlspecialchars($book['title']); ?></h1>
    <div class="row">
        <div class="col-md-6">
            <img src="./admin/<?php echo htmlspecialchars($book['image_url']); ?>" class="img-fluid" alt="Cover Image">
        </div>
        <div class="col-md-6">
            <h3>Description</h3>
            <p><?php echo htmlspecialchars($book['description']); ?></p>
            <h4>Author</h4>
            <p><?php echo htmlspecialchars($book['author']); ?></p>
            <h4>Price</h4>
            <p>$<?php echo htmlspecialchars(number_format($book['price'], 2)); ?></p>
        </div>
    </div>
</div>
<div class="container mt-3">
    <h2>Comments:</h2>
    <?php
    $stmt = $pdo->prepare("SELECT * FROM Comments WHERE bookId = ? ORDER BY created_at DESC");
    $stmt->execute([$book_id]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($comments) {
        foreach ($comments as $comment) {
            echo "<div class='alert alert-secondary'>";
            echo "<p>" . htmlspecialchars($comment['comment']) . "</p>";
            echo "<small>By " . htmlspecialchars($comment['userName']) . " on " . $comment['created_at'] . "</small>";
            echo "</div>";
        }
    } else {
        echo "<p>No comments yet.</p>";
    }
    ?>
</div>
<div class="container mt-3">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Leave a comment</h5>
            <form action="post_comment.php" method="post">
                <input type="hidden" name="bookId" value="<?php echo $book_id; ?>">
                <div class="mb-3">
                    <label for="userName" class="form-label">Your Name:</label>
                    <input type="text" class="form-control" id="userName" name="userName" required>
                </div>
                <div class="mb-3">
                    <label for="comment" class="form-label">Comment:</label>
                    <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit Comment</button>
            </form>
        </div>
    </div>
</div>


<?php require 'footer.php'; ?>
