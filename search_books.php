<?php
session_start();
require 'includes/db_connect.php';

if(!isset($_SESSION['user_id'])){
    die("Access denied.");
}

// Check if user is a member
$stmt = $pdo->prepare("SELECT * FROM members WHERE user_id = :uid");
$stmt->execute([':uid' => $_SESSION['user_id']]);
$member = $stmt->fetch(PDO::FETCH_ASSOC);
$is_member = $member ? true : false;

// Fetch all books initially
$stmt = $pdo->query("SELECT * FROM books ORDER BY title");
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

require 'includes/header.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Search Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <style>
        .book-card img { height: 300px; padding: 15px; }
        .cart-icon { top: 0px; right: 0px; }
    </style>
</head>
<body class="p-3 bg-light">

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Books List</h2>
        <div class="d-flex align-items-center gap-2">
            <a href="./member/dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            <a href="./member/cart.php" class="cart-icon position-relative">
                <i class="fas fa-shopping-cart fa-lg"></i>
                <?php if (!empty($_SESSION['cart'])): ?>
                    <span class="badge bg-danger rounded-circle position-absolute" 
                        style="top:-10px; right:-10px; font-size:0.7rem;">
                        <?= count($_SESSION['cart']); ?>
                    </span>
                <?php endif; ?>
            </a>
        </div>
    </div>

    <!-- Search Input -->
    <div class="mb-4">
        <input type="text" id="searchInput" class="form-control" placeholder="Search by Title, Author, Category, Description, Publisher, ISBN">
    </div>

    <div id="booksContainer" class="row row-cols-1 row-cols-md-5 g-4">
        <?php if(count($books) === 0): ?>
            <p>No books found.</p>
        <?php else: ?>
            <?php foreach($books as $book): 
                $imagePath = $book['image'] ? 'Images/'.$book['image'] : 'Images/dragon.jpg';
                $disableAdd = ($book['available'] == 0) ? 'disabled aria-disabled="true"' : '';
            ?>
            <div class="col">
                <div class="card h-100 book-card d-flex flex-column"
                     data-title="<?= strtolower($book['title']) ?>"
                     data-author="<?= strtolower($book['author']) ?>"
                     data-category="<?= strtolower($book['category']) ?>"
                     data-description="<?= strtolower($book['description']) ?>"
                     data-publisher="<?= strtolower($book['publisher']) ?>"
                     data-isbn="<?= strtolower($book['isbn']) ?>">
                    <img src="<?= $imagePath ?>" class="card-img-top" alt="<?= htmlspecialchars($book['title']) ?>">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="card-title"><?= htmlspecialchars($book['title']) ?></h5>
                            <p class="card-text">By <?= htmlspecialchars($book['author']) ?></p>
                            <p class="card-text"><strong>Available:</strong> <?= $book['available'] ?></p>
                        </div>
                        <div class="mt-3 d-flex justify-content-between">
                            <a href="view_book.php?book_id=<?= $book['book_id'] ?>" class="btn btn-primary btn-sm">View Details</a>
                            <?php if($is_member): ?>
                                <a href="member/add_cart.php?book_id=<?= $book['book_id'] ?>" 
                                   class="btn btn-success btn-sm <?= $disableAdd ?>">Add to Cart</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
document.getElementById('searchInput').addEventListener('input', function () {
    const input = this.value.toLowerCase();
    const cards = document.querySelectorAll('#booksContainer .book-card');
    let anyVisible = false;

    cards.forEach(card => {
        const title = card.getAttribute('data-title');
        const author = card.getAttribute('data-author');
        const category = card.getAttribute('data-category');
        const description = card.getAttribute('data-description');
        const publisher = card.getAttribute('data-publisher');
        const isbn = card.getAttribute('data-isbn');

        const match = title.includes(input) ||
                      author.includes(input) ||
                      category.includes(input) ||
                      description.includes(input) ||
                      publisher.includes(input) ||
                      isbn.includes(input);

        card.closest('.col').style.display = match ? "" : "none";
        if(match) anyVisible = true;
    });

    // Show "No books found" if nothing matches
    let msg = document.getElementById('noBooksMessage');
    if(!anyVisible){
        if(!msg){
            msg = document.createElement('p');
            msg.id = 'noBooksMessage';
            msg.innerText = 'No books found.';
            document.getElementById('booksContainer').appendChild(msg);
        }
    } else if(msg){
        msg.remove();
    }
});
</script>
</body>
</html>
