<?php
    session_start();
    require '../includes/db_connect.php';

    if (!isset($_SESSION['user_id'])) {
        header("Location: ../auth/login.php");
        exit;
    }
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = :uid");
    $stmt->execute([':uid' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        die("User not found.");
    }
    $stmt = $pdo->prepare("SELECT * FROM members WHERE user_id = :uid");
    $stmt->execute([':uid' => $user_id]);
    $member = $stmt->fetch(PDO::FETCH_ASSOC);
    $is_member = $member ? true : false;
    require '../includes/header.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Member Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">
    <style>
        .disabled-link {
            pointer-events: none;
            opacity: 0.6;
        }
    </style>
</head>
<body class="p-4 bg-light">
    <div class="container-dashboard">
        <h2 class="mb-4">Welcome, <?= htmlspecialchars($user['name']); ?></h2>
        <!-- Cart Icon -->
        <a href="cart.php" class="cart-icon" title="View Cart">
            <i class="fas fa-shopping-cart"></i>
            <?php if (!empty($_SESSION['cart'])): ?>
                <span class="badge bg-danger rounded-circle"
                      style="position: absolute; top: -10px; right: -10px; font-size: 0.7rem;">
                    <?= count($_SESSION['cart']); ?>
                </span>
            <?php endif; ?>
        </a>

        <!-- Membership Notice -->
        <?php if (!$is_member): ?>
            <div class="alert alert-info">
                <strong>Note:</strong> You are currently a regular user.
                To access all library features, please become a library member.
            </div>
            <a href="become_member.php" class="btn btn-success mb-4">Become a Library Member</a>
        <?php endif; ?>

        <!-- Dashboard Menu -->
        <div class="card p-3 shadow-sm">
            <h4 class="mb-3">Dashboard Menu</h4>
            <ul class="list-group">
                <li class="list-group-item <?= !$is_member ? 'disabled-link' : '' ?>">
                    <a href="<?= $is_member ? 'my_books.php' : '#' ?>">My Issued Books</a>
                </li>
                <li class="list-group-item">
                    <a href="../search_books.php">Search Books</a>
                </li>
                <li class="list-group-item <?= !$is_member ? 'disabled-link' : '' ?>">
                    <a href="<?= $is_member ? 'view_fines.php' : '#' ?>">View Fines</a>
                </li>
                <li class="list-group-item">
                    <a href="cart.php">My Cart</a>
                </li>
            </ul>
        </div>
    </div>
</body>
</html>

