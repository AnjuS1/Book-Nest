<?php
require '../includes/db_connect.php';
$search = $_GET['search'] ?? '';

$stmt = $pdo->prepare("
    SELECT * FROM books
    WHERE title LIKE :q1
       OR author LIKE :q2
       OR category LIKE :q3
       OR publisher LIKE :q4
    ORDER BY added_date
");

$stmt->execute([
    ':q1' => "%$search%",
    ':q2' => "%$search%",
    ':q3' => "%$search%",
    ':q4' => "%$search%"
]);

$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Books List</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="../adminstyle.css" rel="stylesheet"/>
    </head>

    <body class="p-3 bg-light">
    <div class="container">
        <h2>Books List</h2>
        <div class="d-flex mb-3 align-items-center justify-content-between">
            <div>
                <a href="add_book.php" class="btn btn-success me-2">Add New Book</a>
                <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
            <div class="ms-auto">
                <input type="text" id="searchInput" class="form-control"style="width: 250px;"placeholder="Search books..."value="<?= htmlspecialchars($search) ?>">
            </div>
        </div>
        <table id="booksTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Book ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>Publisher</th>
                    <th>Available</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($books)): ?>
                    <tr><td colspan="7" class="text-center text-muted">No books found</td></tr>
                <?php else: ?>
                    <?php foreach($books as $book): ?>
                    <tr>
                        <td><?= htmlspecialchars($book['book_id']); ?></td>
                        <td><?= htmlspecialchars($book['title']); ?></td>
                        <td><?= htmlspecialchars($book['author']); ?></td>
                        <td><?= htmlspecialchars($book['category']); ?></td>
                        <td><?= htmlspecialchars($book['publisher']); ?></td>
                        <td><?= htmlspecialchars($book['available']); ?></td>
                        <td>
                            <a href="../view_book.php?book_id=<?= $book['book_id']; ?>" class="btn btn-info btn-sm">View</a>
                            <a href="edit_book.php?book_id=<?= $book['book_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete_book.php?book_id=<?= $book['book_id']; ?>" 
                            class="btn btn-danger btn-sm"
                            onclick="return confirm('Delete this book?');">
                            Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <p id="noBooksMessage" style="display:none;">No records found.</p>
    </div>
    <script>
        document.getElementById('searchInput').addEventListener('input', function () {
            const input = this.value.toLowerCase();
            const rows = document.querySelectorAll('#booksTable tbody tr');
            let anyVisible = false;

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                const show = text.includes(input);
                row.style.display = show ? "" : "none";
                if(show) anyVisible = true;
            });

            // Show/hide "No records found"
            const msg = document.getElementById('noBooksMessage');
            msg.style.display = anyVisible ? "none" : "block";
        });
    </script>

    </body>
</html>
