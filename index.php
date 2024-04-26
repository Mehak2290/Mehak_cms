<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require 'header.php'; 
require 'connect.php';

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$queryParts = [];
$params = [];

if (!empty($search)) {
    $queryParts[] = "(title LIKE :search OR description LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

if (!empty($category)) {
    $queryParts[] = "category_id = :category";
    $params[':category'] = $category;
}

$queryStr = "SELECT * FROM Books";
if ($queryParts) {
    $queryStr .= " WHERE " . implode(' AND ', $queryParts);
}

try {
    $stmt = $pdo->prepare($queryStr);
    $stmt->execute($params);
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Connection or query failed: " . $e->getMessage();
    exit;
}
?>
<div class="container mt-3">
    <div class="row">
        <div class="col-md-12">
            <form action="" method="GET" class="form-inline">
                <input type="text" name="search" class="form-control mr-sm-2" placeholder="Search by title or description" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" />
                <select name="category" class="form-control mr-sm-2">
                    <option value="">All Categories</option>
                    <?php
                    $catStmt = $pdo->query("SELECT * FROM Categories");
                    $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($categories as $category) {
                        echo "<option value=\"" . htmlspecialchars($category['category_id']) . "\"" . 
                             (isset($_GET['category']) && $_GET['category'] == $category['category_id'] ? ' selected' : '') .
                             ">" . htmlspecialchars($category['category_name']) . "</option>";
                    }
                    ?>
                </select>
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>
    </div>
</div>
<div class="container mt-5">
    <h1 class="mb-4 text-center">Available Books</h1>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
        <?php foreach ($books as $book): ?>
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <a href="view.php?book_id=<?php echo $book['book_id']; ?>"> <!-- Link to the view page -->
                        <img src="./admin/<?php echo !empty($book['image_url']) ? htmlspecialchars($book['image_url']) : 'https://via.placeholder.com/150'; ?>" class="card-img-top" alt="Cover Image" style="height: 300px; object-fit: cover;">
                    </a>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                        <p class="card-text"><a href="view.php?book_id=<?php echo $book['book_id']; ?>" class="stretched-link"></a></p>
                    </div>
                    <div class="card-footer bg-transparent border-top-0">
                        <small class="text-muted">Author: <?php echo htmlspecialchars($book['author']); ?></small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>


<?php require 'footer.php'; ?>
