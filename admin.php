<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    $_SESSION['error'] = 'You must be logged in to view this page.';
    header('Location: login.php');
    exit();
}
require 'header.php';
require '../connect.php';
session_start();

// Handle POST request for adding a new admin
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addAdmin'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];

    $sql = "INSERT INTO Admin (username, password, email) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([$username, $password, $email]);
        $_SESSION['message'] = "Admin successfully added.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error adding admin: " . $e->getMessage();
    }
    header("Location: admin.php");
    exit();
}

// Handle deletion of an admin
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_admin_id'])) {
    $admin_id = $_POST['delete_admin_id'];

    $sql = "DELETE FROM Admin WHERE admin_id = ?";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([$admin_id]);
        $_SESSION['message'] = "Admin deleted successfully.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error deleting admin: " . $e->getMessage();
    }
    header("Location: admin.php");
    exit();
}

?>

<div class="container mt-5">
    <h2>Manage Admins</h2>
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
            <h4>Current Admins</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>Admin ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $sql = "SELECT * FROM Admin";
                        $stmt = $pdo->query($sql);
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['admin_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                            echo "<td>
                                <a href='edit_admin.php?admin_id=" . $row['admin_id'] . "' class='btn btn-primary'>Edit</a>
                                <form action='admin.php' method='POST' style='display:inline-block;'>
                                    <input type='hidden' name='delete_admin_id' value='" . $row['admin_id'] . "'>
                                    <button type='submit' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this admin?\")'>Delete</button>
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
            <h4>Add New Admin</h4>
            <form action="admin.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username:</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <button type="submit" name="addAdmin" class="btn btn-primary">Add Admin</button>
            </form>
        </div>
    </div>
</div>

<?php require 'footer.php'; ?>
