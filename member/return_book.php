<?php
session_start();
require '../includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    die("Access denied.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['issue_id'])) {
    die("Invalid request.");
}

$issue_id = intval($_POST['issue_id']);

// Verify user is a library member
$stmt = $pdo->prepare("SELECT * FROM members WHERE user_id = :uid");
$stmt->execute([':uid' => $_SESSION['user_id']]);
$member = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$member) {
    die("You must be a library member to return a book.");
}

$member_id = $member['member_id'];

// Check if this issued book belongs to the member
$stmt = $pdo->prepare("
    SELECT i.issue_id, i.book_id, i.status, b.available
    FROM issued_books i
    JOIN books b ON i.book_id = b.book_id
    WHERE i.issue_id = :iid AND i.member_id = :mid
");
$stmt->execute([':iid' => $issue_id, ':mid' => $member_id]);
$issued = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$issued) {
    die("No record found or unauthorized action.");
}

if ($issued['status'] === 'returned') {
    $_SESSION['flash'] = "This book has already been returned.";
    header("Location: my_books.php");
    exit;
}

// Update issued_books to mark as returned
$stmt = $pdo->prepare("
    UPDATE issued_books 
    SET status = 'returned', return_date = NOW() 
    WHERE issue_id = :iid
");
$stmt->execute([':iid' => $issue_id]);

// Increment available count in books table
$stmt = $pdo->prepare("UPDATE books SET available = available + 1 WHERE book_id = :bid");
$stmt->execute([':bid' => $issued['book_id']]);

$_SESSION['flash'] = "Book returned successfully.";
header("Location: my_books.php");
exit;
?>
