<?php
    session_start();
    require '../includes/db_connect.php';

    if (!isset($_SESSION['user_id'])) {
        header("Location: ../index.php");
        exit;
    }

    if (!isset($_GET['book_id']) || !is_numeric($_GET['book_id'])) {
        die("Invalid book ID.");
    }

    $book_id = intval($_GET['book_id']);
    $stmt = $pdo->prepare("SELECT * FROM books WHERE book_id = :bid");
    $stmt->execute([':bid' => $book_id]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$book) {
        $_SESSION['flash'] = "Book not found.";
    } elseif ($book['available'] <= 0) {
        $_SESSION['flash'] = "Sorry, this book is currently unavailable.";
    } else {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        if (isset($_SESSION['cart'][$book_id])) {
            $_SESSION['flash'] = "This book is already in your cart.";
        } else {
            $_SESSION['cart'][$book_id] = [
                'book_id' => $book_id,
                'title' => $book['title'],
                'author' => $book['author'],
                'category' => $book['category'],
                'publisher' => $book['publisher']
            ];
            $_SESSION['flash'] = "Book added to cart successfully!";
        }
    }
    header("Location: cart.php");
    exit;
?>
