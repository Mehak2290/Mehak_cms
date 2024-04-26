<?php 
session_start();

if (!isset($_SESSION['admin_id'])) {
    $_SESSION['error'] = 'You must be logged in to view this page.';
    header('Location: login.php');
    exit();
}
require 'header.php'; 
require '../connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['comment']) && !empty($_POST['bookId']) && !empty($_POST['userName'])) {
    $comment = $_POST['comment'];
    $bookId = $_POST['bookId'];
    $userName = $_POST['userName'];

    $sql = "INSERT INTO Comments (comment, bookId, userName) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([$comment, $bookId, $userName]);
        $_SESSION['message'] = "Comment successfully added.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error adding comment: " . $e->getMessage();
    }
    header("Location: comments.php"); 
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_comment_id'])) {
    $comment_id = $_POST['delete_comment_id'];

    $sql = "DELETE FROM Comments WHERE id = ?";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([$comment_id]);
        $_SESSION['message'] = "Comment deleted successfully.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error deleting comment: " . $e->getMessage();
    }
    header("Location: comments.php");
    exit();
}
?>

<div class="container mt-5">
    <h2>Manage Comments</h2>
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
        <div class="col-md-12">
            <h4>Current Comments</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Comment</th>
                        <th>Book ID</th>
                        <th>User Name</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $sql = "SELECT * FROM Comments";
                        $stmt = $pdo->query($sql);
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['comment']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['bookId']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['userName']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                            echo "<td>
                                    <form action='comments.php' method='POST'>
                                        <input type='hidden' name='delete_comment_id' value='" . $row['id'] . "'>
                                        <button type='submit' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this comment?\")'>Delete</button>
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
    </div>
</div>

<?php require './footer.php'; ?>
