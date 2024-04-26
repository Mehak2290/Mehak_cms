<?php 
session_start();

if (!isset($_SESSION['admin_id'])) {
    $_SESSION['error'] = 'You must be logged in to view this page.';
    header('Location: login.php');
    exit();
}
require 'header.php'; 
require '../connect.php';
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['categoryName'])) {
    $categoryName = $_POST['categoryName'];

    $sql = "INSERT INTO Categories (category_name) VALUES (?)";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([$categoryName]);
        $_SESSION['message'] = "Category successfully added.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error adding category: " . $e->getMessage();
    }
    header("Location: categories.php"); 
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_category_id'])) {
    $category_id = $_POST['delete_category_id'];

    $sql = "DELETE FROM Categories WHERE category_id = ?";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([$category_id]);
        $_SESSION['message'] = "Category deleted successfully.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error deleting category: " . $e->getMessage();
    }
    header("Location: categories.php");
    exit();
}
?>

<div class="container mt-5">
    <h2>Manage Categories</h2>
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
    <div class="row">
        <div class="col-md-8">
            <h4>Current Categories</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>Category ID</th>
                        <th>Category Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $sql = "SELECT * FROM Categories";
                        $stmt = $pdo->query($sql);
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['category_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['category_name']) . "</td>";
                            echo "<td>
                            <a href='edit_category.php?category_id=" . $row['category_id'] . "' class='btn btn-primary'>Edit</a>
                                    <form action='categories.php' method='POST'>
                                        <input type='hidden' name='delete_category_id' value='" . $row['category_id'] . "'>
                                        <button type='submit' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this category?\")'>Delete</button>
                                    </form>
                                  </td>";
                            echo "</tr>";
                        }
                    } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="col-md-4">
            <h4>Add New Category</h4>
            <form action="categories.php" method="POST">
                <div class="mb-3">
                    <label for="categoryName" class="form-label">Category Name:</label>
                    <input type="text" class="form-control" id="categoryName" name="categoryName" required>
                </div>
                <button type="submit" class="btn btn-primary">Add Category</button>
            </form>
        </div>
    </div>
</div>
    <?
require './footer.php'
?>
