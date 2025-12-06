<?php
session_start();
require '../includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    die("Access denied.");
}

// Check if user is a member
$stmt = $pdo->prepare("SELECT * FROM members WHERE user_id=:uid");
$stmt->execute([':uid' => $_SESSION['user_id']]);
$member = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$member) {
    die("You must be a library member to access this page.");
}

$member_id = $member['member_id'];

// Fetch issued books
$stmt = $pdo->prepare("
    SELECT i.issue_id, b.title, i.issue_date, i.due_date, i.return_date, i.status 
    FROM issued_books i 
    JOIN books b ON i.book_id = b.book_id
    WHERE i.member_id = :mid
");
$stmt->execute([':mid' => $member_id]);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

require '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Issued Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">
</head>
<body class="p-4 bg-light">

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Issued Books</h2>
        <div class="d-flex align-items-center gap-2">
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            <a href="cart.php" class="cart-icon" title="View Cart">
                <i class="fas fa-shopping-cart"></i>
                <?php if (!empty($_SESSION['cart'])): ?>
                    <span class="badge bg-danger rounded-circle" style="position: absolute; top: -10px; right: -10px; font-size: 0.7rem;">
                        <?= count($_SESSION['cart']); ?>
                    </span>
                <?php endif; ?>
            </a>
        </div>
    </div>

    <?php if (empty($books)): ?>
        <div class="alert alert-info">You have not issued any books yet.</div>
    <?php else: ?>
        <table class="table table-bordered table-striped shadow-sm">
            <thead class="table">
                <tr>
                    <th>Title</th>
                    <th>Issue Date</th>
                    <th>Due Date</th>
                    <th>Return Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($books as $b): ?>
                <tr>
                    <td><?= htmlspecialchars($b['title']); ?></td>
                    <td><?= htmlspecialchars($b['issue_date']); ?></td>
                    <td><?= htmlspecialchars($b['due_date']); ?></td>
                    <td><?= $b['return_date'] ? htmlspecialchars($b['return_date']) : 'Not Returned'; ?></td>
                    <td>
                        <?php if ($b['status'] == 'Returned'): ?>
                            <span class="badge bg-success">Returned</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark"><?= htmlspecialchars($b['status']); ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                            // Check if an unpaid fine exists 
                            $stmt_fine = $pdo->prepare("SELECT * FROM fines WHERE issue_id=:iid AND paid_status='unpaid'");
                            $stmt_fine->execute([':iid' => $b['issue_id']]);
                            $fine = $stmt_fine->fetch(PDO::FETCH_ASSOC);
                            $disableReturn = ($b['status'] == 'Returned' || $fine);
                        ?>
                        <form action="return_book.php" method="post" class="d-inline">
                            <input type="hidden" name="issue_id" value="<?= $b['issue_id']; ?>">
                            <button type="submit" class="btn btn-sm btn-danger" <?= $disableReturn ? 'disabled' : ''; ?>>
                                Return
                            </button>
                        </form>
                        <a href="view_fines.php?issue_id=<?= $b['issue_id']; ?>" 
                        class="btn btn-sm btn-info <?= !$fine ? 'disabled' : ''; ?>"
                        <?= !$fine ? 'aria-disabled="true" tabindex="-1"' : ''; ?>>
                        View Fine
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php if (isset($_SESSION['flash'])): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($_SESSION['flash']); unset($_SESSION['flash']); ?>
    </div>
<?php endif; ?>


</body>
</html>
