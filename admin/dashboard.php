<?php
session_start();
require '../includes/db_connect.php';
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    die("Access denied.");
}

// Count totals for dashboard
try{
    $total_books = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
    $total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role IN ('member', 'user')")->fetchColumn();
    $total_issued = $pdo->query("SELECT COUNT(*) FROM issued_books WHERE status='issued'")->fetchColumn();
    $total_fines = $pdo->query("SELECT COUNT(*) FROM fines WHERE paid_status='unpaid'")->fetchColumn();
}catch(PDOException $e){
    die("Error fetching dashboard data: ".$e->getMessage());
}

require '../includes/header.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../adminstyle.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="p-4 bg-light admin-dashboard">
<div class="container">

    <div class="row g-3">
        <div class="col-md-4">
            <a href="books_list.php" class="card-link admin-dashboard-card">
                <div class="card p-3 bg-white shadow-sm text-center">
                    <h5>Total Books</h5>
                    <h3 class="text-primary"><?php echo $total_books; ?></h3>
                    <small>Manage Books</small>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="members_list.php" class="card-link admin-dashboard-card">
                <div class="card p-3 bg-white shadow-sm text-center">
                    <h5>Total Users</h5>
                    <h3 class="text-success"><?php echo $total_users; ?></h3>
                    <small>Manage Users</small>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="issued_books.php" class="card-link admin-dashboard-card">
                <div class="card p-3 bg-white shadow-sm text-center">
                    <h5>Issued Books</h5>
                    <h3 class="text-warning"><?php echo $total_issued; ?></h3>
                    <small>View Issued Books</small>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="categories.php" class="card-link admin-dashboard-card">
                <div class="card p-3 bg-white shadow-sm text-center">
                    <h5>View category</h5>
                    <h3 class="text-info"><i class="bi bi-book"></i></h3>
                    <small>Manage category</small>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="fines.php" class="card-link admin-dashboard-card">
                <div class="card p-3 bg-white shadow-sm text-center">
                    <h5>Unpaid Fines</h5>
                    <h3 class="text-danger"><?php echo $total_fines; ?></h3>
                    <small>View Fines</small>
                </div>
            </a>
        </div>
    </div>
</div>
</body>
</html>
