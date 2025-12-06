<?php
session_start();
require '../includes/db_connect.php';

$cart = $_SESSION['cart'] ?? [];

// Handle delete using PRG
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove'])) {
    $book_id = (int)$_POST['remove'];
    if (isset($_SESSION['cart'][$book_id])) {
        unset($_SESSION['cart'][$book_id]);
        $_SESSION['flash'] = "Book removed from cart.";
    }
    header("Location: cart.php");
    exit;
}

// Fetch categories
$categories = [];
$stmt = $pdo->query("SELECT category_id, category_name FROM categories");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $categories[$row['category_id']] = $row['category_name'];
}

// Flash message
$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html>
    <head>
        <title>My Cart</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="p-3 bg-light">
        <div class="container">
            <h2 class="mb-4">🛒 My Cart</h2>

            <?php if($flash): ?>
                <div class="alert alert-info"><?= htmlspecialchars($flash) ?></div>
            <?php endif; ?>

            <?php if(!empty($cart)): ?>
                <table class="table table-bordered table-striped mt-3">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Category</th>
                            <th>Publisher</th>
                            <th>Quantity</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($cart as $book_id => $book): 
                            $title = $book['title'] ?? 'N/A';
                            $author = $book['author'] ?? 'N/A';
                            $publisher = $book['publisher'] ?? 'N/A';
                            $category = $categories[$book['category']] ?? 'N/A';
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($title); ?></td>
                            <td><?= htmlspecialchars($author); ?></td>
                            <td><?= htmlspecialchars($category); ?></td>
                            <td><?= htmlspecialchars($publisher); ?></td>
                            <td>1</td>
                            <td>
                                <form method="post" onsubmit="return confirm('Are you sure?');">
                                    <input type="hidden" name="remove" value="<?= $book_id ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="d-flex gap-2 mt-3">
                    <form action="checkout.php" method="post">
                        <button type="submit" class="btn btn-success">Checkout</button>
                    </form>
                    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                    <a href="my_books.php" class="btn btn-primary">View Issued Books</a>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">Your cart is empty 🛒</div>
                <div class="d-flex gap-2">
                    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                    <a href="my_books.php" class="btn btn-primary">Issued Books</a>
                </div>
            <?php endif; ?>
        </div>
    </body>
</html>