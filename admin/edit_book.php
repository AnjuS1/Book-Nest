<?php
    session_start();
    require '../includes/db_connect.php';

    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
        die("Access denied.");
    }
    if (!isset($_GET['book_id'])) {
        $_SESSION['flash'] = "Book ID missing.";
        header("Location: books_list.php");
        exit;
    }

    $book_id = (int)$_GET['book_id'];
    $stmt = $pdo->prepare("SELECT * FROM books WHERE book_id = :id");
    $stmt->execute([':id' => $book_id]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$book) {
        die("Book not found.");
    }
    $categories = $pdo->query("SELECT category_id, category_name FROM categories")->fetchAll(PDO::FETCH_ASSOC);

    $msg = $_SESSION['msg'] ?? '';
    unset($_SESSION['msg']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
        $title = $_POST['title'];
        $author = $_POST['author'];
        $publisher = $_POST['publisher'];
        $isbn = $_POST['isbn'];
        $quantity = (int)$_POST['quantity'];
        $category_id = (int)$_POST['category'];
        $description = $_POST['description'];

        // --- Handle image upload ---
        $image_name = $book['image'] ?: 'dragon.jpg';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['image']['tmp_name'];
            $fileName = $_FILES['image']['name'];
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif'];
            if (in_array($fileExt, $allowed)) {
                $destPath = '../Images/' . $fileName;
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $image_name = $fileName;
                } else {
                    $_SESSION['msg'] = "Error uploading image.";
                    header("Location: edit_book.php?book_id=$book_id");
                    exit();
                }
            } else {
                $_SESSION['msg'] = "Invalid image type.";
                header("Location: edit_book.php?book_id=$book_id");
                exit();
            }
        }
        if (!preg_match('/^\d{13}$/', $isbn)) {
            $_SESSION['msg'] = "ISBN must be exactly 13 digits.";
            header("Location: edit_book.php?book_id=$book_id");
            exit();
        }

        $available = $book['available'] + ($quantity - $book['quantity']); // adjust available
        $stmt = $pdo->prepare("UPDATE books 
            SET title = :title, 
                author = :author, 
                category = :category, 
                publisher = :publisher, 
                isbn = :isbn, 
                description = :description,
                quantity = :quantity, 
                available = :available,
                image = :image
            WHERE book_id = :id");

        try {
            $stmt->execute([
                ':title' => $title,
                ':author' => $author,
                ':category' => $category_id,
                ':publisher' => $publisher,
                ':isbn' => $isbn,
                ':description' => $description,
                ':quantity' => $quantity,
                ':available' => $available,
                ':image' => $image_name,
                ':id' => $book_id
            ]);
            $_SESSION['msg'] = "Book updated successfully.";

            header("Location: edit_book.php?book_id=$book_id");
            exit();
        } catch (PDOException $e) {
            $_SESSION['msg'] = "Error: " . $e->getMessage();
            header("Location: edit_book.php?book_id=$book_id");
            exit();
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Book</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light p-4">
    <div class="container">
        <div class="card p-4">
            <h2 class="mb-3 text-center">Edit Book</h2>

            <?php if ($msg): ?>
                <div class="alert <?= strpos($msg, 'Error') === 0 ? 'alert-danger' : 'alert-success' ?>">
                    <?= htmlspecialchars($msg) ?>
                </div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Title:</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($book['title']) ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Author:</label>
                    <input type="text" name="author" value="<?= htmlspecialchars($book['author']) ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Category:</label>
                    <select name="category" class="form-select" required>
                        <?php foreach ($categories as $cat): 
                            $selected = ($cat['category_id'] == $book['category']) ? 'selected' : '';
                        ?>
                            <option value="<?= $cat['category_id'] ?>" <?= $selected ?>><?= htmlspecialchars($cat['category_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Publisher:</label>
                    <input type="text" name="publisher" value="<?= htmlspecialchars($book['publisher']) ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">ISBN:</label>
                    <input type="text" name="isbn" value="<?= htmlspecialchars($book['isbn']) ?>" class="form-control" maxlength="13" pattern="\d{13}" title="ISBN must be exactly 13 digits" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description:</label>
                    <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($book['description'] ?? '') ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Quantity:</label>
                    <input type="number" name="quantity" value="<?= $book['quantity'] ?>" class="form-control" min="1" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Book Image:</label>
                    <?php if (!empty($book['image'])): ?>
                        <div class="mb-2">
                            <img src="../Images/<?= htmlspecialchars($book['image']) ?>" alt="Book Image" style="height:100px;">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <small class="text-muted">Leave blank to keep existing image.</small>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="books_list.php" class="btn btn-secondary">Back</a>
                    <input type="submit" name="update" value="Update Book" class="btn btn-primary">
                </div>
            </form>
        </div>
    </div>

    <script>
        // ISBN digits only, max 13
        const isbnInput = document.querySelector('input[name="isbn"]');
        isbnInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').slice(0,13);
        });
    </script>
    </body>
</html>
