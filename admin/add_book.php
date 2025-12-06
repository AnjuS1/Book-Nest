<?php
session_start();
require '../includes/db_connect.php';

$input = [
    'title'=>'', 'author'=>'', 'category'=>'', 'publisher'=>'', 
    'isbn'=>'', 'description'=>'', 'quantity'=>''
];
$msg = '';
$alertClass = '';

$categories = $pdo->query("SELECT category_id, category_name FROM categories")->fetchAll(PDO::FETCH_ASSOC);

// Handle POST request
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])){

    foreach($input as $key => $val){
        $input[$key] = trim($_POST[$key] ?? '');
    }
    $input['quantity'] = (int)$input['quantity'];
    $available = $input['quantity'];
    $added_date = date('Y-m-d');

    // Validation
    if(empty($input['title']) || empty($input['author']) || empty($input['category']) || empty($input['publisher']) || empty($input['isbn'])){
        $msg = "Please fill in all required fields.";
        $alertClass = 'alert-danger';
    } elseif(!preg_match('/^\d{13}$/', $input['isbn'])){
        $msg = "ISBN must be exactly 13 digits.";
        $alertClass = 'alert-danger';
    } else {

        $default_image = 'dragon.jpg';
        $image_name = $default_image;

        if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK){
            $uploaded_name = basename($_FILES['image']['name']);
            $target_dir = "../Images/";
            if(!is_dir($target_dir)) mkdir($target_dir, 0755, true);
            if(move_uploaded_file($_FILES['image']['tmp_name'], $target_dir.$uploaded_name)){
                $image_name = $uploaded_name;
            }
        }


        // Insert into database if no errors
        if(!$msg){
            try{
                $stmt = $pdo->prepare("INSERT INTO books 
                    (title, author, category, publisher, isbn, description, quantity, available, added_date, image) 
                    VALUES 
                    (:title, :author, :category, :publisher, :isbn, :description, :quantity, :available, :added_date, :image)");

                $stmt->execute([
                    ':title' => $input['title'],
                    ':author' => $input['author'],
                    ':category' => $input['category'],
                    ':publisher' => $input['publisher'],
                    ':isbn' => $input['isbn'],
                    ':description' => $input['description'],
                    ':quantity' => $input['quantity'],
                    ':available' => $available,
                    ':added_date' => $added_date,
                    ':image' => $image_name
                ]);

                $_SESSION['success'] = "Book added successfully.";
                header("Location: add_book.php");
                exit;

            } catch(PDOException $e){
                $msg = "Database error: ".$e->getMessage();
                $alertClass = 'alert-danger';
            }
        }
    }
}

// Retrieve success message after redirect
if(isset($_SESSION['success'])){
    $msg = $_SESSION['success'];
    $alertClass = 'alert-success';
    unset($_SESSION['success']);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add New Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-3">
<div class="container">
    <div class="card p-4">
        <h2 class="mb-4 text-center">Add New Book</h2>

        <?php if($msg): ?>
            <div class="alert <?= $alertClass ?>"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Title:</label>
                <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($input['title']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Author:</label>
                <input type="text" name="author" class="form-control" required value="<?= htmlspecialchars($input['author']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Category:</label>
                <select name="category" class="form-select" required>
                    <option value="" disabled <?= !$input['category']?'selected':'' ?>>Select Category</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= $cat['category_id'] ?>" <?= $input['category']==$cat['category_id']?'selected':'' ?>>
                            <?= htmlspecialchars($cat['category_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Publisher:</label>
                <input type="text" name="publisher" class="form-control" required value="<?= htmlspecialchars($input['publisher']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">ISBN:</label>
                <input type="text" name="isbn" class="form-control" maxlength="13" required value="<?= htmlspecialchars($input['isbn']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Description:</label>
                <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($input['description']) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Quantity:</label>
                <input type="number" name="quantity" class="form-control" min="1" required value="<?= htmlspecialchars($input['quantity']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Image:</label>
                <input type="file" name="image" accept="image/*">
            </div>

            <div class="d-flex justify-content-between">
                <a href="books_list.php" class="btn btn-secondary">Back to Books List</a>
                <input type="submit" name="add" value="Add Book" class="btn btn-primary">
            </div>
        </form>
    </div>
</div>

<script>
    const isbnInput = document.querySelector('input[name="isbn"]');
    isbnInput.addEventListener('input', function(){
        this.value = this.value.replace(/\D/g,'').slice(0,13);
    });
</script>
</body>
</html>
