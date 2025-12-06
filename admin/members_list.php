<?php
session_start();
require '../includes/db_connect.php';
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){ 
    die("Access denied."); 
}

// Fetch members
$stmt = $pdo->query("SELECT user_id, name, email, role, visit_count 
                    FROM users 
                    WHERE role IN ('member', 'user') 
                    ORDER BY user_id");
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Members List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../adminstyle.css" rel="stylesheet"/>
</head>
<body class="member-list">

<div class="container">
    <h2 class="mb-3 text-primary">Users List</h2>

    <div class="d-flex mb-3 align-items-center">
        <a href="add_member.php" class="btn btn-success me-2">Add Member</a>
        <a href="dashboard.php" class="btn btn-secondary me-3">Back to Dashboard</a>
        <input type="text" id="searchInput" class="form-control w-25 searchInput"  placeholder="Search members">
    </div>


    <table class="table table-bordered table-striped align-middle" id="membersTable">
        <thead class="table-primary">
            <tr>
                <th>User ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Visit Count</th>
                <th style="width:150px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($members): ?>
                <?php foreach($members as $m): ?>
                    <tr>
                        <td><?= htmlspecialchars($m['user_id']); ?></td>
                        <td><?= htmlspecialchars($m['name']); ?></td>
                        <td><?= htmlspecialchars($m['email']); ?></td>
                        <td><?= htmlspecialchars($m['role']); ?></td>
                        <td><?= intval($m['visit_count']); ?></td>
                        <td>
                            <a href="edit_member.php?user_id=<?= $m['user_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete_member.php?user_id=<?= $m['user_id']; ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Delete this member?');">
                               Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center text-muted">No members found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <!-- Not found message -->
    <p id="noMembersMessage" style="display:none;">No records found.</p>
</div>

<!-- Live Search-->
<script>
    document.getElementById('searchInput').addEventListener('keyup', function () {
        let input = this.value.toLowerCase();
        let rows = document.querySelectorAll('#membersTable tbody tr');
        let anyVisible = false;

        rows.forEach(row => {
            let text = row.innerText.toLowerCase();
            const show = text.includes(input);
            row.style.display = show ? "" : "none";
            if(show) anyVisible = true;
        });

        const msg = document.getElementById('noMembersMessage');
        msg.style.display = anyVisible ? "none" : "block";
    });
</script>

</body>
</html>
