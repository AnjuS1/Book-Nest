<?php
session_start();
require '../includes/db_connect.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    die("Access denied.");
}

$old = $_SESSION['old'] ?? [];
$errors = $_SESSION['errors'] ?? [];
$msg = $_SESSION['msg'] ?? '';

unset($_SESSION['old'], $_SESSION['errors'], $_SESSION['msg']);

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])){
    $category_name = trim($_POST['category_name']);

    if($category_name === ''){
        $_SESSION['errors'] = ['Category name cannot be empty.'];
        $_SESSION['old'] = $_POST;
        header("Location: categories.php");
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO categories (category_name) VALUES (:name)");
    try{
        $stmt->execute([':name' => $category_name]);
        $_SESSION['msg'] = "Category added successfully!";
        header("Location: categories.php");
        exit;
    } catch(PDOException $e){
        $_SESSION['errors'] = ["Database error: ".$e->getMessage()];
        $_SESSION['old'] = $_POST;
        header("Location: categories.php");
        exit;
    }
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY category_name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Categories</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
        <link href="../adminstyle.css" rel="stylesheet"/>
    </head>
    <body class="p-4 bg-light">

    <div class="container">
        <h2 class="mb-3">Categories List</h2>

        <div class="d-flex mb-3 align-items-center">
            <button class="btn btn-success me-3" data-bs-toggle="modal" data-bs-target="#addCategoryModal">Add Category</button>
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            <input type="text" id="searchInput" class="form-control w-25 ms-auto" placeholder="Search categories">
        </div>
        <?php if(!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php foreach($errors as $e) echo htmlspecialchars($e)."<br>"; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif($msg): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= htmlspecialchars($msg) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <table id="categoriesTable" class="table table-bordered">
            <thead class="table-primary">
                <tr>
                    <th style="width:70%;">Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($categories as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['category_name']); ?></td>
                    <td>
                        <a href="edit_category.php?id=<?= $c['category_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete_category.php?id=<?= $c['category_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete category?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <!-- Not found message -->
        <p id="noCategoriesMessage" style="display:none;">No records found.</p>
    </div>

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form method="post">
        <div class="modal-header">
            <h5 class="modal-title" id="addCategoryLabel">Add New Category</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Category Name:</label>
                <input type="text" name="category_name" class="form-control" required
                    value="<?= htmlspecialchars($old['category_name'] ?? '') ?>">
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" name="add" class="btn btn-primary">Add Category</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
        </form>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Live search
       document.getElementById('searchInput').addEventListener('keyup', function () {
            const input = this.value.toLowerCase();
            const rows = document.querySelectorAll('#categoriesTable tbody tr');
            let anyVisible = false;

            rows.forEach(row => {
                const show = row.innerText.toLowerCase().includes(input);
                row.style.display = show ? '' : 'none';
                if(show) anyVisible = true;
            });

            // Show or hide "No records found"
            const msg = document.getElementById('noCategoriesMessage');
            msg.style.display = anyVisible ? 'none' : 'block';
        });

        // If form validation fails, automatically open modal
        <?php if(!empty($errors) && !empty($old)): ?>
            var addModal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
            addModal.show();
        <?php endif; ?>
    </script>
    </body>
</html>
