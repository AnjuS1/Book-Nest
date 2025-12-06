<?php
    session_start();
    require '../includes/db_connect.php';
    if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
        die("Access denied.");
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $_SESSION['old'] = $_POST;
        $member_id = $_POST['member_id'] ?? '';
        $issue_id = $_POST['issue_id'] ?? '';
        $fine_amount = $_POST['fine_amount'] ?? '';
        $paid_status = $_POST['paid_status'] ?? 'unpaid';
        $remarks = trim($_POST['remarks'] ?? '');
        if ($remarks === '') { $remarks = "No remarks"; }
        $errors = [];
        if (!$member_id || !$issue_id || !$fine_amount) {
            $errors[] = "All fields are required.";
        }
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header("Location: add_fine.php");
            exit;
        }
        $stmt = $pdo->prepare("
            INSERT INTO fines (issue_id, member_id, fine_amount, paid_status, fine_date, remarks)
            VALUES (?, ?, ?, ?, NOW(), ?)
        ");

        if ($stmt->execute([$issue_id, $member_id, $fine_amount, $paid_status, $remarks])) {
            $_SESSION['success'] = "Fine added successfully!";
        } else {
            $_SESSION['errors'] = ["Error adding fine."];
        }

        header("Location: add_fine.php");
        exit;
    }

    // Fetch members with issued book
    $members = $pdo->query("
        SELECT DISTINCT m.member_id, u.name
        FROM members m
        JOIN users u ON m.user_id = u.user_id
        JOIN issued_books i ON i.member_id = m.member_id
        WHERE i.return_date IS NULL
        ORDER BY u.name ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Fetch issued books that are not yet returned
    $issued_books = $pdo->query("
        SELECT i.issue_id, i.member_id, b.title
        FROM issued_books i
        JOIN books b ON i.book_id = b.book_id
        WHERE i.return_date IS NULL
    ")->fetchAll(PDO::FETCH_ASSOC);

    $old     = $_SESSION['old']     ?? [];
    $errors  = $_SESSION['errors']  ?? [];
    $success = $_SESSION['success'] ?? '';

    unset($_SESSION['old'], $_SESSION['errors'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Add Fine</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </head>
    <body class="p-3 bg-light">
    <div class="container">
        <h2>Add Fine</h2>
        <a href="fines.php" class="btn btn-secondary mb-3">Back to Fines</a>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $e): ?>
                    <?= htmlspecialchars($e) ?><br>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Select Member</label>
                <select name="member_id" id="member_id" class="form-select" required>
                    <option value="">-- Select Member --</option>

                    <?php foreach ($members as $m): ?>
                        <option value="<?= $m['member_id'] ?>"
                            <?= ($old['member_id'] ?? '') == $m['member_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Select Book (Issued)</label>
                <select name="issue_id" id="issue_id" class="form-select" required>
                    <option value="">-- Select Book --</option>

                    <?php foreach ($issued_books as $b): ?>
                        <option 
                            value="<?= $b['issue_id'] ?>" 
                            data-member="<?= $b['member_id'] ?>"
                            <?= ($old['issue_id'] ?? '') == $b['issue_id'] ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($b['title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Fine Amount</label>
                <input type="number" class="form-control" 
                    name="fine_amount" required
                    value="<?= htmlspecialchars($old['fine_amount'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Paid Status</label>
                <select name="paid_status" class="form-select">
                    <option value="unpaid" <?= ($old['paid_status'] ?? '') === 'unpaid' ? 'selected' : '' ?>>Unpaid</option>
                    <option value="paid"   <?= ($old['paid_status'] ?? '') === 'paid' ? 'selected' : '' ?>>Paid</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Remarks</label>
                <input type="text" class="form-control" 
                    name="remarks" maxlength="255"
                    value="<?= htmlspecialchars($old['remarks'] ?? '') ?>"
                    placeholder="No remarks">
            </div>

            <input type="submit" class="btn btn-primary" value="Add Fine">

        </form>
    </div>
    <script>
        $(document).ready(function(){
            function filterBooks() {
                var selectedMember = $('#member_id').val();
                $('#issue_id option').each(function() {
                    var memberId = $(this).data('member');
                    if (!memberId || memberId == selectedMember) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }

            $('#member_id').change(filterBooks);
            filterBooks();
        });
    </script>
    </body>
</html>
