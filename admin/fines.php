<?php
session_start();
require '../includes/db_connect.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    die("Access denied.");
}

// Fetch fines
$stmt = $pdo->query("
    SELECT 
        f.fine_id,
        u.name AS member_name,
        b.title AS book_title,
        f.fine_amount,
        f.paid_status,
        f.paid_method,
        f.fine_date,
        i.due_date
    FROM fines f
    JOIN issued_books i ON f.issue_id = i.issue_id
    JOIN members m ON i.member_id = m.member_id
    JOIN users u ON m.user_id = u.user_id
    JOIN books b ON i.book_id = b.book_id
");
$fines = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fines</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="../adminstyle.css" rel="stylesheet"/>
</head>
<body class="p-3 bg-light">
<div class="container">
    <h2>Fines List</h2>
    <div class="d-flex mb-3 align-items-center">
        <a href="add_fine.php" class="btn btn-success me-2">Add Fine</a>
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        <input type="text" id="searchInput" class="form-control w-25 searchInput"  placeholder="Search fines">
    </div>

    <?php if(empty($fines)): ?>
        <div class="alert alert-info">No fines found.</div>
    <?php else: ?>
        <table id="finesTable" class="table table-bordered table-striped">
            <thead class="table-primary">
                <tr>
                    <th>Member</th>
                    <th>Book</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Fine Date</th>
                    <th>Due Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($fines as $f): ?>
                <tr id="fine-<?= $f['fine_id']; ?>">
                    <td><?= htmlspecialchars($f['member_name']); ?></td>
                    <td><?= htmlspecialchars($f['book_title']); ?></td>
                    <td><?= $f['fine_amount']; ?></td>
                    <td id="status-<?= $f['fine_id']; ?>">
                        <?php 
                            if(strtolower($f['paid_status']) == 'unpaid'){
                                echo 'Unpaid';
                            } else {
                                if($f['paid_method'] === 'offline'){
                                    echo 'Paid offline';
                                } elseif($f['paid_method'] === 'online'){
                                    echo 'Paid online';
                                } else {
                                    echo 'Paid';
                                }
                            }
                        ?>
                    </td>
                    <td><?= $f['fine_date']; ?></td>
                    <td><?= htmlspecialchars($f['due_date']); ?></td>
                    <td id="action-<?= $f['fine_id']; ?>">
                        <?php if(strtolower($f['paid_status']) == 'unpaid'): ?>
                            <button class="btn btn-success btn-sm mark-paid" data-id="<?= $f['fine_id']; ?>">Mark Paid</button>
                        <?php else: ?>
                            <?php 
                                if($f['paid_method'] === 'offline'){
                                    echo '<span>Paid offline</span>';
                                } elseif($f['paid_method'] === 'online'){
                                    echo '<span>Paid online</span>';
                                } else {
                                    echo '<span>Paid</span>';
                                }
                            ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button class="btn btn-danger btn-sm delete-fine" data-id="<?= $f['fine_id']; ?>">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <!-- Not found message -->
        <p id="noFinesMessage" style="display:none;">No records found.</p>
    <?php endif; ?>
</div>
<script>
    $(document).ready(function(){
        // Mark Paid (admin action → Paid offline)
        $(".mark-paid").click(function(){
            var fine_id = $(this).data("id");
            var btn = $(this);

            $.post("mark_fine_paid.php", {id: fine_id}, function(data){
                try {
                    var res = JSON.parse(data);
                    if(res.success){
                        // Admin marked fine → Paid offline
                        $("#status-" + fine_id).html('<span>Paid offline</span>');
                        $("#action-" + fine_id).html('<span>Paid offline</span>');
                        btn.remove();
                    } else {
                        alert("Error: " + res.error);
                    }
                } catch(e){
                    alert("Unexpected error.");
                }
            });
        });

        // Delete Fine
        $(".delete-fine").click(function(){
            if(!confirm("Are you sure you want to delete this fine?")) return;
            var fine_id = $(this).data("id");
            var row = $(this).closest("tr");

            $.post("delete_fine.php", {id: fine_id}, function(data){
                row.remove();
                alert("Fine deleted successfully.");
            });
        });

        document.getElementById('searchInput').addEventListener('keyup', function () {
            let input = this.value.toLowerCase();
            let rows = document.querySelectorAll('#finesTable tbody tr');
            let anyVisible = false;

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                const show = text.includes(input);
                row.style.display = show ? "" : "none";
                if(show) anyVisible = true;
            });

            const msg = document.getElementById('noFinesMessage');
            msg.style.display = anyVisible ? "none" : "block";
        });
    });
</script>
</body>
</html>
