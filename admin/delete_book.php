<?php
    session_start();
    require '../includes/db_connect.php';

    if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
        die("Access denied.");
    }

    if(!isset($_GET['book_id'])) die("Book ID missing.");
    $book_id = (int)$_GET['book_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM books WHERE book_id = :id");
        $stmt->execute([':id' => $book_id]);
        $msg = "Book deleted successfully!";
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1451) {
            $msg = "Cannot delete this book because it is currently issued to a member.";
        } else {
            $msg = "Error deleting book: " . $e->getMessage();
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Delete Book</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="p-4 bg-light">
    <div class="container text-center mt-5">
        <div class="alert alert-info">
            <?= htmlspecialchars($msg) ?>
        </div>
        <a href="books_list.php" class="btn btn-primary">Back to Books List</a>
        <a href="dashboard.php" class="btn btn-secondary">Go to Dashboard</a>
    </div>
    </body>
</html>
