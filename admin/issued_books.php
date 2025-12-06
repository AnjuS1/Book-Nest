<?php
session_start();
require '../includes/db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Access denied.");
}

// Fetch issued books with username
$stmt = $pdo->query("
    SELECT 
        i.issue_id,
        u.user_id,
        u.name AS member_name,
        u.email AS member_email,
        m.member_id,
        m.phone,
        m.address,
        b.book_id,
        b.title AS book_title,
        i.issue_date,
        i.due_date,
        i.return_date,
        i.status
    FROM issued_books i
    JOIN members m ON i.member_id = m.member_id
    JOIN users u ON m.user_id = u.user_id
    JOIN books b ON i.book_id = b.book_id
");


$issued_books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Issued Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../adminstyle.css" rel="stylesheet"/>
    <style>
        .status {
            padding: 4px 8px;
            border-radius: 5px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .status.issued { background: #ffc107; color: #000; }
        .status.returned { background: #28a745; color: #fff; }
        .status.overdue { background: #dc3545; color: #fff; }
    </style>
</head>

<body class="bg-light p-4">
    <div class="container">
        <h2 class="mb-3">Issued Books</h2>
        <div class="d-flex mb-3 align-items-center">
            <a href="dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>
            <input type="text" id="searchInput" class="form-control w-25 searchInput"  placeholder="Search books">
        </div>
        <table id="issuedTable" class="table table-bordered table-striped">
            <thead class="table-primary">
                <tr>
                    <th>Member</th>
                    <th>Book</th>
                    <th>Issue Date</th>
                    <th>Due Date</th>
                    <th>Return Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($issued_books as $i): ?>
                <tr>
                    <td><?= htmlspecialchars($i['member_name']); ?></td>
                    <td><?= htmlspecialchars($i['book_title']); ?></td>
                    <td><?= htmlspecialchars($i['issue_date']); ?></td>
                    <td><?= htmlspecialchars($i['due_date']); ?></td>
                    <td><?= $i['return_date'] ? htmlspecialchars($i['return_date']) : '-'; ?></td>
                    <td>
                        <span class="status <?= strtolower($i['status']); ?>">
                            <?= ucfirst($i['status']); ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <!-- Not found message -->
        <p id="noBooksMessage" style="display:none;">No records found.</p>

    </div>
    <!-- Live Search-->
    <script>
        document.getElementById('searchInput').addEventListener('keyup', function () {
            let input = this.value.toLowerCase();
            let rows = document.querySelectorAll('#issuedTable tbody tr');
            let anyVisible = false;

            rows.forEach(row => {
                let text = row.innerText.toLowerCase();
                const show = text.includes(input);
                row.style.display = show ? "" : "none";
                if(show) anyVisible = true;
            });

            const msg = document.getElementById('noBooksMessage');
            msg.style.display = anyVisible ? "none" : "block";
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
