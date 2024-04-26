<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    $_SESSION['error'] = 'You must be logged in to view this page.';
    header('Location: login.php');
    exit();
}
require 'header.php';
require '../connect.php';


$category_id = $_GET['category_id'] ?? null;
if (!$category_id) {
    $_SESSION['error'] = "No category ID provided.";
    header("Location: categories.php");
    exit();
}

// Fetch category details
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $stmt = $pdo->prepare("SELECT * FROM Categories WHERE category_id = ?");
    $stmt->execute([$category_id]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Handle POST request to update the category
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['categoryName'])) {
    $categoryName = $_POST['categoryName'];

    $sql = "UPDATE Categories SET category_name = ? WHERE category_id = ?";
    $stmt = $pdo->prepare($sql);
    try {
        $stmt->execute([$categoryName, $category_id]);
        $_SESSION['message'] = "Category successfully updated.";
        header("Location: categories.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error updating category: " . $e->getMessage();
    }
}

?>

<div class="container mt-5">
    <h2>Edit Category</h2>
    <?php
    if (isset($_SESSION['message'])) {
        echo '<div class="alert alert-success">' . $_SESSION['message'] . '</div>';
        unset($_SESSION['message']);
    }
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
        unset($_SESSION['error']);
    }
    ?>
    <form action="edit_category.php?category_id=<?php echo $category_id; ?>" method="POST">
        <div class="mb-3">
            <label for="categoryName" class="form-label">Category Name:</label>
            <input type="text" class="form-control" id="categoryName" name="categoryName" required value="<?php echo htmlspecialchars($category['category_name']); ?>">
        </div>
        <button type="submit" class="btn btn-primary">Update Category</button>
    </form>
</div>

<?php require 'footer.php'; ?>
