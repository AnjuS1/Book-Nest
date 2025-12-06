<?php
    session_start();
    require '../includes/db_connect.php';

    if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){ 
        die("Access denied."); 
    }
    if(!isset($_GET['id'])) {
        $_SESSION['msg'] = "Category ID missing.";
        header("Location: categories.php");
        exit();
    }
    $cat_id = (int)$_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM categories WHERE category_id=:id");
    $stmt->execute([':id'=>$cat_id]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$category){
        $_SESSION['msg'] = "Category not found.";
        header("Location: categories.php");
        exit();
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
        $name = trim($_POST['category_name']);

        if (empty($name)) {
            $_SESSION['msg'] = "Category name cannot be empty.";
            $_SESSION['old_input'] = ['category_name' => $name];
            header("Location: edit_category.php?id=$cat_id");
            exit();
        }
        try {
            $stmt = $pdo->prepare("UPDATE categories SET category_name=:name WHERE category_id=:id");
            $stmt->execute([':name'=>$name, ':id'=>$cat_id]);
            $_SESSION['msg'] = "Category updated successfully!";
            header("Location: edit_category.php?id=$cat_id");
            exit();
        } catch (PDOException $e) {
            $_SESSION['msg'] = "Error: " . $e->getMessage();
            $_SESSION['old_input'] = ['category_name' => $name];
            header("Location: edit_category.php?id=$cat_id");
            exit();
        }
    }
    $msg = $_SESSION['msg'] ?? '';
    unset($_SESSION['msg']);

    $old_input = $_SESSION['old_input'] ?? [];
    unset($_SESSION['old_input']);
    $category_name_value = $old_input['category_name'] ?? $category['category_name'];
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Category</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="../adminstyle.css" rel="stylesheet">
    </head>
    <body class="admin-page">

    <div class="container">
        <div class="card admin-card-category p-4">
            <h2 class="mb-4 text-center">Edit Category</h2>
            <?php if ($msg): ?>
                <div class="alert <?= strpos($msg,'Error')===0 ? 'alert-danger' : 'alert-success' ?>">
                    <?= htmlspecialchars($msg) ?>
                </div>
            <?php endif; ?>
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Category Name:</label>
                    <input type="text" name="category_name" value="<?= htmlspecialchars($category_name_value); ?>" class="form-control" required>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="categories.php" class="btn btn-secondary">Back</a>
                    <input type="submit" name="update" value="Update Category" class="btn btn-primary">
                </div>
            </form>
        </div>
    </div>
    </body>
</html>
