<?php
session_start();
require 'includes/db_connect.php';

if(!isset($_GET['book_id'])) {
    die("Invalid book ID");
}

$book_id = $_GET['book_id'];

// Fetch book with category name
$stmt = $pdo->prepare("
    SELECT b.*, c.category_name 
    FROM books b
    LEFT JOIN categories c ON b.category = c.category_id
    WHERE b.book_id = :bid
");
$stmt->execute([':bid' => $book_id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$book){
    die("Book not found");
}

// Determine role
$role = $_SESSION['role'] ?? 'member';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($book['title']); ?> - Book Details</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.card-book {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.book-cover {
    max-width: 220px;
    width: 100%;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}
.book-details {
    flex: 1;
}
.book-details h2 {
    margin-bottom: 10px;
}
.book-details p {
    margin-bottom: 6px;
    font-size: 0.95rem;
}
.btn-group-custom {
    margin-top: 15px;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}
</style>
</head>
<body class="p-4 bg-light">
<div class="container">

<a href="<?= $role === 'admin' ? 'admin/books_list.php' : 'search_books.php' ?>" class="btn btn-secondary mb-3">← Back</a>

<div class="card-book">
    <div class="col-md-3">
        <?php if(!empty($book['image'])): ?>
            <img src="Images/<?= htmlspecialchars($book['image']); ?>" class="book-cover img-fluid">
        <?php endif; ?>
    </div>

    <div class="book-details">
        <h2><?= htmlspecialchars($book['title']); ?></h2>
        <h5 class="text-muted"><?= htmlspecialchars($book['author']); ?></h5>
        <p><strong>Category:</strong> <?= htmlspecialchars($book['category_name'] ?? 'Unknown'); ?></p>
        <p><strong>Publisher:</strong> <?= htmlspecialchars($book['publisher']); ?></p>
        <p><strong>ISBN:</strong> <?= htmlspecialchars($book['isbn']); ?></p>
        <p><strong>Available:</strong> 
            <?php if($book['available'] > 0): ?>
                <span><?= $book['available']; ?></span>
            <?php else: ?>
                <span class="text-danger">Unavailable</span>
            <?php endif; ?>
        </p>

        <div class="btn-group-custom">
        <?php if($role === 'member'): ?>
            <form action="member/cart.php" method="post" class="d-inline">
                <input type="hidden" name="book_id" value="<?= $book['book_id']; ?>">
                <?php if($book['available'] > 0): ?>
                    <button type="submit" name="add_cart" class="btn btn-primary">Add to Cart</button>
                <?php else: ?>
                    <button type="button" class="btn btn-secondary" disabled>Currently Unavailable</button>
                <?php endif; ?>
            </form>
        <?php elseif($role === 'admin'): ?>
            <a href="admin/edit_book.php?book_id=<?= $book['book_id']; ?>" class="btn btn-warning">Edit Book</a>
            <a href="admin/delete_book.php?book_id=<?= $book['book_id']; ?>" class="btn btn-danger" 
               onclick="return confirm('Are you sure you want to delete this book?');">Delete Book</a>
        <?php endif; ?>
        </div>
    </div>
</div>

</div>
</body>
</html>
