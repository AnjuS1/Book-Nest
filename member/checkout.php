<?php
    session_start();
    require '../includes/db_connect.php';

    if (!isset($_SESSION['user_id'])) {
        header("Location: ../index.php");
        exit;
    }
    if (empty($_SESSION['cart'])) {
        $_SESSION['flash'] = "Your cart is empty.";
        header("Location: cart.php");
        exit;
    }
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("SELECT member_id FROM members WHERE user_id=:uid");
    $stmt->execute([':uid'=>$user_id]);
    $member = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$member) {
        $stmt = $pdo->prepare("INSERT INTO members (user_id, name, email, join_date)
                            SELECT user_id, name, email, NOW() FROM users WHERE user_id=:uid");
        $stmt->execute([':uid'=>$user_id]);
        $member_id = $pdo->lastInsertId();
    } else {
        $member_id = $member['member_id'];
    }

    foreach ($_SESSION['cart'] as $book) {
        $book_id = $book['book_id'];

        // Check availability
        $stmt = $pdo->prepare("SELECT available FROM books WHERE book_id=:bid");
        $stmt->execute([':bid'=>$book_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && $row['available'] > 0) {
            // Issue the book
            $stmt = $pdo->prepare("INSERT INTO issued_books (member_id, book_id, issue_date, due_date, status)
                                VALUES (:member_id, :book_id, NOW(), DATE_ADD(NOW(), INTERVAL 7 DAY), 'Issued')");
            $stmt->execute([
                ':member_id' => $member_id,
                ':book_id' => $book_id
            ]);

            // Decrease availability
            $stmt = $pdo->prepare("UPDATE books SET available = available - 1 WHERE book_id=:bid");
            $stmt->execute([':bid'=>$book_id]);
        }
    }

    // Clear cart
    unset($_SESSION['cart']);
    $_SESSION['flash'] = "Books checked out successfully!";
    header("Location: cart.php");
    exit;
?>
