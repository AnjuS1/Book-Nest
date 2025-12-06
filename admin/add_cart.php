<?php
    session_start();
    require '../includes/db_connect.php';

    if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
        die("Access denied.");
    }
    $input = ['category_name' => '', 'description' => ''];
    $msg = '';
    $alertClass = 'alert-danger';
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
        $input['category_name'] = trim($_POST['category_name'] ?? '');
        $input['description']   = trim($_POST['description'] ?? '');
        if(empty($input['category_name'])){
            $msg = "Category name is required.";
        } else {
            // Insert into DB
            try {
                $stmt = $pdo->prepare("INSERT INTO categories (category_name, description) 
                                    VALUES (:name, :desc)");
                $stmt->execute([
                    ':name' => $input['category_name'],
                    ':desc' => $input['description']
                ]);
                $_SESSION['success'] = "Category added successfully.";
                header("Location: add_category.php"); 
                exit;
            } catch(PDOException $e){
                $msg = "Database Error: " . $e->getMessage();
            }
        }
    }

    if(isset($_SESSION['success'])){
        $msg = $_SESSION['success'];
        $alertClass = 'alert-success';
        unset($_SESSION['success']);
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Add Category</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="p-4 bg-light">
    <div class="container" style="max-width: 550px;">
        <div class="card p-4 shadow-sm">
            <h2 class="text-center mb-3">Add Category</h2>
            <?php if($msg): ?>
                <div class="alert <?= $alertClass ?>">
                    <?= htmlspecialchars($msg) ?>
                </div>
            <?php endif; ?>
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Category Name:</label>
                    <input type="text" name="category_name" class="form-control"
                        required value="<?= htmlspecialchars($input['category_name']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Description:</label>
                    <input type="text" name="description" class="form-control"
                        value="<?= htmlspecialchars($input['description']) ?>">
                </div>
                <div class="d-flex justify-content-between">
                    <a href="categories_list.php" class="btn btn-secondary">Back</a>
                    <input type="submit" name="add" value="Add Category" class="btn btn-primary">
                </div>
            </form>
        </div>
    </div>
    </body>
</html>
