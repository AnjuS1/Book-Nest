<?php
    session_start();
    require '../includes/db_connect.php';

    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
        die("Access denied.");
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $category_name = trim($_POST['category_name']);
        if ($category_name == '') {
            $_SESSION['flash'] = "Category name cannot be empty.";
            header("Location: add_category.php");
            exit;
        }
        try {
            $stmt = $pdo->prepare("INSERT INTO categories (category_name) VALUES (:name)");
            $stmt->execute([':name' => $category_name]);

            $_SESSION['flash'] = "Category added successfully!";
        } catch (PDOException $e) {
            $_SESSION['flash'] = "Error: " . $e->getMessage();
        }
        header("Location: add_category.php");
        exit;
    }

    $msg = $_SESSION['flash'] ?? '';
    unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Add Category</title>
        <link rel="stylesheet" href="../adminstyle.css">
    </head>
    <body class="add-category">
        <a href="dashboard.php" class="link">Back to Dashboard</a>
        <div class="add-category-container">
            <h2>Add New Category</h2>
            <?php if ($msg): ?>
                <p class="add-category-msg"><?= htmlspecialchars($msg) ?></p>
            <?php endif; ?>
            <form method="post">
                <label>Category Name:</label>
                <input type="text" name="category_name" class="add-category-input" required>
                <button type="submit" name="add" class="add-category-button">Add Category</button>
            </form>
        </div>
    </body>
</html>
